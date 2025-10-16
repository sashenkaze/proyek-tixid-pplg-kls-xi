@extends('templates.app')

@section('content')
    <div class="container mt-5">
        <div class="d-flex justify-content-end mt-3">
            <a href="{{ route('admin.users.export') }}" class="btn btn-secondary me-2">Export (.xlsx)</a>
            <a href="{{ route('admin.users.create') }}" class="btn btn-success">Tambah Staff</a>
            <a href="{{ route('admin.users.trash') }}" class="btn btn-warning ms-2">Data Trash</a>
        </div>

        @if (Session::get('success'))
            <div class="alert alert-success mt-3">
                <h3>Berhasil!</h3> {{ Session::get('success') }}
            </div>
        @endif
        @if (Session::get('failed'))
            <div class="alert alert-danger mt-3">
                <h3>Gagal!</h3>{{ Session::get('failed') }}
            </div>
        @endif
        <h5>Data Pengguna</h5>

        <table class="table table-responsive table-bordered mt-3" id="userTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            {{-- @foreach ($users as $key => $user)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    @if ($user->role === 'admin')
                        <td><small class="badge badge-info">{{ $user->role }}</small></td>
                    @else
                        <td><small class="badge badge-success">{{ $user->role }}</small></td>
                    @endif


                    <td class="d-flex justify-content-center">
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary">Edit</a>

                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
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
            $('#userTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.users.datatables') }}",
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
                        data: 'email',
                        name: 'email',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'roleBadge',
                        name: 'roleBadge',
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
