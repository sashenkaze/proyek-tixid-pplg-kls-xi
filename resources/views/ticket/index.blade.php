@extends('templates.app')

@section('content')
    <div class="container card my-5 p-4">
        <div class="card-body">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane"
                        type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Tiket
                        Aktif</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane"
                        type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Tiket
                        Kadaluarsa</button>
                </li>

            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab"
                    tabindex="0">
                    <h5 class="my-4">Data Tiket Aktif, {{ Auth::user()->name }}</h5>
                    @forelse ($ticketActive as $ticket)
                        @foreach ($ticket->row_of_seats as $seat)
                            <div class="w-100 mb-3 p-3 border rounded">
                                <p class="text-end"><b>{{ $ticket->schedule->cinema->name }}</b></p>
                                <hr>
                                <b>{{ $ticket->schedule->movie->title }}</b>
                                <p>Tanggal :
                                    {{ \Carbon\Carbon::parse($ticket->ticketPayment->booked_date)->format('d F, Y') }}</p>
                                <p>Waktu : {{ \Carbon\Carbon::parse($ticket->hour)->format('H:i') }}</p>
                                <p>Kursi : {{ $seat }}</p>
                                <p>Harga Tiket : Rp {{ number_format($ticket->schedule->price, 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    @empty
                        <p class="text-muted">Tidak ada tiket aktif.</p>
                    @endforelse
                    
                </div>
                <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab"
                    tabindex="0">
                    <h5 class="my-4">Data Tiket Kadaluarsa, {{ Auth::user()->name }}</h5>
                    @forelse ($ticketNonActive as $ticket)
                        @foreach ($ticket->row_of_seats as $seat)
                            <div class="w-100 mb-3 p-3 border rounded bg-light">
                                <p class="text-end"><b>{{ $ticket->schedule->cinema->name }}</b></p>
                                <hr>
                                <b>{{ $ticket->schedule->movie->title }}</b>
                                <p>Tanggal :
                                    {{ \Carbon\Carbon::parse($ticket->ticketPayment->booked_date)->format('d F, Y') }}</p>
                                <p>Waktu : {{ \Carbon\Carbon::parse($ticket->hour)->format('H:i') }}</p>
                                <p>Kursi : {{ $seat }}</p>
                                <p>Harga Tiket : Rp {{ number_format($ticket->schedule->price, 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    @empty
                        <p class="text-muted">Tidak ada tiket kadaluarsa.</p>
                    @endforelse

                </div>
            </div>
        </div>
    </div>
@endsection
