<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PromoExport;
use Str;
use Yajra\DataTables\Facades\DataTables;

class PromoController extends Controller
{
    public function index()
    {
        $promos = Promo::all();
        return view('staff.promo.index', compact('promos'));
    }

    public function create()
    {
        return view('staff.promo.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'promo_code' => 'required|unique:promos,promo_code',
            'type' => 'required|in:percent,rupiah',
            'discount' => [
                'required',
                'numeric',
                'min:1',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->type === 'percent' && $value > 100) {
                        $fail('Diskon persentase tidak boleh lebih dari 100%.');
                    }
                    if ($request->type === 'rupiah' && $value < 1000) {
                        $fail('Diskon rupiah minimal Rp 1000.');
                    }
                },
            ],
        ]);

        $createData = Promo::create([
            'promo_code' => $request->promo_code,
            'type' => $request->type,
            'discount' => $request->discount,
            'actived' => 1, // default aktif
        ]);

        if ($createData) {
            return redirect()->route('staff.promos.index')->with('success', 'Berhasil menambahkan data!');
        } else {
            return redirect()->back()->with('error', 'Gagal! Silakan coba lagi');
        }
    }

    public function edit($id)
    {
        $promo = Promo::findOrFail($id);
        return view('staff.promo.edit', compact('promo'));
    }

    public function update(Request $request, $id)
    {
        $promo = Promo::findOrFail($id);

        $request->validate([
            'promo_code' => 'required|unique:promos,promo_code,' . $promo->id,
            'type' => 'required|in:percent,rupiah',
            'discount' => [
                'required',
                'numeric',
                'min:1',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->type === 'percent' && $value > 100) {
                        $fail('Diskon persentase tidak boleh lebih dari 100%.');
                    }
                    if ($request->type === 'rupiah' && $value < 1000) {
                        $fail('Diskon rupiah minimal Rp 1000.');
                    }
                },
            ],
        ]);

        $updateData = Promo::where('id', $id)->update([
            'promo_code' => $request->promo_code,
            'type' => $request->type,
            'discount' => $request->discount,
            'actived' => $request->actived,
        ]);

        if ($updateData) {
            return redirect()->route('staff.promos.index')->with('success', 'Berhasil mengubah data!');
        } else {
            return redirect()->back()->with('error', 'Gagal! Coba lagi.');
        }
    }

    public function destroy($id)
    {
        $promo = Promo::findOrFail($id);
        $promo->delete();

        return redirect()->route('staff.promos.index')->with('success', 'Berhasil menghapus data promo!');
    }

    public function patch($id)
    {
        $promo = Promo::findOrFail($id);
        $promo->update([
            'actived' => 0
        ]);

        return redirect()->route('staff.promos.index')->with('success', 'Promo berhasil dinonaktifkan!');
    }

    public function exportExcel()
    {
        $file_name = 'data-promo.xlsx';
        return Excel::download(new PromoExport, $file_name);
    }

    public function trash()
    {
        // onlyTrashed() : filter data yang sudah di hps, yg deleted_at di phpmyadmin nya ada isi tanggal, hanya filter tetap gunakan get()/first() untuk ambilnya
        $promos = Promo::onlyTrashed()->get();
        return view('staff.promo.trash', compact('promos'));
    }

    public function restore($id)
    {
        // sebelum dicari, difilter dulu diakses hanya yg sudah diihapus
        $promo = Promo::onlyTrashed()->find($id);
        // restore() : mengembalikan data yg sudah dihapus
        $promo->restore();
        return redirect()->route('staff.promos.index')->with('success', 'Data berhasil dikembalikan!');
    }

    public function deletePermanent($id)
    {
        $promo = Promo::onlyTrashed()->find($id);
        // forceDelete() : menghapus permanen dari db
        $promo->forceDelete();
        return redirect()->route('staff.promos.index')->with('success', 'Data berhasil dihapus permanen!');
    }

    public function dataForDatatables()
    {
        $promos = Promo::query()->get();
        return DataTables::of($promos)->addIndexColumn()
            ->addColumn('type', function ($promo) {
                return $promo->type == 'percent' ? '% (Persen)' : 'Rp (Rupiah)';
            })
            ->addColumn('discount', function ($promo) {
                if ($promo->type == 'percent') {
                    return $promo->discount . '%';
                } else {
                    return 'Rp' . number_format($promo->discount, 0, ',', '.');
                }
            })
            ->addColumn('activedBadge', function ($promo) {
                if ($promo->actived == 1) {
                    return '<span class="badge badge-success">Aktif</span>';
                } else {
                    return '<span class="badge badge-secondary">Non-aktif</span>';
                }
            })
            ->addColumn('buttons', function ($promo) {
                $btnEdit = '<a href="' . route('staff.promos.edit', $promo->id) . '" class="btn btn-primary me-2">Edit</a>';
                $btnDelete = '<form action="' . route('staff.promos.destroy', $promo->id) . '" method="POST" class="m-0">' .
                    csrf_field() .
                    method_field('DELETE') .
                    '<button type="submit" class="btn btn-danger me-2">Hapus</button>
                        </form>';
                $btnNonaktif = '';
                if ($promo->actived == 1) {
                    $btnNonaktif = '<form action="' . route('staff.promos.patch', $promo->id) . '" method="POST" class="d-inline">'
                        . csrf_field()
                        . method_field('PATCH')
                        . '<button type="submit" class="btn btn-warning">Non-aktifkan</button>'
                        . '</form>';
                }
                return '<div class="d-flex gap-2 justify-content-center align-items-center">'
                    . $btnEdit . $btnDelete . $btnNonaktif . '</div>';
            })
            ->rawColumns(['type', 'discount', 'activedBadge', 'buttons'])
            ->make(true);
    }
}
