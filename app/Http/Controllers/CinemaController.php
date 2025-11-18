<?php

namespace App\Http\Controllers;

use App\Models\Cinema;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CinemaExport;
use App\Models\Schedule;
use Yajra\DataTables\Facades\DataTables;

class CinemaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //ambil data dari model
        //all() = select * from table/model cinemas
        $cinemas = Cinema::all();
        //mengirim data ke blade -> compact('namavariabel')
        return view('admin.cinemas.index', compact('cinemas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.cinemas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'location' => 'required|min:10'
        ], [
            'name.required' => 'Nama bioskop harus diisi',
            'location.required' => 'Alamat bioskop harus diisi',
            'location.min' => 'Alamat bioskop minimal 10 karakter'
        ]);
        $createData = Cinema::create([
            'name' => $request->name,
            'location' => $request->location,
        ]);
        if ($createData) {
            return redirect()->route('admin.cinemas.index');
        } else {
            return redirect()->back()->with('error', 'Gagal! Silakan coba lagi');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Cinema $cinema)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //($id) = ngambil data dari {id} routenya
        //find($id) = mencari data cinema yang id nya sesuai dengan isi dari $id
        $cinema = Cinema::find($id);
        return view('admin.cinemas.edit', compact('cinema'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cinema $cinema, $id)
    {
        $request->validate([
            'name' => 'required|min:3',
            'location' => 'required|min:10'
        ], [
            'name.required' => 'Nama bioskop harus diisi',
            'name.min' => 'Nama bioskop minimal 3 karakter',
            'location.required' => 'Alamat bioskop harus diisi',
            'location.min' => 'Alamat bioskop minimal 10 karakter'
        ]);
        //kirim data
        $updateData = Cinema::where('id', $id)->update([
            'name' => $request->name,
            'location' => $request->location,
        ]);
        //perpindahan halaman
        if ($updateData) {
            return redirect()->route('admin.cinemas.index')->with('success', 'Berhasil mengubah data!');
        } else {
            return redirect()->back()->with('failed', 'Gagal! Silakan coba lagi');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //hapus tabel/data cinema per id dari db dan web
        $cinema = Cinema::findOrFail($id); //cari data berdasarkan id
        $cinema->delete(); //hapus dari db

        return redirect()->route('admin.cinemas.index')->with('success', 'Data Berhasil dihapus!');
    }

    public function exportExcel()
    {
        $file_name = 'data-bioskop.xlsx';
        return Excel::download(new CinemaExport, $file_name);
    }

    public function trash()
    {
        // onlyTrashed() : filter data yang sudah di hps, yg deleted_at di phpmyadmin nya ada isi tanggal, hanya filter tetap gunakan get()/first() untuk ambilnya
        $cinemas = Cinema::onlyTrashed()->get();
        return view('admin.cinemas.trash', compact('cinemas'));
    }

    public function restore($id)
    {
        // sebelum dicari, difilter dulu diakses hanya yg sudah diihapus
        $cinema = Cinema::onlyTrashed()->find($id);
        // restore() : mengembalikan data yg sudah dihapus
        $cinema->restore();
        return redirect()->route('admin.cinemas.index')->with('success', 'Data berhasil dikembalikan!');
    }

    public function deletePermanent($id)
    {
        $cinema = Cinema::onlyTrashed()->find($id);
        // forceDelete() : menghapus permanen dari db
        $cinema->forceDelete();
        return redirect()->route('admin.cinemas.index')->with('success', 'Data berhasil dihapus permanen!');
    }

    public function dataForDatatables()
    {
        $cinemas = Cinema::query();
        return DataTables::of($cinemas)->addIndexColumn()
            ->addColumn('buttons', function ($cinema) {
                $btnEdit = '<a href="' . route('admin.cinemas.edit', $cinema['id']) . '" class="btn btn-primary me-2">Edit</a>';
                $btnDelete = '<form action="' . route('admin.cinemas.destroy', $cinema['id']) . '" method="POST" class="m-0">' .
                    csrf_field() .
                    method_field('DELETE') .
                    '<button type="submit" class="btn btn-danger me-2">Hapus</button>
                        </form>';
                return '
                    <div class="d-flex flex-column align-items-center">
                        <div class="d-flex gap-2 mb-2" style="width: 100%; max-width: 250px;">
                            ' . $btnDelete . $btnEdit . '
                        </div>
                    </div>
        ';
            })
            ->rawColumns(['buttons'])
            ->make(true);
    }

    public function listCinema()
    {
        $cinemas = Cinema::all();
        return view('schedule.cinemas', compact('cinemas'));
    }

    public function cinemaSchedule($cinema_id)
    {
        // whereHas : argumen (nama relasi) 1 wajib, argumen 2 () opsional
        $schedules = Schedule::where('cinema_id', $cinema_id)->with('movie')->whereHas('movie', function($q) {
            $q->where('actived', 1);
        })->get();
        return view('schedule.cinema-schedule', compact('schedules'));
    }
}

