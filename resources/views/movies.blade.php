@extends('templates.app')

@section('content')
    <style>
        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }

        .card:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            border-radius: 5px;
        }

        .card-body {
            transition: transform 0.3 ease, box-shadow 0.3 ease;
            border-radius: 10px;
            cursor: pointer;
        }

        .card.hover-shadow:hover .card-text a {
            color: #fff !important;
            background-color: #0d6efd !important;
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
        }
    </style>
    <div class="container my-3 mt-5">
        <h5 class="mb-3">Seluruh Film Sedang Tayang</h5>

        {{-- form untuk search : method="GET" karena akan menampilkan data, bukan menambahkan data --}}
        <form action="{{ route('home.movies.all') }}" method="GET">
            <div class="row align-items-center">
                <div class="col-8">
                    <input type="text" class="form-control" placeholder="Cari judul film..." name="search_movie"
                        value="{{ request('search_movie') }}">
                </div>
                <div class="col-4 d-flex">
                    <button type="submit" class="btn btn-primary me-2 flex-fill">Cari</button>
                    @if (request('search_movie'))
                        <a href="{{ route('home.movies.all') }}" class="btn btn-secondary">❌</a>
                    @else
                        <button type="button" class="btn btn-secondary" disabled>❌</button>
                    @endif
                </div>
            </div>
        </form>

        <div class="d-flex justify-content-center flex-wrap gap-5 my-3">
            @foreach ($movies as $item)
                <a href="{{ route('schedules.detail', $item['id']) }}" class="card" style="width: 13rem; margin: 5px;">
                    <img style="object-fit: cover; min-height: 340px" src="{{ asset('storage/' . $item['poster']) }}"
                        class="card-img-top" alt="Poster Film">
                    <div class="card-body p-0">
                        <p class="card-text text-center bg-primary py-2 mb-0">
                            <b class="text-warning">Beli Tiket</b>
                        </p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    <script>
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const clearBtn = document.querySelector('.btn.btn-secondary');
                const searchInput = document.querySelector('input[name="search_movie"]');

                // kalau di search box nya ada teks, dihapus dulu
                if (searchInput && searchInput.value.trim() !== '') {
                    searchInput.value = '';
                }

                // redirect ke movies all kalau button clear nya ada
                if (clearBtn && clearBtn.tagName === 'A') {
                    window.location.href = clearBtn.href;
                }
            }
        });
    </script>
@endsection
