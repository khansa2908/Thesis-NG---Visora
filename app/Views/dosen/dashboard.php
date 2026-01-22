<?= $this->extend("layout/template"); ?>

<?= $this->section("content"); ?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard Beban Kerja</h1>
</div>

<div class="row">

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Bimbingan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $statistik['total'] ?> Mahasiswa</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Seminar Proposal</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $statistik['sempro'] ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Seminar Prasidang</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $statistik['prasidang'] ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Sidang Skripsi</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $statistik['sidang'] ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">

    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Sebaran Bimbingan (Pie Chart)</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="myPieChart"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    <?php 
                        // Hitung Persentase di PHP agar aman
                        $total = $statistik['total'];
                        $perSempro = $total > 0 ? round(($statistik['sempro'] / $total) * 100, 1) : 0;
                        $perPrasidang = $total > 0 ? round(($statistik['prasidang'] / $total) * 100, 1) : 0;
                        $perSidang = $total > 0 ? round(($statistik['sidang'] / $total) * 100, 1) : 0;
                    ?>
                    
                    <span class="mr-2">
                        <i class="fas fa-circle text-info"></i> Sempro (<?= $perSempro ?>%)
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-warning"></i> Prasidang (<?= $perPrasidang ?>%)
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-success"></i> Sidang (<?= $perSidang ?>%)
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Status Mahasiswa (Column Chart)</h6>
            </div>
            <div class="card-body">
                <div class="chart-bar">
                    <canvas id="myBarChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section("scripts"); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

<script>
    // Registrasi Plugin agar bisa dipakai
    Chart.register(ChartDataLabels);

    // Set default font
    Chart.defaults.font.family = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.color = '#858796';

    // Ambil data dari PHP
    var dataSempro = <?= $statistik['sempro'] ?>;
    var dataPrasidang = <?= $statistik['prasidang'] ?>;
    var dataSidang = <?= $statistik['sidang'] ?>;

    // --- 1. PIE CHART (Menampilkan Persen Langsung) ---
    var ctxPie = document.getElementById("myPieChart");
    var myPieChart = new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: ["Seminar Proposal", "Seminar Prasidang", "Sidang Skripsi"],
            datasets: [{
                data: [dataSempro, dataPrasidang, dataSidang],
                backgroundColor: ['#36b9cc', '#f6c23e', '#1cc88a'],
                hoverBackgroundColor: ['#2c9faf', '#dda20a', '#17a673'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            layout: {
                padding: 20
            },
            plugins: {
                // Konfigurasi DataLabels (Angka di Chart)
                datalabels: {
                    color: '#fff', // Warna teks putih
                    textStrokeColor: '#000', // Warna outline (Hitam)
                    textStrokeWidth: 2, // Ketebalan outline (sesuaikan jika kurang tebal)
                    font: {
                        weight: 'bold',
                        size: 14
                    },
                    formatter: (value, ctx) => {
                        // Rumus Menghitung Persen Otomatis
                        let sum = 0;
                        let dataArr = ctx.chart.data.datasets[0].data;
                        dataArr.map(data => {
                            sum += data;
                        });
                        // Jika data 0, jangan tampilkan apa-apa biar rapi
                        if (value === 0) return null;
                        
                        let percentage = (value * 100 / sum).toFixed(1) + "%";
                        return percentage;
                    }
                },
                tooltip: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: true,
                    caretPadding: 10,
                },
                legend: {
                    display: false
                }
            },
            cutout: '65%',
        },
    });

    // --- 2. BAR/COLUMN CHART (Menampilkan Angka Langsung) ---
    var ctxBar = document.getElementById("myBarChart");
    var myBarChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: ["Seminar Proposal", "Seminar Prasidang", "Sidang Skripsi"],
            datasets: [{
                label: "Jumlah Mahasiswa",
                backgroundColor: "#4e73df",
                hoverBackgroundColor: "#2e59d9",
                borderColor: "#4e73df",
                data: [dataSempro, dataPrasidang, dataSidang],
            }],
        },
        options: {
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 25,
                    top: 25, // Tambah padding atas agar angka tidak kepotong
                    bottom: 0
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 6
                    },
                },
                y: {
                    ticks: {
                        maxTicksLimit: 5,
                        padding: 10,
                        callback: function(value) { return value; } // Format angka biasa
                    },
                    grid: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                },
            },
            plugins: {
                // Konfigurasi DataLabels (Angka di Atas Batang)
                datalabels: {
                    anchor: 'end', // Posisi di ujung batang
                    align: 'top',  // Di atas batang
                    color: '#4e73df', // Warna teks biru (sesuai tema)
                    font: {
                        weight: 'bold'
                    },
                    formatter: function(value) {
                        return value > 0 ? value : ''; // Hanya tampilkan jika > 0
                    }
                },
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                },
            }
        }
    });
</script>
<?= $this->endSection(); ?>