@extends('templates.app')

@section('content')
<div class="container card my-5 p-4">
    <div class="card-body">
        <b>{{ $schedule['cinema']['name'] }}</b>
        {{-- mengambil tgl hari ini : Carbon::now() --}}
        <b>{{ \Carbon\Carbon::now()->format('d, M, Y') }} || {{ $hour }}</b>

        <div class="d-flex justify-content-center">
            <div class="row w-50">
                <div class="d-flex col-4">
                    <div style="border-radius: 20%; width: 25px; height: 25px; background: #112646;"></div>
                    <p class="ms-2">Kursi Kosong</p>
                </div>
                <div class="d-flex col-4">
                    <div style="border-radius: 20%; width: 25px; height: 25px; background: #eaeaea;"></div>
                    <p class="ms-2">Kursi Terjual</p>
                </div>
                <div class="d-flex col-4">
                    <div style="border-radius: 20%; width: 25px; height: 25px; background: #3e85ef;"></div>
                    <p class="ms-2">Kursi Dipilih</p>
                </div>
            </div>
        </div>

        @php
            $row = range('A', 'H');
            $col = range(1, 18);
        @endphp
        {{-- looping untuk membuat baris AH --}}
        @foreach ($row as $baris)
        <div class="d-flex text-center justify-content-center my-1">
            @foreach ($col as $kursi)
                {{-- jika kursi nomor 7, tambahkan space kosong untuk jalan --}}
                @if ($kursi == 7)
                    <div style="width: 35px"></div>
                @endif

                @php
                    $seat = $baris . "-" . $kursi;
                @endphp
                @if (in_array($seat, $seatsFormat))
                    <div style="background: #eaeaea; border-radius: 10px; width: 45px; height: 45px; cursor: pointer;" class="p-2 mx-1 text-dark">
                    <span style="font-size: 12px;">{{ $baris }}-{{ $kursi }}</span>
                </div>
                @else

                {{-- munculkan A-1 A-2 dst --}}
                <div style="background: #112645; border-radius: 10px; width: 45px; height: 45px; cursor: pointer;" class="p-2 mx-1 text-white" onclick="selectedSeat('{{ $schedule->price }}', '{{ $baris }}', '{{ $kursi }}', this)">
                    <span style="font-size: 12px;">{{ $baris }}-{{ $kursi }}</span>
                </div>
                @endif
            @endforeach
        </div>
        @endforeach
    </div>
</div>

<div class="w-100 p-2 bg-light text-center fixed-bottom" id="wrapBtn">
    <b class="text-center p-3">Layar Bioskop</b>
    <div class="row" style="border: 1px solid #d1d1d1">
        <div class="col-6 text-center" style="border: 1px solid #d1d1d1">
            <p>Total Harga</p>
            <h5 id="totalPrice">Rp. -</h5>
        </div>
        <div class="col-6 text-center" style="border: 1px solid #d1d1d1">
            <p>Kursi Dipilh</p>
            <h5 id="selectedSeats">-</h5>
        </div>
    </div>
    {{-- Menyimpan value yang diperlukan untuk aksi ringkasan Pemesanan --}}
    <input type="hidden" name="user_id" value="{{ Auth::user()->id }}" id="user_id">
    <input type="hidden" name="schedule_id" value="{{ $schedule->id }}" id="schedule_id">
    <input type="hidden" name="hour" value="{{ $hour }}" id="hour">

    <div style="color: black; font-weight: bold; cursor: not-allowed;" class="w-100 text-center" id="btnOrder">Ringkasan Pemesanan</div>
</div>
@endsection

@push('script')
    <script>
        // array data kursi yg sudah Dipilih
        let seats = [];
        let totalPriceData = null;

        function selectedSeat(price, row, col, el) {
            // membuat A-1 sesuai row dan col yang dipilih
            let seatItem = row + "-" + col;
            // cek apakah kursi ini udah ada di array seats
            let indexSeat = seats.indexOf(seatItem);
            // jika ada akan muncul index nya jika gak ada A-1
            if (indexSeat == -1) {
                // kalau gak ada simpan kursi yg dipilih ke array
                seats.push(seatItem);
                // kasih warna biru muda ke kursi yang dipilih
                el.style.background = "#3e85ef";
            } else {
                // kalau ada di array artinya klik kali ini membatalkan pilihkan
                seats.splice(indexSeat, 1); // hapus item dari array
                // kembalikan warna ke biru Terjual
                el.style.background = "#112645";
            }

            // menghitung total harga sesuai kursi yang dipilih
            let totalPrice = price * (seats.length);
            // jumlah item array
            totalPriceData = totalPrice;
            let totalPriceEl = document.querySelector("#totalPrice");
            totalPriceEl.innerText = "Rp " + totalPrice.toLocaleString("id-ID");
            // memunculkan daftar kursi yang dipilih
            let selectedSeatsEl = document.querySelector("#selectedSeats")
            // seats.join(", ") mengubah array jadi string, dipisahkan dengan tanda tertentu
            selectedSeatsEl.innerText = seats.join(", ");

            // jika seats nya lebih dari/sama dengan 1 aktifkan order dan tambah fungsi onclick untuk proses data tiket
            let btnOrder = document.querySelector('#btnOrder');

            if (seats.length > 0) {
                btnOrder.style.background = '#112645';
                btnOrder.style.color = 'white';
                btnOrder.style.cursor = 'pointer';
                btnOrder.onclick = createTicketData;
            } else {
                btnOrder.style.background = '';
                btnOrder.style.color = '';
                btnOrder.style.cursor = '';
                btnOrder.onclick = null;
            }

            function createTicketData() {
                // AJAX = asynchronous js and xml, jika mau akses data ke server melalui js gunakan method ajax({}). bisa digunakan hanya melalui jquery ($)
                $.ajax({
                    url: "{{ route('tickets.store') }}", // routing untuk akses data
                    method: "POST", // http method
                    data: { // data yang akan dikirim (diambil pake Request $request)
                        _token: "{{ csrf_token() }}", // CSRF token
                        user_id: $("#user_id").val(),
                        schedule_id: $("#schedule_id").val(),
                        row_of_seats: seats,
                        quantity: seats.length,
                        total_price: totalPriceData,
                        hour: $("#hour").val()
                    },
                    success: function(response) {
                        // console.log(response)
                        // Jika berhasil menambahkan data, arahkan halaman ke tiket order (ringkasan order)
                        let ticketId = response.data.id;
                        window.location.href = `/tickets/${ticketId}/order`;
                    },
                    error: function(message) {
                        console.log(message);
                        alert("Terjadi kesalahan ketika membuat data tiket!");
                    }
                })
            }
        }
    </script>
@endpush
