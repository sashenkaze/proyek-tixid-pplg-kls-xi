@extends('templates.app')

@section('content')
    <div class="container mt-5">
        <h5 class="mb-5">Grafik Pembelian Tiket</h5>
        <div class="row">
            <div class="col-6">
                <h5>Data Pembelian Tiket Bulan {{ now()->format('F') }}</h5><canvas id="chartBar"></canvas>
            </div>
            <div class="col-6">
                <h5>Data Film Berdasarkan Status</h5><canvas id="chartPie" style="margin: auto;"></canvas>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        let labelBar = [];
        let dataBar = [];
        let labelPie = [];
        let dataPie = [];

        // ketika html selesai di render, jalankan fungsi js disini
        $(function() {
            $.ajax({
                url: "{{ route('admin.tickets.chart') }}",
                method: "GET",
                success: function(response) {
                    labelBar = response.labels;
                    dataBar = response.data;

                    chartBar();
                },
                error: function(err) {
                    alert('Gagal mengambil data untuk chart Bar!');
                }
            });

            $.ajax({
                url: "{{ route('admin.movies.chart') }}",
                method: "GET",
                success: function(response) {
                    labelPie = response.labels;
                    dataPie = response.data;
                    chartPie();
                },
                error: function(err) {
                    alert('Gagal mengambil data untuk chart Pie!');
                }
            });
        });
        const ctx = document.getElementById('chartBar');
        function chartBar() {
            new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labelBar,
                datasets: [{
                    label: 'Penjualan Tiket Bulan Ini',
                    data: dataBar,
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
            });
        }

        const ctx2 = document.getElementById('chartPie');
        function chartPie() {
            new Chart(ctx2, {
                type: 'pie',
                data: {
                    labels: labelPie,
                    datasets: [{
                        label: 'Data Film Berdasarkan Status',
                        data: dataPie,
                        backgroundColor: [
                            'rgb(54, 162, 235)',
                            'rgb(255, 99, 132)'
                        ],
                        hoverOffset: 4
                    }]
                },
            });
        }
    </script>
@endpush

{{-- @push('script') --}}
    {{-- <script>
        let labelBar = [];
        let dataBar = [];
        $(function() {
            $.ajax({
                url: "{{ route('admin.tickets.chart') }}",
                method: "GET",
                success: function(response) {
                    console.log(response)
                    labelBar = response.labels;
                    dataBar = response.data;
                    chartBar();
                },
                error: function(err) {
                    alert('Gagal ambil data grafik!');
                }
            });
        });

        const ctx = document.getElementById('chartBar');

        function chartBar() {
            new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labelBar,
                            datasets: [{
                                label: 'Penjualan Tiket Bulan Ini',
                                data: dataBar,
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            },
                        }
                    });

                    new Chart(document.getElementById('chartPie'), {
                        type: 'pie',
                        data: {
                            labels: ['Aktif', 'Tidak Aktif'],
                            datasets: [{
                                label: 'Penjualan Tiket Bulan Ini',
                                data: dataBar,
                                borderWidth: 1;
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
        }
    </script> --}}
{{-- @endpush --}}
