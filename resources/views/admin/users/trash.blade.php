@extends('templates.app')

@section('content')
    <div class="container my-5">
        <div class="d-flex justify-content-end">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
        @if (Session::get('success'))
            <div class="alert alert-success mt-3">
                <h3>Berhasil!</h3> {{ Session::get('success') }}
            </div>
        @endif
        <h3 class="my-3">Data Sampah : Film</h3>
        <table class="table table-bordered">
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
            @foreach ($users as $key => $user)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    @if ($user->role === 'admin')
                        <td><small class="badge badge-info">{{ $user->role }}</small></td>
                    @else
                        <td><small class="badge badge-success">{{ $user->role }}</small></td>
                    @endif
                    <td class="d-flex align-item-center">
                        <form action="{{ route('admin.users.restore', $user  ['id']) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success">Kembalikan</button>
                        </form>
                        <form action="{{ route('admin.users.delete_permanent', $user['id']) }}" method="POST"
                            class="ms-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Hapus Selamanya</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection
