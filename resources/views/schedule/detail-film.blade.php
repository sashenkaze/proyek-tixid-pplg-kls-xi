@extends('templates.app')

@section('content')
    <div class="container pt-5">
        <div class="w-75 d-block m-auto d-flex justify-content-center">
            <div style="width: 150px; height: 200px">
                <img src="{{ asset('storage/' . $movie['poster']) }}" alt="" class="w-100">
            </div>
            <div class="mx-5 mt-4">
                <h5>{{ $movie['title'] }}</h5>
                <table>
                    <tr>
                        <td><b class="text-secondary">Genre</b></td>
                        <td class="px-3"></td>
                        <td>{{ $movie['genre'] }}</td>
                    </tr>
                    <tr>
                        <td><b class="text-secondary">Durasi</b></td>
                        <td class="px-3"></td>
                        <td>{{ $movie['duration'] }}</td>
                    </tr>
                    <tr>
                        <td><b class="text-secondary">Sutradara</b></td>
                        <td class="px-3"></td>
                        <td>{{ $movie['director'] }}</td>
                    </tr>
                    <tr>
                        <td><b class="text-secondary">Rating Usia</b></td>
                        <td class="px-3"></td>
                        <td><span class="badge badge-danger">{{ $movie['age_rating'] }}+    </span></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="w-100 row mt-5">
            <div class="col-6 pe-5">
                <div class="d-flex flex-column justify-content-end align-items-end">
                    <div class="d-flex align-items-center">
                        <h3 class="text-warning me-2">9.0</h3>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fa-regular fa-star text-warning"></i>
                    </div>
                    <small>4.414 Vote</small>
                </div>
            </div>
            <div class="col-6 ps-5" style="border-left: 2px solid #c7c7c7">
                <div class="d-flex align-items-center">
                    <div class="fas fa-heart text-danger me-2"></div>
                    <b>Masukan Watchlist</b>
                </div>
                <small>9.000 Orang</small>
            </div>
            <div class="d-flex w-100 bg-light mt-3">
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Bioskop
                    </button>
                    <ul class="dropdown-menu">
                        @foreach ($listCinema['schedules'] as $schedule)
                            <li><a href="?search-cinema={{ $schedule['cinema']['id'] }}" class="dropdown-item">{{ $schedule['cinema']['name'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Sortir
                    </button>
                    @php

                        // Ambil nilai tanda tanya ? di url : request()-> get('nama-params')
                        if (request()->get('sort-price')) {
                            // Kalau di url ada ?sort-price=
                            // Cek jika nilai di url skrng ASC, maka berikutnya jadi DESC
                            if (request()->get('sort-price') == 'ASC') {
                                $typePrice = 'DESC';
                            } else {
                                $typePrice = 'ASC';
                            }
                        } else {
                            // Kalau di url gak ada ?sort-price= berarti baru pertama kali di klik sortir, isi default menjadi ASC
                            $typePrice = 'ASC';
                        }

                        if (request()->get('sort-Alfabet')) {
                            if (request()->get('sort-Alfabet') == 'ASC') {
                                $typeAlfabet = 'DESC';
                            } else {
                                $typeAlfabet = 'ASC';
                            }
                        } else {
                            $typeAlfabet = 'ASC';
                        }
                    @endphp
                    <ul class="dropdown-menu">
                        {{-- href="?" tanda tanya digunakan untuk mengirimkan query params melalui http method get atau a href (tidak di dalam form), biasanya digunakan untuk search, sort, limit --}}
                        <li><a href="?sort-price={{ $typePrice }}" class="dropdown-item">Harga</a></li>
                        <li><a href="?sort-alfabet={{ $typeAlfabet }}" class="dropdown-item">Alfabet</a></li>
                    </ul>
                </div>
            </div>
            <div class="mb-5">
                @foreach ($movie['schedules'] as $schedule)
                    <div class="w-100 my-3">
                        <div class="d-flex justify-content-between">
                            <div>
                                <i class="fa-solid fa-building"></i><b class="ms-2">{{ $schedule['cinema']['name'] }}</b>
                                <br>
                                <small class="ms-3">{{ $schedule['cinema']['location'] }}</small>
                            </div>
                            <div>
                                <b>Rp. {{ number_format($schedule['price'], 0, ',', '.') }}</b>
                            </div>
                        </div>
                        <div class="d-flex gap-3 ps-3 my-2">
                            @foreach ($schedule['hours'] as $index => $hours)
                                {{-- this : mengirimkan element html ini ke js untuk di manipulasi --}}
                                <div class="btn btn-outline-secondary" style="cursor: pointer" onclick="selectedHour('{{ $schedule->id }}', '{{ $index }}', this)">{{ $hours }}</div>
                            @endforeach
                        </div>
                    </div>
                    <hr>
                @endforeach
            </div>
            <div class="w-100 p-2 bg-light text-center fixed-bottom" id="wrapBtn">
                {{-- disabled button = kalau  --}}
                <a href="javascript:void(0)" id="btnTicket"><i class="fa-solid fa-ticket"></i> BELI TIKET</a>
            </div>  
        </div>
    @endsection

@push('script')
        <script>
            // menyimpan elemen sebelumnya yang pernah di klik
            let elementBefore = null;
            function selectedHour(scheduleId, hourId, el) {
                // jika elemen sebelumnya ada, dan skrg pindah ke elemen lain kliknya. ubah elemen sebelumnya jadi putih lagi
                if (elementBefore) {
                    // ubah styling css : style.property
                    elementBefore.style.background = "";
                    elementBefore.style.color = "";
                    // property css kebab (border-color) di js jadi camel case (borderColor)
                    elementBefore.style.borderColor = "";
                }
                // kasih warna ke element baru
                el.style.background = "#112645";
                el.style.color = "white";
                el.style.borderColor = "#112645";
                // update elemen sebelumnya pake elemen baru
                elementBefore = el;

                let wrapBtn = document.querySelector("#wrapBtn");
                let btnTicket = document.querySelector("#btnTicket");
                // kasih warna biru ke div wrap dan hilangkan warna abu
                // warna abu dari 'bg-light' class bootstrap
                wrapBtn.classList.remove('bg-light');
                wrapBtn.style.background = "#112645";
                // siapkan route
                let url = "{{ route('schedules.show-seats', ['scheduleId' => ':scheduleId', 'hourId' => ':hourId']) }}"
                .replace(":scheduleId", scheduleId) //ubah parameter (:) dengan value dari js
                .replace(":hourId", hourId);
                // isi url ke href btnTicket
                btnTicket.href = url;
                btnTicket.style.color = 'white';
            }
        </script>
@endpush
