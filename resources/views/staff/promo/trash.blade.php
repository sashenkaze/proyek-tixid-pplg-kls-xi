@extends('templates.app')

@section('content')
    <div class="container my-5">
        <div class="d-flex justify-content-end">
            <a href="{{ route('staff.promos.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
        @if (Session::get('success'))
            <div class="alert alert-success mt-3">
                <h3>Berhasil!</h3> {{ Session::get('success') }}
            </div>
        @endif
        <h3 class="my-3">Data Sampah : Promo</h3>
        <table class="table table-bordered">
            <tr>
                <th>#</th>
                <th>Kode</th>
                <th>Tipe</th>
                <th>Jumlah</th>
                <th>Aksi</th>
            </tr>
            @foreach ($promos as $key => $promo)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $promo->promo_code }}</td>
                    <td>{{ $promo->type == 'percent' ? '% (Persen)' : 'Rp (Rupiah)' }}</td>
                    <td>
                        @if ($promo->type == 'percent')
                            {{ $promo->discount }}%
                        @else
                            Rp {{ number_format($promo->discount, 0, ',', '.') }}
                        @endif
                    </td>
                    <td class="d-flex align-item-center">
                        <form action="{{ route('staff.promos.restore', $promo['id']) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success">Kembalikan</button>
                        </form>
                        <form action="{{ route('staff.promos.delete_permanent', $promo['id']) }}" method="POST"
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
