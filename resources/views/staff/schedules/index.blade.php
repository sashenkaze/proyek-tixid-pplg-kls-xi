@extends('templates.app')

@section('content')
    <div class="container my-5">
        <div class="d-flex justify-content-end">
            <a href="{{ route('staff.schedules.export') }}" class="btn btn-secondary me-2">Export (.xlsx)</a>
            {{-- karena modal isi tidak akan berubah, munculkan dengan bootstrap target --}}
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAdd">Tambah Data</button>
            <a href="{{ route('staff.schedules.trash') }}" class="btn btn-warning ms-2">Data Trash</a>
        </div>
        @if (Session::get('success'))
            <div class="alert alert-success mt-3">{{ Session::get('success') }}</div>
        @endif
        @if (Session::get('failed'))
            <div class="alert alert-danger mt-3">
                <h3>Gagal!</h3>{{ Session::get('failed') }}
            </div>
        @endif
        <h3 class="my-3">Data Jadwal Tayang</h3>
        <table class="table table-bordered" id="scheduleTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Bioskop</th>
                    <th>Judul Film</th>
                    <th>Harga</th>
                    <th>Jam Tayang</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            {{-- @foreach ($schedules as $key => $schedule)
                <tr>
                    <td>{{ $key + 1 }}</td> --}}
            {{-- ambil nama relasi kemudian nama field --}}
            {{-- <td>{{ $schedule['cinema']['name'] }}</td>
                    <td>{{ $schedule['movie'] ? $schedule['movie']['title'] : 'Film sudah dihapus admin' }}</td>
                    <td>Rp. {{ number_format($schedule['price'], 0, ',', '.') }}</td>
                    <td>
                        <ul> --}}
            {{-- karena hours bentuknya array, jd pakai loop --}}
            {{-- @foreach ($schedule['hours'] as $hour) --}}
            {{-- bentuk array item : ['09.30', '13.00'] jd $hours lgsg berisi datanya --}}
            {{-- <li>{{ $hour }}</li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="d-flex align-items-center">
                        <a href="{{ route('staff.schedules.edit', $schedule->id) }}" class="btn btn-primary">Edit</a>
                        <form method="POST" action="{{ route('staff.schedules.destroy', $schedule->id) }}" class="ms-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach --}}
        </table>

        {{-- modal --}}
        <div class="modal fade" id="modalAdd" tabindex="-1" aria-labelledby="modalAddLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="modalAddLabel">Tambah Data</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('staff.schedules.store') }}">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="cinema_id" class="col-form-label">Bioskop:</label>
                                <select name="cinema_id" id="cinema_id"
                                    class="form-select @error('cinema_id') is-invalid @enderror">
                                    <option disabled hidden selected>-- Pilih Bioskop --</option>
                                    @foreach ($cinemas as $cinema)
                                        {{-- jumlah opsi select sesuai data cinemas --}}
                                        {{-- FK cinema_id menyimpan id jd value ['id'] tp munculkan ['name'] nya --}}
                                        <option value="{{ $cinema['id'] }}">{{ $cinema['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('cinema_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="movie_id" class="col-form-label">Film:</label>
                                <select name="movie_id" id="movie_id"
                                    class="form-select @error('movie_id') is-invalid @enderror">
                                    <option disabled hidden selected>-- Pilih Film --</option>
                                    @foreach ($movies as $movie)
                                        <option value="{{ $movie['id'] }}">{{ $movie['title'] }}</option>
                                    @endforeach
                                </select>
                                @error('movie_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="price" class="col-form-label">Harga:</label>
                                <input type="number" name="price" id="price"
                                    class="form-control @error('price') is-invalid @enderror">
                                @error('price')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="hours" class="col-form-label">Jam Tayang:</label>
                                {{-- Kalau ada error yg berhubungan dengan item array hours --}}
                                @if ($errors->has('hours.*'))
                                    {{-- ambil ket err pada item pertama --}}
                                    <small class="text-danger">{{ $errors->first('hours.*') }}</small>
                                @endif
                                <input type="time" name="hours[]" id="hours"
                                    class="form-control @if ($errors->has('hours.*')) is-invalid @endif">
                                {{-- akan diisi input tambahan dari js --}}
                                <div id="additionalInput"></div>
                                <span class="text-primary my-3" style="cursor: pointer" onclick="addInput()">+Tambah Input
                                    Jam</span>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Kirim</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(function() {
            $('#scheduleTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('staff.schedules.datatables') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'cinema_name',
                        name: 'cinema_name',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'movie_title',
                        name: 'movie_title',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'price_display',
                        name: 'price_display',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'hours_display',
                        name: 'hours_display',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'buttons',
                        name: 'buttons',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });
    </script>


    <script>
        function addInput() {
            let content = '<input type="time" name="hours[]" id="hours" class="form-control my-2">';
            // ambil wadah
            let wrap = document.querySelector('#additionalInput');
            // simpan konten, tp gunakan += agar konten terus bertambah bukan mengubah
            wrap.innerHTML += content;
        }
    </script>
    {{-- pengkondisian php cek error, jika terjadi err apapun : $errors->any() --}}
    @if ($errors->any())
        <script>
            //panggil modal
            let modalAdd = document.querySelector('#modalAdd');
            //munculkan modal lagi, lewat js
            new bootstrap.Modal(modalAdd).show();
        </script>
    @endif
@endpush
