@extends('templates.app')

@section('content')
    <div class="container mt-5">
        <div class="d-flex justify-content-end mt-3">
            <a href="{{ route('admin.cinemas.export') }}" class="btn btn-secondary me-2">Export (.xlsx)</a>
            <a href="{{ route('admin.cinemas.create') }}" class="btn btn-success">Tambah Data</a>
            <a href="{{ route('admin.cinemas.trash') }}" class="btn btn-warning ms-2">Data Trash</a>
        </div>
        @if (Session::get('success'))
            <div class="alert alert-success mt-3">
                <h3>Berhasil!</h3>{{ Session::get('success') }}
            </div>
        @endif
        @if (Session::get('failed'))
            <div class="alert alert-danger mt-3">
                <h3>Gagal!</h3>{{ Session::get('failed') }}
            </div>
        @endif
        <h5 class="mt-3">Data Bioskop</h5>
        <table class="table table-responsive table-bordered mt-3" id="cinemaTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Bioskop</th>
                    <th>Lokasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            {{-- karena all() banyak data maka akan berbentuk array, munculinnya pakai foreach --}}
            {{-- @foreach ($cinemas as $key => $item)
                <tr>
                    {{-- munculin 1,2,3 dari $key (index) yg mulainya dari 0 biar dari 1 ditambah +1 --}}
            {{-- <td>{{ $key + 1 }}</td> --}}
            {{-- mengambil data $var['field'] --}}
            {{-- <td>{{ $item['name'] }}</td>
                    <td>{{ $item['location'] }}</td>
                    <td class="d-flex justify-content-center"> --}}
            {{-- item['id'] = route ('name'), $data yg dikirim, mengirim id ke/{id} --}}
            {{-- <a href="{{ route('admin.cinemas.edit', $item['id']) }}" class="btn btn-primary">Edit</a> --}}
            {{-- <a href="" class="btn btn-danger ms-2">Hapus</a> --}}
            {{-- <form action="{{ route('admin.cinemas.destroy', $item['id']) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger ms-2"
                                onclick="return confirm('Yakin mau hapus data ini?')">Hapus</button>
                        </form>

                    </td>
                </tr>
            @endforeach --}}
        </table>
    </div>
@endsection

@push('script')
    <script>
        $(function() {
            $('#cinemaTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.cinemas.datatables') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'location',
                        name: 'location',
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
            })
        })
    </script>
@endpush
