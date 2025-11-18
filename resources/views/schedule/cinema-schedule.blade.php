@extends('templates.app')
@section('content')
    <div class="container mt-5 card">
        <div class="card-body">
            {{-- karena data schedules diambil dengan get() dan data lebh dari satu. maka untuk mengambil data cinemanya ambil dari 1 data aja index 0 --}}
            <i class="fa-solid fa-location-dot me-3"></i>{{ $schedules[0]['cinema']['location'] }}
            <hr>
            @foreach ($schedules as $schedule)
                <div class="my-1">
                    <div class="d-flex">
                        <div style="width: 150px; height: 200px; overflow: hidden;">
                            <img src="{{ asset('storage/' . $schedule['movie']['poster']) }}" alt="" class="w-100">
                        </div>
                        <div class="ms-5 mt-4">
                            <table>
                                <tr>
                                    <td><b class="text-secondary">Judul</b></td>
                                    <td class="px-3"></td>
                                    <td>{{ $schedule['movie']['title'] }}</td>
                                </tr>
                                <tr>
                                    <td><b class="text-secondary">Genre</b></td>
                                    <td class="px-3"></td>
                                    <td>{{ $schedule['movie']['genre'] }}</td>
                                </tr>
                                <tr>
                                    <td><b class="text-secondary">Duration</b></td>
                                    <td class="px-3"></td>
                                    <td>{{ $schedule['movie']['duration'] }}</td>
                                </tr>
                                <tr>
                                    <td><b class="text-secondary">Sutradara</b></td>
                                    <td class="px-3"></td>
                                    <td>{{ $schedule['movie']['director'] }}</td>
                                </tr>
                                <tr>
                                    <td><b class="text-secondary">Rating Usia</b></td>
                                    <td class="px-3"></td>
                                    <td> <span class="badge badge-success">{{ $schedule['movie']['age_rating'] }}+</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="w-100 my-4">
                        <div>
                            <b>Rp. {{ number_format($schedule['price'], 0, ',', '.') }}</b>
                        </div>
                        <div class="d-flex gap-3 ps-3 my-2">
                            @foreach ($schedule['hours'] as $index => $hours)
                                {{-- this : mengirimkan element html ini ke js untuk di manipulasi --}}
                                <div class="btn btn-outline-secondary" style="cursor: pointer"
                                    onclick="selectedHour('{{ $schedule->id }}', '{{ $index }}', this)">
                                    {{ $hours }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <hr>
            @endforeach
        </div>
    </div>
    <div class="w-100 p-2 bg-light text-center fixed-bottom" id="wrapBtn">
        <a href="javascript:void(0)" id="btnTicket"><i class="fa-solid fa-ticket"></i> BELI TIKET</a>
    </div>
@endsection

@push('script')
    <script>
        let elementBefore = null;

        function selectedHour(scheduleId, hourId, el) {
            // jika element sebelumnya ada, dan skrng pindah ke element lain kliknya. ubah elementsebelumnya jadi putih lagi
            if (elementBefore) {
                // ubah styling css : stle.property
                elementBefore.style.background = "";
                elementBefore.style.color = "";
                //di kosongkan karna saat kita mencet akan hilang
                // property css kebab (border-color) di js adi camel (borderColor)
                elementBefore.style.borderColor = "";
            }
            // kasi warna ke element baru
            el.style.background = "#112646";
            el.style.color = "white";
            el.style.borderColor = "#112646";
            //update element sebelumnya pake element baru
            elementBefore = el;

            let wrapBtn = document.querySelector("#wrapBtn");
            let btnTicket = document.querySelector("#btnTicket");
            //kasi warna biru ke div wrap dan hilangkan warna abu
            // warna abu dari 'bg-light' class bootstrap
            wrapBtn.classList.remove('bg-light');
            wrapBtn.style.background = '#112646';
            //siapkan route
            let url = "{{ route('schedules.show-seats', ['scheduleId' => ':scheduleId', 'hourId' => ':hourId']) }}"
                .replace(':scheduleId', scheduleId) // ubah parameter (:) dengan value dari js
                .replace(':hourId', hourId);
            // isi url ke href btnTicket
            btnTicket.href = url;
            btnTicket.style.color = white;
        }
    </script>
@endpush
