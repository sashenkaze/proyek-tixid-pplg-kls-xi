@extends('templates.app')

@section('content')
    <div class="container my-5">
        <div class="d-flex justify-content-end">
            <a href="{{ route('admin.movies.export') }}" class="btn btn-secondary me-2">Export (.xlsx)</a>
            <a href="{{ route('admin.movies.create') }}" class="btn btn-success">Tambah Data</a>
            <a href="{{ route('admin.movies.trash') }}" class="btn btn-warning ms-2">Data Trash</a>
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
        <h5 class="mt-3">Data Film</h5>
        <table class="table table-bordered" id="movieTable">
            <thead>
                <tr>
                    <td>#</td>
                    <td>Poster</td>
                    <td>Judul Film</td>
                    <td>Status</td>
                    <td>Aksi</td>
                </tr>
            </thead>
        </table>

        <!-- Modal -->
        <div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Deskripsi Film</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modalDetailBody">
                        ...
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


{{-- mengisi stack --}}
@push('script')
    <script>
        // $ memanggil query JS
        // Membuat tampilan datatable di id="movieTable"
        $(function() {
            $('#movieTable').DataTable({
                // Memberi tanda load pas lagi memproses controller
                processing: true,
                // Data yang disajikan di proses di controller (server side)
                serverSide: true,
                // route untuk menuju controller yg memproses datatable
                ajax: "{{ route('admin.movies.datatables') }}",
                // Menentukan urutan td
                columns: [
                    // { data: namaDataAtauNamaColumn, name: namaDataAtauNamaColumn, orderable: TRUE/FALSE, searchable: TRUE/FALSE }
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'imgPoster',
                        name: 'imgPoster',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'title',
                        name: 'title',
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
                    // Kalau mau ditambah aksi order -> orderable: true, kalau nggak mau ada sort di data tersebut -> orderable : false
                    // Kalau mau disertakan pada proses pencarian -> searchable: true, kalau nggak mau disertakan untuk mencari data -> searchable: false
                ]
            })
        })
    </script>

    <script>
        function showModal(item) {
            // console.log(item);
            //mengambil image dengan fungsi php
            let image = "{{ asset('storage/') }}" + "/" + item.poster;
            //backtip (`) = menyimpan string yang berbais-baris, ada enternya
        let content = `
                    <img src="${image}" width="120" class="d-block mx-auto my-2">
                    <ul>
                        <li>Judul : ${item.title}</li>
                        <li>Durasi Film : ${item.duration}</li>
                        <li>Genre Film : ${item.genre}</li>
                        <li>Sutradara : ${item.director}</li>
                        <li>Usia Minimal : <span class = "badge badge-danger"> ${item.age_rating}+</span></li>
                        <li>Sinopsis : ${item.description}</li>
                    </ul>
                `;
            //panggil element html yang akan diisi konten diatas : document.queryselector()
            let modalDetailBody = document.querySelector("#modalDetailBody");
            //isi konten html : innerhtml
            modalDetailBody.innerHTML = content;
            //panggil element html bagian modal
            let modalDetail = document.querySelector("#modalDetail");
            //munculkan modal bootstrap
            new bootstrap.Modal(modalDetail).show();
        }
    </script>
@endpush
