<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Schedule;
use App\Exports\MovieExport;
use Pdo\Firebird;
use Str;
use Yajra\DataTables\Facades\DataTables;

class MovieController extends Controller
{
    // Function baru
    public function home()
    {
        // Format pencarian data : where('column', 'operator', 'value')
        // Jika operator ==/= operator bisa TIDAK DITULIS
        // Operator yang digunakan : < kurang dari | > lebih dari | <> tidak sama dengan
        // Format mengurutkan data : orderBy('column', 'DEC/ASC') -> DESC z-a/9-0, ASC a-z/0-9
        // Get() : mengambil seluruh data hasil filter
        // Limit(angka) : mengambil data dengan jumlah tertentu
        $movies = Movie::where('actived', 1)->inRandomOrder()->limit(5)->get();
        return view('home', compact('movies'));
    }

    public function homeAllMovie(Request $request)
    {
        // Ambil data dari input name="search_movie"
        $title = $request->search_movie;
        // Kalau search_movie nggak kosong, cari data
        if ($title != "") {
            // Operator LIKE : mencari data yang mirip/mengandung kata tertentu
            // % digunakan untuk mengaktifkan LIKE
            // $kata : mencari kata belakang
            // Kata% : mencari kata depan
            // %kata% : mencari kata depan, tengah, belakang
            $movies = Movie::where('title', 'LIKE', '%' . $title . '%')->where('actived', 1)->orderBy('created_at', 'DESC')->get();
        } else {
            $movies = Movie::where('actived', 1)->orderBy('created_at', 'DESC')->get();
        }

        return view('movies', compact('movies'));
    }

