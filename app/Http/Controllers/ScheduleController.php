<?php

namespace App\Http\Controllers;

use App\Models\Cinema;
use App\Models\Movie;
use App\Models\Schedule;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ScheduleExport;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cinemas = Cinema::all();
        $movies = Movie::all();

        // with() : mengambil data detail dari relasi, tdak hanya id nya
        // isi di dalam with diambil dari nama fungsi relasi di model
        $schedules = Schedule::with(['cinema', 'movie'])->get();
        return view('staff.schedules.index', compact('cinemas', 'movies', 'schedules'));
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
            'cinema_id' => 'required',
            'movie_id' => 'required',
            'price' => 'required|numeric',
            //validasi item array (.) validasi index ke berapa pun (*)
            'hours.*' => 'required|date_format:H:i',
        ], [
            'cinema_id.required' => 'Bioskop harus dipilih',
            'movie_id.required' => 'Film harus dipilih',
            'price.required' => 'Harga harus diisi',
            'price.numeric' => 'Harga harus diisi dengan angka',
            'hours.*.required' => 'Jam tayang harus diisi minimal satu data',
            'hours.*.date_format' => 'Jam tayang harus diisi dengan jam:menit',
        ]);

        //pengecekan data berdasarkan cinema_id dan movie_id lalu ambil hours nya
        //value('hours) : hanya mengambil hours, ga perlu data lain
        $hours = Schedule::where('cinema_id', $request->cinema_id)->where('movie_id', $request->movie_id)->value('hours');
        //jika data blm ada $hours akan null, agar tetap array gunakan ternary
        //jika $hours ada isinya ambil, kalau null buat array kosong
        $hoursBefore = $hours ?? [];
        // gabungkan hours sebelumnya dengan yg baru ditambahkan
        $mergeHours = array_merge($hoursBefore, $request->hours);
        // hilangkan jam yang duplikat, gunakan array ini untuk db
        $newHours = array_unique($mergeHours);
        //updateOrcreate() : jika cinema_id dan movie_id udah ada di schedule (UPDATE data price & hours) kalau gak ada (CREATE semua)
        $createData = Schedule::updateOrCreate([
            // mencari data
            'cinema_id' => $request->cinema_id,
            'movie_id' => $request->movie_id,
        ], [
            // update ini
            'price' => $request->price,
            'hours' => $newHours,
        ]);
        if ($createData) {
            return redirect()->route('staff.schedules.index')->with('success', 'Berhasil menambahkan data!');
        } else {
            return redirect()->route('staff.schedules.index')->with('error', 'Gagal! Coba lagi');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $schedule = Schedule::where('id', $id)->with(['cinema', 'movie'])->first();
        return view('staff.schedules.edit', compact('schedule'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'price' => 'required|numeric',
            'hours.*' => 'required|date_format:H:i'
        ], [
            'price.required' => 'Harga harus diisi',
            'price.numeric' => 'Harga harus diisi dengan angka',
            'hours.*.required' => 'Jam tayang harus diisi minimal satu data',
            'hours.*.date_format' => 'Jam tayang harus diisi dengan Jam:menit'
        ]);

        $updateData = Schedule::where('id', $id)->update([
            'price' => $request->price,
            'hours' => array_unique($request->hours)
        ]);
        if ($updateData) {
            return redirect()->route('staff.schedules.index')->with('success', 'Berhasil mengubah data!');
        } else {
            return redirect()->back()->with('error', 'Gagal! Coba lagi.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Schedule::where('id', $id)->delete();
        return redirect()->route('staff.schedules.index')->with('success', 'Berhasil menghapus data');
    }

    public function trash()
    {
        // onlyTrashed() : filter data yang sudah di hps, yg deleted_at di phpmyadmin nya ada isi tanggal, hanya filter tetap gunakan get()/first() untuk ambilnya
        $schedules = Schedule::onlyTrashed()->get();
        return view('staff.schedules.trash', compact('schedules'));
    }

    public function restore($id)
    {
        // sebelum dicari, difilter dulu diakses hanya yg sudah diihapus
        $schedule = Schedule::onlyTrashed()->find($id);
        // restore() : mengembalikan data yg sudah dihapus
        $schedule->restore();
        return redirect()->route('staff.schedules.index')->with('success', 'Data berhasil dikembalikan!');
    }

    public function deletePermanent($id)
    {
        $schedule = Schedule::onlyTrashed()->find($id);
        // forceDelete() : menghapus permanen dari db
        $schedule->forceDelete();
        return redirect()->route('staff.schedules.index')->with('success', 'Data berhasil dihapus permanen!');
    }

    public function exportExcel()
    {
        $file_name = 'data-schedule.xlsx';
        return Excel::download(new ScheduleExport, $file_name);
    }

    public function dataForDatatables()
    {
        $schedules = Schedule::with(['cinema', 'movie'])->get();

        return DataTables::of($schedules)
            ->addIndexColumn()
            ->addColumn('cinema_name', function ($schedule) {
                return $schedule->cinema->name;
            })
            ->addColumn('movie_title', function ($schedule) {
                return $schedule->movie->title;
            })
            ->addColumn('price_display', function ($schedule) {
                return 'Rp ' . number_format($schedule->price, 0, ',', '.');
            })
            ->addColumn('hours_display', function ($schedule) { 
                $hours = is_array($schedule->hours) ? $schedule->hours : json_decode($schedule->hours, true);
                if (!$hours)
                    return '-';

                $list = '<ul class="m-0 p-0" style="list-style:none;">';
                foreach ($hours as $hour) {
                    $list .= "<li>{$hour}</li>";
                }
                $list .= '</ul>';
                return $list;
            })
            ->addColumn('buttons', function ($schedule) {
                $btnEdit = '<a href="' . route('staff.schedules.edit', $schedule->id) . '" class="btn btn-primary me-1">Edit</a>';

                $btnDelete = '<form method="POST" action="' . route('staff.schedules.destroy', $schedule->id) . '" class="d-inline">'
                    . csrf_field()
                    . method_field('DELETE')
                    . '<button type="submit" class="btn btn-danger" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\')">Hapus</button>'
                    . '</form>';

                return '<div class="d-flex justify-content-center gap-2">' . $btnEdit . $btnDelete . '</div>';
            })
            ->rawColumns(['cinema_name', 'movie_title', 'price_display', 'hours_display', 'buttons'])
            ->make(true);
    }

}
