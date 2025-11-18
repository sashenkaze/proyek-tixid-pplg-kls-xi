<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketPayment;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Schedule;
use Carbon\Carbon;
use App\Models\Promo;

class TicketController extends Controller
{
    public function showSeats($scheduleId, $hourId)
    {
        $schedule = Schedule::where('id', $scheduleId)->with('cinema')->first();
        // jika tidak ditemukan buat default kosong
        $hour = $schedule['hours'][$hourId] ?? '';
        $seats = Ticket::whereHas('ticketPayment', function ($q) {
            // whereDate : mencari data tanggal
            $q->whereDate('paid_date', now()->format('Y-m-d'));
        })->whereTime('hour', $hour)->pluck('row_of_seats');
        // pluck() : mengambil satu field saja, disimpan di array
        // ...$seats :
        $seatsFormat = array_merge(...$seats);
        // dd($seatsFormat);
        return view('schedule.show-seats', compact('schedule', 'hour', 'seatsFormat'));
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $now = Carbon::now();

        $ticketActive = Ticket::whereHas('ticketPayment', function ($q) {
            $q->whereNotNull('paid_date');
        })
            ->where('user_id', auth()->id())
            ->get()
            ->filter(function ($t) use ($now) {
                $playAt = Carbon::parse($t->ticketPayment->booked_date . ' ' . $t->hour);
                return $playAt->gte($now);
            });

        $ticketNonActive = Ticket::whereHas('ticketPayment', function ($q) {
            $q->whereNotNull('paid_date');
        })
            ->where('user_id', auth()->id())
            ->get()
            ->filter(function ($t) use ($now) {
                $playAt = Carbon::parse($t->ticketPayment->booked_date . ' ' . $t->hour);
                return $playAt->lt($now);
            });

        return view('ticket.index', compact('ticketActive', 'ticketNonActive'));
    }


    public function chartData()
    {
        // ambil data bulan sekarang
        $month = now()->format('m');
        $tickets = Ticket::whereHas('TicketPayment', function ($q) use ($month) {
            // whereMonth : mencari berdasarkan bulan
            $q->whereMonth('booked_date', $month)->where('paid_date', '<>', NULL);
        })->get()->groupBy(function ($ticket) {
            // hasil data berdasarkan bulan dan yang sudah dibayar, dikelompokkan (groupBy) berdasarkan tanggal pembelian untuk menghitung dihari itu berapa yg beli tiket
            return Carbon::parse($ticket['ticketPayment']['booked_date'])->format('Y-m-d');
        })->toArray();
        // ambil data key/index (tanggal) untuk data label chartjs
        $labels = array_keys($tickets);
        // membuat array untuk menyimpan data jumlah pembelian tiap tanggal
        $data = [];
        foreach ($tickets as $key => $ticket) {
            // simpan hasil perhitungan count() dari $ticket. $ticket berbentuk array data-data yang dibeli di tanggal tertentu
            array_push($data, count($ticket));
        }
        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
        // dd($tickets);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'schedule_id' => 'required',
            'row_of_seats' => 'required',
            'quantity' => 'required',
            'total_price' => 'required',
            'hour' => 'required',
        ]);

        $createData = Ticket::create([
            'user_id' => $request->user_id,
            'schedule_id' => $request->schedule_id,
            'row_of_seats' => $request->row_of_seats,
            'quantity' => $request->quantity,
            'total_price' => $request->total_price,
            'actived' => 1,
            'service_fee' => 4000 * $request->quantity,
            'hour' => $request->hour,
        ]);
        // hasilnya dikirimkan dalam bentuk response json karena nanti dari sini success nya ditangani oleh ajax js
        return response()->json([
            'message' => 'Berhasil membuat data tiket',
            'data' => $createData
        ]);
    }

    public function ticketOrder($ticketId)
    {
        $ticket = Ticket::where('id', $ticketId)->with(['schedule.cinema', 'schedule.movie'])->first();
        $promos = Promo::where('actived', 1)->get();
        // dd($ticket);
        return view('schedule.order', compact('ticket', 'promos'));
    }

    public function createBarcode(Request $request, $ticketId)
    {
        $barcodeKode = 'TICKET' . $ticketId . rand(1, 10);
        // format() : ekstensi file, size() : ukuran gambar, margin() : margin luar gambar
        $qrimage = QrCode::format('svg')->size(300)->margin(2)->generate($barcodeKode);
        $fileName = $barcodeKode . '.svg';
        $path = 'barcodes/' . $fileName;
        // karena file bukan dari luar (generate), memindahkannya tidak bisa dengan storeAs gunakan Storage::disk
        // pindahkan gambar ke storage public
        Storage::disk('public')->put($path, $qrimage);

        $createData = TicketPayment::create([
            'ticket_id' => $ticketId,
            'barcode' => $path,
            'status' => 'process',
            'booked_date' => now()
        ]);
        //update total_price pada ticket juga menggunakan promo
        if ($request->promo_id != null) {
            $ticket = Ticket::find($ticketId);
            $promo = Promo::find($request->promo_id);
            if ($promo && $promo['type'] == 'percent') {
                $totalPrice = $ticket['total_price'] - ($ticket['total_price'] * $promo['discount'] / 100);
            } else {
                $totalPrice = $ticket['total_price'] - $promo['discount'];
            }
            $ticket->update(['promo_id' => $request->promo_id, 'total_price' => $totalPrice]);
        }
        return response()->json(['message' => 'Berhasil membuat barcode pembayaran', 'data' => $createData]);
    }

    /**
     * Display the specified resource.
     */

    public function paymentPage($ticketId)
    {
        $ticket = Ticket::where('id', $ticketId)->with(['promo', 'ticketPayment'])->first();
        return view('schedule.payment', compact('ticket'));
    }

    public function proofPayment($ticketId)
    {
        $updateData = TicketPayment::where('ticket_id', $ticketId)->update([
            'paid_date' => now()
        ]);
        // arahkan ke halaman tiket struk
        return redirect()->route('tickets.show', $ticketId);
    }
    public function show($ticketId)
    {
        $ticket = Ticket::where('id', $ticketId)->with(['schedule', 'schedule.cinema', 'schedule.movie', 'ticketPayment'])->first();
        return view('schedule.ticket-receipt', compact('ticket'));
    }

    public function exportPdf($ticketId)
    {
        // menentukan data yang akan dikirim ke blade pdf
        // bentuk data harus array tidak collection -> toArray();
        $ticket = Ticket::where('id', $ticketId)->with(['schedule', 'schedule.cinema', 'schedule.movie', 'ticketPayment'])->first()->toArray();
        // menentukan nama alias variabel yg akan di gunakan di blade pdf
        view()->share('ticket', $ticket);
        // menentukan blade yang akan dicetak menjadi pdf dan compact data yg digunakan
        $pdf = Pdf::LoadView('schedule.pdf', $ticket);
        // unduh pdf dengan tema file tertentu
        $filename = 'TICKET' . $ticket['id'] . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        //
    }
}