    public function movieSchedules($movie_id, Request $request)
    {
        // Request $request : mengambil data dari form atau href="?"
        $sortPrice = $request['sort-price'];
        if ($sortPrice) {
            // Karena mau mengurutkan berdasarkan price yang ada di schedules, maka sorting (orderBy) disimpan di relasi with schedules
            $movie = Movie::where('id', $movie_id)->with([
                'schedules' => function ($q) use ($sortPrice) {
                    // $q : mewakilkan model schedule
                    // 'schedules => function($q) {...} : melakukan filter/menjalankan eloquent di dalam relasi
                    $q->orderBy('price', $sortPrice);
                },
                'schedules.cinema'
            ])->first();
        } else {
            // Mengambil relasi di dalam relasi
            // Relasi cinema ada di schedule -> schedules.cinema (.)
            $movie = Movie::where('id', $movie_id)->with(['schedules', 'schedules.cinema'])->first();
            // First() : karena 1 data film, diambilnya satu
        }

        $sortAlfabet = $request['sort-alfabet'];
        if ($sortAlfabet) {
            // Ambil collection, collection : hasil dari get, first, all
            // $movie->schedules mengacu ke data relasi schedules
            // sortBy : mengurutkan collection (ASC), orderBy : mengurutkan query eloquent
            $movie->schedules = $movie->schedules->sortBy(function ($schedule) {
                return $schedule->cinema->name; // Mengurutkan berdasarkan name dari relasi cinema
            })->values();
        } elseif ($sortAlfabet == 'DESC') {
            // Kalau sortAlfabet bukan ASC, berarti DESC, gunakan sortByDesc (untuk mengurutkan secara DESC)
            $movie->schedules = $movie->schedules->sortByDesc(function ($schedule) {
                return $schedule->cinema->name;
            })->values();
            // Values() : ambil ulang data dari collection
        }

        // $searchCinema = $request['search-cinema'];
        // if ($searchCinema) {
        //     // whereHas('namarelasi') : mengambil data utama hanya jika memiliki relasi ini
        //     // whereHas('namarelasi', function($q) {...}) : mengambil data utama hanya jika memiliki relasi in idan data pada relasinya memiliki kriteria tertentu
        //     $movie = Movie::where('id', $movie_id)->where(['schedules', 'schedules.cinema'])->whereHas('schedules', function($q) use($searchCinema) {
        //         // Ambil data movie yang memiliki data schedules dan data schedules bagian cinema_id nya sesuai dengan $searchCinema
        //         // 'cinema_id' yang dimaksud disini cinema_id yg dimiliki model Schedule, karena $q mewakilkan model Schedule
        //         $q->where('cinema_id', $searchCinema);
        //     })->first();
        // }

        $searchCinema = $request['search-cinema'];
        if ($searchCinema) {
            // Filter collection, ambil relasi schedules hanya yg cinema_id nya sesuai dengan search-cinema
            $movie->schedules = $movie->schedules->where('cinema_id', $searchCinema)->values();
        }

        // List untuk dropdown bioskop, data murni yang tidak terfilter/sort apapun
        $listCinema = Movie::where('id', $movie_id)->with(['schedules', 'schedules.cinema'])->first();

        return view('schedule.detail-film', compact('movie', 'listCinema'));

    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $movies = Movie::all();
        return view('admin.movies.index', compact('movies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.movies.create');
    }

    /**
     * Store a newly created resourc    e in storage.
     */
    public function store(Request $request)
    {
        // Cek seluruh request data dr input form
        // Dd($request->all());
        $request->validate([
            'title' => 'required',
            'duration' => 'required',
            'genre' => 'required',
            'director' => 'required',
            'age_rating' => 'required|numeric',
            // Mimes => jenis file yang boleh di upload
            'poster' => 'required|mimes:jpeg,png,jpg,svg,webp',
            'description' => 'required|min:10',
        ], [
            'title.required' => 'Judul film harus diisi',
            'duration.required' => 'Durasi film harus diisi',
            'genre.required' => 'Genre film harus diisi',
            'director.required' => 'Sutradara harus diisi',
            'age_rating.required' => 'Usia minimal harus diisi',
            'age_rating.numeric' => 'Usia minimal harus berupa angka',
            'poster.required' => 'Poster file harus diisi',
            'poster.mimes' => 'Poster file harus berupa gambar (jpeg, png, jpg, svg, webp)',
            'description.required' => 'Sinopsis harus diisi',
        ]);
        // $request->file('poster') => untuk mengambil file yang diupload dari input form dengan name 'poster'
        $gambar = $request->file('poster');
        // Buat nama  baru, nama acak untuk membedakan tiap file yang diupload
        // Abcde-poster.jpg
        // GetClientOriginalExtension() => untuk mengambil ekstensi asli dari file yang diupload
        $namaGambar = Str::random(5) . "-poster." . $gambar->getClientOriginalExtension();
        // StoreAs("posters", $namaGambar, 'public') => untuk menyimpan file yang diupload ke dalam folder 'posters' di dalam folder 'storage/app/public'
        // Hasil storeAs berupa path/file yang disimpan, visibility : public/private
        $path = $gambar->storeAs("poster", $namaGambar, 'public');

        $createData = Movie::create([
            'title' => $request->title,
            'duration' => $request->duration,
            'genre' => $request->genre,
            'director' => $request->director,
            'age_rating' => $request->age_rating,
            // Yang disimpan di db lokasi file nya dari storeAs
            'poster' => $path,
            'description' => $request->description,
            'actived' => 1
        ]);
        if ($createData) {
            return redirect()->route('admin.movies.index')->with('success', 'Data film berhasil ditambahkan!');
        } else {
            return redirect()->route('admin.movies.create')->with('error', 'Gagal! Silakan coba lagi.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Movie $movie)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $movie = Movie::find($id);
        return view('admin.movies.edit', compact('movie'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Cek seluruh request data dr input form
        // dd($request->all());
        $request->validate([
            'title' => 'required',
            'duration' => 'required',
            'genre' => 'required',
            'director' => 'required',
            'age_rating' => 'required|numeric',
            // Mimes => jenis file yang boleh di upload
            'poster' => 'mimes:jpeg,png,jpg,svg,webp',
            'description' => 'required|min:10',
        ], [
            'title.required' => 'Judul film harus diisi',
            'duration.required' => 'Durasi film harus diisi',
            'genre.required' => 'Genre film harus diisi',
            'director.required' => 'Sutradara harus diisi',
            'age_rating.required' => 'Usia minimal harus diisi',
            'age_rating.numeric' => 'Usia minimal harus berupa angka',
            'poster.mimes' => 'Poster file harus berupa gambar (jpeg, png, jpg, svg, webp)',
            'description.required' => 'Sinopsis harus diisi',
        ]);

        // Data sebelumnya
        $movie = Movie::find($id);
        // Jika ada file poster baru
        if ($request->file('poster')) {
            $filesebelumnya = storage_path("app/public/" . $movie['poster']);
            // File_exists() : cek apakah file ada di storage/app/public/poster/nama.jpg
            if (file_exists($filesebelumnya)) {
                // Unlink : hapus
                unlink($filesebelumnya);
            }

            $gambar = $request->file('poster');
            // Buat nama  baru, nama acak untuk membedakan tiap file yang diupload
            // Abcde-poster.jpg
            // GetClientOriginalExtension() => untuk mengambil ekstensi asli dari file yang diupload
            $namaGambar = Str::random(5) . "-poster." . $gambar->getClientOriginalExtension();
            // StoreAs("posters", $namaGambar, 'public') => untuk menyimpan file yang diupload ke dalam folder 'posters' di dalam folder 'storage/app/public'
            // Hasil storeAs berupa path/file yang disimpan, visibility : public/private
            $path = $gambar->storeAs("poster", $namaGambar, 'public');
        }

        $updateData = Movie::where('id', $id)->update([
            'title' => $request->title,
            'duration' => $request->duration,
            'genre' => $request->genre,
            'director' => $request->director,
            'age_rating' => $request->age_rating,
            'poster' => $path ?? $movie['poster'],
            'description' => $request->description,
            'actived' => 1
        ]);
        if ($updateData) {
            return redirect()->route('admin.movies.index')->with('success', 'Data film berhasil ditambahkan!');
        } else {
            return redirect()->route('admin.movies.create')->with('error', 'Gagal! Silakan coba lagi.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */

    public function dataChart() {
        $movieActive = Movie::where('actived', 1)->count();
        $movieNonActive = Movie::where('actived', 0)->count();

        $labels = ['Film Aktif', 'Film Non-Aktif'];
        $data = [$movieActive, $movieNonActive];

        return response()->json(['labels' => $labels, 'data' => $data]);
    }
    public function destroy($id)
    {
        $schedules = Schedule::where('movie_id', $id)->count();
        if ($schedules) {
            return redirect()->route('admin.movies.index')->with('failed', 'Tidak dapat menghapus data bioskop! Data tertaut fengan jadwal tayang');
        }

        $movie = Movie::findOrFail($id);
        // Hapus gambar nya dari storage
        if ($movie->poster) {
            $posterPath = storage_path("app/public" . $movie['poster']);
            if (file_exists($posterPath)) {
                unlink($posterPath);
            }
        }

        $movie->delete();

        return redirect()->route('admin.movies.index')->with('success', 'Data film berhasil dihapus');
    }

    // Untuk non aktifkan suatu data film
    public function patch($id)
    {
        $movie = Movie::findOrFail($id);
        $movie->update([
            'actived' => 0
        ]);

        return redirect()->route('admin.movies.index')->with('success', 'Film berhasil dinonaktifkan!');
    }

    public function exportExcel()
    {
        $file_name = 'data-film.xlsx';
        return Excel::download(new MovieExport, $file_name);
    }

    public function trash()
    {
        // OnlyTrashed() : filter data yang sudah di hps, yg deleted_at di phpmyadmin nya ada isi tanggal, hanya filter tetap gunakan get()/first() untuk ambilnya
        $movies = Movie::onlyTrashed()->get();
        return view('admin.movies.trash', compact('movies'));
    }

    public function restore($id)
    {
        // Sebelum dicari, difilter dulu diakses hanya yg sudah diihapus
        $movie = Movie::onlyTrashed()->find($id);
        // Restore() : mengembalikan data yg sudah dihapus
        $movie->restore();
        return redirect()->route('admin.movies.index')->with('success', 'Data berhasil dikembalikan!');
    }

    public function deletePermanent($id)
    {
        $movie = Movie::onlyTrashed()->find($id);
        // ForceDelete() : menghapus permanen dari db
        $movie->forceDelete();
        return redirect()->route('admin.movies.index')->with('success', 'Data berhasil dihapus permanen!');
    }

    public function dataForDatatables()
    {
        // Siapkan query eloquent dari model Movie
        $movies = Movie::query()->get();
        // DataTables::of($movies) : menyiapkan data untuk DataTables, data diambil ari $movies
        return DataTables::of($movies)->addIndexColumn() // Memberikan nomor 1, 2, dst, di column table
            // addColumn() : menambahkan data dari selain table movies, digunakan untuk button aksi dan data yang perlu di manipulasi
            ->addColumn('imgPoster', function ($movie) {
                $urlImage = asset('storage') . "/" . $movie['poster'];
                // Menambahkan data baru bernama imgPoster dengan hasil tag img yang link nya udah nyambung ke storage "' untuk konten ke variabel
                return '<img src="' . $urlImage . '" width="200px">';
            })
            ->addColumn('activedBadge', function ($movie) {
                if ($movie->actived == 1) {
                    return '<span class="badge badge-success">Aktif</span>';
                } else {
                    return '<span class="badge badge-secondary">Non-Aktif</span>';
                }
            })
            ->addColumn('buttons', function ($movie) {
                $jsonData = e(json_encode($movie));
                $btnDetail = '<button class="btn btn-secondary me-2" onclick="showModal(' . $jsonData . ')">Detail</button>';
                $btnEdit = '<a href="' . route('admin.movies.edit', $movie['id']) . '" class="btn btn-primary me-2">Edit</a>';
                $btnDelete = '<form action="' . route('admin.movies.destroy', $movie['id']) . '" method="POST" class="m-0">' .
                    csrf_field() .
                    method_field('DELETE') .
                    '<button type="submit" class="btn btn-danger me-2">Hapus</button>
                        </form>';
                $btnNonAktif = '';
                if ($movie->actived == 1) {
                    $btnNonAktif = '<form action="' . route('admin.movies.patch', $movie['id']) . '" method="POST" class="m-0">' .
                        csrf_field() .
                        method_field('PATCH') .
                        '<button type="submit" class="btn btn-warning me-2">Non-Aktifkan</button>
                        </form>';
                }
                return '
                    <div class="d-flex flex-column align-items-center">
                        <div class="d-flex gap-2 mb-2" style="width: 100%; max-width: 250px;">
                            ' . $btnDetail . $btnEdit . '
                        </div>
                        <div class="d-flex gap-2" style="width: 100%; max-width: 250px;">
                            '. $btnDelete . $btnNonAktif . '
                        </div>
                    </div>
        ';
            })
            // rawColumns() :
            ->rawColumns(['imgPoster', 'activedBadge', 'buttons'])
            ->make(true); // Mengubah query menjadi JSON (format yg bisa dibaca datatables)
    }
}
