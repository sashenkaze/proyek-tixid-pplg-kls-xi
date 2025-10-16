@extends('templates.app')

@section('content')
    <div class="container mt-5">
        <div class="d-flex justify-content-end mt-3">
            <a href="{{ route('staff.promos.export') }}" class="btn btn-secondary me-2">Export (.xlsx)</a>
            <a href="{{ route('staff.promos.create') }}" class="btn btn-success">Tambah Promo</a>
            <a href="{{ route('staff.promos.trash') }}" class="btn btn-warning ms-2">Data Trash</a>
        </div>
        @if (Session::get('success'))
            <div class="alert alert-success mt-3">{{ Session::get('success') }}</div>
        @endif
        @if (Session::get('login'))
            <div class="alert alert-success mt-3">{{ Session::get('login') }}, <b>Selamat Datang {{ Auth::user()->name }}</b>
            </div>
        @endif
        @if (Session::get('failed'))
            <div class="alert alert-danger mt-3">
                <h3>Gagal!</h3>{{ Session::get('failed') }}
            </div>
        @endif
        <h5>Data Promo</h5>

        <table class="table table-responsive table-bordered mt-3" id="promoTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Kode</th>
                    <th>Tipe</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            {{-- @foreach ($promos as $key => $promo)
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
                    <td>
                        @if ($promo->actived == 1)
                            <span class="badge badge-success">Aktif</span>
                        @else
                            <span class="badge badge-danger">Non-aktif</span>
                        @endif
                    </td>
                    <td class="d-flex gap-2 justify-content-center">
                        <a href="{{ route('staff.promos.edit', $promo->id) }}" class="btn btn-primary btn-sm">Edit</a>

                        <form action="{{ route('staff.promos.destroy', $promo->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('Yakin mau hapus promo ini?')">Hapus</button>
                        </form>

                        @if ($promo->actived == 1)
                            <form action="{{ route('staff.promos.patch', $promo->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-warning btn-sm"
                                    onclick="return confirm('Yakin mau non-aktifkan promo ini?')">Non-aktifkan</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach --}}
        </table>

    </div>
@endsection

@push('script')
    <script>
        $(function() {
            $('#promoTable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: "{{ route('staff.promos.datatables') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'promo_code',
                        name: 'promo_code',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'type',
                        name: 'type',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'discount',
                        name: 'discount',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'activedBadge',
                        name: 'activedBadge',
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
