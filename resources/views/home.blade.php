@extends('templates.app')

@section('content')
    <style>
        .bioskop {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }

        .bioskop:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            border-radius: 5px;
        }

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

    @if (Session::get('success'))
        <div class="alert alert-success w-100">
            {{ Session::get('success') }}<b>Selamat Datang, {{ Auth::user()->name }}</b>
        </div>
    @endif
    @if (Session::get('logout'))
        <div class="alert alert-warning w-100">
            {{ Session::get('logout') }}
        </div>
    @endif
    <div class="dropdown">
        <button class="btn btn-light dropdown-toggle d-flex align-items-center w-100" type="button" data-bs-toggle="dropdown"
            aria-expanded="false">
            <i class="fas fa-location-dot me-2"></i>BOGOR
        </button>
        <ul class="dropdown-menu w-100">
            <li><a class="dropdown-item" href="#">Bogor</a></li>
            <li><a class="dropdown-item" href="#">Jakarta</a></li>
            <li><a class="dropdown-item" href="#">Bandung</a></li>
        </ul>
    </div>

    <!-- Carousel wrapper -->
    <div id="carouselBasicExample" class="carousel slide carousel-fade" data-mdb-ride="carousel" data-mdb-carousel-init>
        <!-- Indicators -->
        <div class="carousel-indicators">
            <button type="button" data-mdb-target="#carouselBasicExample" data-mdb-slide-to="0" class="active"
                aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-mdb-target="#carouselBasicExample" data-mdb-slide-to="1"
                aria-label="Slide 2"></button>
            <button type="button" data-mdb-target="#carouselBasicExample" data-mdb-slide-to="2"
                aria-label="Slide 3"></button>
            <button type="button" data-mdb-target="#carouselBasicExample" data-mdb-slide-to="3"
                aria-label="Slide 4"></button>
            <button type="button" data-mdb-target="#carouselBasicExample" data-mdb-slide-to="4"
                aria-label="Slide 5"></button>
        </div>

        <!-- Inner -->
        <div class="carousel-inner">
            <!-- Single item -->
            <div class="carousel-item active">
                <img src="https://asset.tix.id/banner_promo_v2/5d77315c-eca6-4747-b3bd-991a170a81d1.webp" class="d-block w-100"
                    alt="Sunset Over the City" />
                <div class="carousel-caption d-none d-md-block">

                </div>
            </div>

            <!-- Single item -->
            <div class="carousel-item">
                <img src="{{ asset('66f9ade8-766b-4303-b4e1-75fc13d9a517.jpg') }}" class="d-block w-100"
                    alt="Canyon at Nigh" />
                <div class="carousel-caption d-none d-md-block">

                </div>
            </div>

            <!-- Single item -->
            <div class="carousel-item">
                <img src="https://asset.tix.id/microsite_v2/364808bc-9f54-4246-acfa-cbb952c38470.webp" class="d-block w-100"
                    alt="Canyon at Nigh" />
                <div class="carousel-caption d-none d-md-block">

                </div>
            </div>

            <!-- Single item -->
            <div class="carousel-item">
                <img src="https://asset.tix.id/microsite_v2/4bdd2c55-fbaa-4081-afe6-8f127cd4963a.webp" class="d-block w-100"
                    alt="Canyon at Nigh" />
                <div class="carousel-caption d-none d-md-block">

                </div>
            </div>

            <!-- Single item -->
            <div class="carousel-item">
                <img src="https://asset.tix.id/microsite_v2/13e54493-a006-49d8-8011-7bfc10270fc5.webp" class="d-block w-100"
                    alt="Cliff Above a Stormy Sea" />
                <div class="carousel-caption d-none d-md-block">

                </div>
            </div>
        </div>
        <!-- Inner -->

        <!-- Controls -->
        <button class="carousel-control-prev" type="button" data-mdb-target="#carouselBasicExample" data-mdb-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-mdb-target="#carouselBasicExample" data-mdb-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
    <!-- Carousel wrapper -->
    <div class="container my-3">
        <div class="d-flex justify-content-between align-items-center w-100">
            <div class="mb-3 mt-3">
                <h5>
                    <i class="fa-solid fa-clapperboard"></i> Sedang Tayang
                </h5>
            </div>
            <div>
                <a href="{{ route('home.movies.all') }}" class="btn btn-warning rounded-pill">
                    {{-- <i class="fa-solid fa-film"></i> --}} Semua Film
                </a>
            </div>
        </div>
        <div class="d-flex mb-5 my-3 gap-2">
            <a href="{{ route('home.movies.all') }}" class="btn btn-outline-primary rounded-pill bioskop"
                style="padding: 5px 10px !important"><small>Semua Film</small></a>
            <a href="" class="btn btn-outline-primary rounded-pill bioskop"
                style="padding: 5px 10px !important"><small>XXI</small></a>
            <a href="" class="btn btn-outline-primary rounded-pill bioskop"
                style="padding: 5px 10px !important"><small>CGV</small></a>
            <a href="" class="btn btn-outline-primary rounded-pill bioskop"
                style="padding: 5px 10px !important"><small>Cinepolis</small></a>
        </div>
    </div>

    <div class="d-flex justify-content-center gap-5 my-3">
        @foreach ($movies as $item)
            <a href="{{ route('schedules.detail', $item['id']) }}" class="card" style="width: 13rem;">
                <img style="object-fit: cover; min-height: 340px" src="{{ asset('storage/' . $item['poster']) }}"
                    class="card-img-top" alt="Sunset Over the Sea">
                <div class="card-body" style="padding: 0 !important ">
                    <p class="card-text text-center bg-primary py-2 text-warning"><b>Beli Tiket</b></p>
                </div>
            </a>
        @endforeach
    </div>
@endsection
