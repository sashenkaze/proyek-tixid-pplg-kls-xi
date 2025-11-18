<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>TIXID</title>
    <link rel="shortcut icon"
        href="https://play-lh.googleusercontent.com/FcRZx_UEXN2uc7uKM5EKGn7Jmb65c8VVELlmligxdfUcjKKIpzFX0SHXFePllD2g4ik"
        type="image/x-icon">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <!-- MDB -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/9.1.0/mdb.min.css" rel="stylesheet" />
    @stack('style') {{-- stack bisa buat css juga seperti js --}}

    {{-- CDN Jquery : CDN JS prioritas, disimpan di head --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    {{-- CDN CSS Datatables --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css">
</head>

<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
        <div class="container-fluid">
            @if (Auth::check() && Auth::user()->role == 'admin')
                <a class="navbar-brand" href="{{ route('admin.dashboard') }}"><img width="146" height="40"
                        class="d-inline-block align-text-top me-2"
                        src="https://asset.tix.id/wp-content/uploads/2021/10/TIXID_logo_blue-300x82.png" alt="TIX ID"
                        decoding="async"
                        srcset="https://asset.tix.id/wp-content/uploads/2021/10/TIXID_logo_blue-300x82.png 300w, https://asset.tix.id/wp-content/uploads/2021/10/TIXID_logo_blue.png 437w"
                        sizes="(max-width: 146px) 100vw, 146px"></a>
            @else
                <a class="navbar-brand" href="{{ route('home') }}"><img width="146" height="40"
                        class="d-inline-block align-text-top me-2"
                        src="https://asset.tix.id/wp-content/uploads/2021/10/TIXID_logo_blue-300x82.png" alt="TIX ID"
                        decoding="async"
                        srcset="https://asset.tix.id/wp-content/uploads/2021/10/TIXID_logo_blue-300x82.png 300w, https://asset.tix.id/wp-content/uploads/2021/10/TIXID_logo_blue.png 437w"
                        sizes="(max-width: 146px) 100vw, 146px"></a>
            @endif

            <button data-mdb-collapse-init class="navbar-toggler" type="button" data-mdb-target="#navbarNavAltMarkup"
                aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    @if (Auth::check() && Auth::user()->role == 'admin')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a data-mdb-dropdown-init class="nav-link dropdown-toggle" href="#"
                                id="navbarDropdownMenuLink" role="button" aria-expanded="false">Data Master</a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.cinemas.index') }}">Bioskop</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.movies.index') }}">Film</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.users.index') }}">Petugas</a>
                                </li>
                            </ul>
                        </li>
                    @elseif (Auth::check() && Auth::user()->role == 'staff')
                        <li class="nav-item">
                            <a href="{{ route('staff.promos.index') }}" class="nav-link">Promo</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('staff.schedules.index')}}" class="nav-link">Jadwal Tayang</a>
                        </li>
                    @else
                        <a class="nav-link active" aria-current="page" href="{{ route('home') }}"><i
                                class="fa-solid fa-house me-2"></i>Beranda</a>
                        <a class="nav-link" href="{{ route('cinemas.list') }}"><i class="fa-solid fa-film me-2"></i>Bioskop</a>
                        <a class="nav-link" href="{{ route('tickets.index') }}"><i class="fa-solid fa-ticket me-2"></i>Tiket</a>
                    @endif
                </div>

                <div class="d-flex justify-content-center flex-grow-1 mx-3">
                    <div class="input-group" style="max-width: 400px;">
                        <input type="search" class="form-control border-end-0" placeholder="Search"
                            aria-label="Search">
                        <button class="btn btn-outline-secondary border-start-0 bg-white" type="button">
                            <i class="fas fa-search text-muted"></i>
                        </button>
                    </div>
                </div>
                <div class="d-flex ms-auto">
                    @if (Auth::check())
                        <a href="{{ route('logout') }}" class="btn btn-danger">Logout</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary me-3">Login</a>
                        <a href="{{ route('signup') }}" class="btn btn-primary me-3">Sign up</a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-fill pb-5">
        @yield('content') {{-- dinamis isi html --}}
    </main>

    <footer class="bg-body-tertiary text-center text-lg-start mt-5">
        <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.05)">
            © {{ date('Y')}} Copyright:
            <a class="text-body" href="https://tix.id" target="_blank">TixID</a> | <a class="text-body" href="https://github.com/SashenkAze" target="_blank">Sashenka Osaze</a>
        </div>
    </footer>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/9.1.0/mdb.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"
        integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous">
    </script>

     {{-- CDN JS datatables --}}
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

    {{-- CDN chartJS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- yield versi js / dinamis isi js --}}
    @stack('script')
</body>

</html>
