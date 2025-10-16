@extends('templates.app')

@section('content')
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
                <div class="card" style="width: 13rem; margin: 5px;">
                    <img style="object-fit: cover; min-height: 340px" src="{{ asset('storage/' . $item['poster']) }}"
                        class="card-img-top" alt="Poster Film">
                    <div class="card-body p-0">
                        <a href="{{ route('schedules.detail', $item['id']) }}">
                            <p class="card-text text-center bg-primary py-2 mb-0">

                                <b class="text-warning">Beli Tiket</b>

                            </p>
                        </a>
                    </div>
                </div>
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
