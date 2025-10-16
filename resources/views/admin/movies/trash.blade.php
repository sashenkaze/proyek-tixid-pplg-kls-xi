@extends('templates.app')

@section('content')
    <div class="container my-5">
        <div class="d-flex justify-content-end">
            <a href="{{ route('admin.movies.index') }}" class="btn btn-secondary">Kembali</a>
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
                <th>Nama Bioskop</th>
                <th>Judul Film</th>
                <th>Aksi</th>
            </tr>
            @foreach ($movies as $Key => $movie)
                <tr>
                    <td>{{ $Key + 1 }}</td>
                    <td><img src="{{ asset('storage/' . $movie['poster']) }}" style="width: 150px;" class="img-thumbnail" alt=""></td>
                    <td>{{ $movie['title'] }}</td>
                    <td class="d-flex align-item-center">
                        <form action="{{ route('admin.movies.restore', $movie  ['id']) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success">Kembalikan</button>
                        </form>
                        <form action="{{ route('admin.movies.delete_permanent', $movie['id']) }}" method="POST"
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
