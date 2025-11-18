@extends('templates.app')

@section('content')
    <div class="container card my-5 p-4">
        <h5 class="text-center">Selesaikan Pembayaran</h5>
        <img src="{{ asset('storage/' . $ticket['ticketPayment']['barcode']) }}" class="d-block mx-auto mb-4" alt="">
        <div class="w-50 d-block mx-auto">
            <div class="d-flex justify-content-between">
                <p>{{ $ticket['quantity'] }} Tiket</p>
                {{-- implode(',') : mengubah array jd string dipisahkan dengan koma --}}
                <p><b>{{ implode(', ', $ticket['row_of_seats']) }}</b></p>
            </div>
            <div class="d-flex justify-content-between">
                <p>Harga Tiket</p>
                <p><b>Rp. {{ number_format($ticket['schedule']['price'], 0, ',', '.') }} X {{ $ticket['quantity'] }}</b></p>
            </div>
            <div class="d-flex justify-content-between">
                <p>Biaya Layanan</p>
                <p><b>Rp. 4.000 X {{ $ticket['quantity'] }}</b></p>
            </div>
            <div class="d-flex justify-content-between">
                <p>Promo</p>
                @if ($ticket['promo_id'] !=NULL)
                    <p><b>{{ $ticket['promo']['type'] == 'percent' ? $ticket['promo']['discount'] . '%' : 'RP. ' . number_format($ticket['promo']['discount'], 0, ',', '.') }}</b></p>
                @else
                    <p><b>-</b></p>
                @endif
            </div>
            <hr>
            <div class="d-flex justify-content-between">
                @php
                    $price = $ticket['total_price'] + $ticket['service_fee'];
                @endphp
                <p>Harga Akhir</p>
                <b>Rp. {{ number_format($price, 0, ',', '.') }}</b>
            </div>
            <form action="{{ route('tickets.payment.proof', $ticket['id']) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-primary btn-lg btn-block">Sudah Dibayar</button>
            </form>
        </div>
    </div>
@endsection
