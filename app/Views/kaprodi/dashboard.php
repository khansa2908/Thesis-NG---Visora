<?= $this->extend('layout/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title; ?></h1>

        <form action="" method="get" class="form-inline">
            <label class="mr-2 text-gray-800">Pilih Tahun Akademik:</label>
            <select name="filter_semester" class="form-control custom-select shadow-sm" onchange="this.form.submit()">
                <?php if (!empty($list_semester)) : ?>
                    <?php foreach ($list_semester as $sem) : ?>
                        <option value="<?= $sem['kode'] ?>" <?= ($selected_semester == $sem['kode']) ? 'selected' : '' ?>>
                            <?= $sem['nama'] ?>
                        </option>
                    <?php endforeach; ?>
                <?php else : ?>
                    <option value="">Data Semester Tidak Ditemukan</option>
                <?php endif; ?>
            </select>
        </form>
    </div>

    <?php
    // --- LOGIC HITUNG PERSEN DI SINI ---
    $total_data = $pie_sempro + $pie_prasidang + $pie_sidang;

    // Cek biar gak error division by zero
    if ($total_data > 0) {
        $p_sempro    = round(($pie_sempro / $total_data) * 100, 1);
        $p_prasidang = round(($pie_prasidang / $total_data) * 100, 1);
        $p_sidang    = round(($pie_sidang / $total_data) * 100, 1);
    } else {
        $p_sempro = $p_prasidang = $p_sidang = 0;
    }
    ?>

    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status Tugas Akhir Mahasiswa</h6>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">

                        <div class="col-lg-5 col-md-6 mb-4 mb-md-0">
                            <div class="chart-pie pt-4 pb-2">
                                <canvas id="myPieChart"></canvas>
                            </div>
                            <div class="mt-3 text-center font-weight-bold text-gray-800">
                                Total: <?= $total_data; ?> Mahasiswa
                            </div>
                        </div>

                        <div class="col-lg-7 col-md-6">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-0" width="100%" cellspacing="0">
                                    <thead class="bg-light text-gray-900">
                                        <tr>
                                            <th>Status</th>
                                            <th class="text-center" width="20%">Jumlah</th>
                                            <th class="text-center" width="20%">Persen</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><i class="fas fa-circle text-primary mr-2"></i> Seminar Proposal</td>
                                            <td class="text-center font-weight-bold"><?= $pie_sempro ?></td>
                                            <td class="text-center"><?= $p_sempro ?>%</td>
                                        </tr>
                                        <tr>
                                            <td><i class="fas fa-circle text-warning mr-2"></i> Seminar Pra-Sidang</td>
                                            <td class="text-center font-weight-bold"><?= $pie_prasidang ?></td>
                                            <td class="text-center"><?= $p_prasidang ?>%</td>
                                        </tr>
                                        <tr>
                                            <td><i class="fas fa-circle text-success mr-2"></i> Sidang Skripsi</td>
                                            <td class="text-center font-weight-bold"><?= $pie_sidang ?></td>
                                            <td class="text-center"><?= $p_sidang ?>%</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-dark">Beban Pembimbing</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar"><canvas id="barPembimbing"></canvas></div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-dark">Beban Reviewer Sempro</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar"><canvas id="barSempro"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-dark">Beban Reviewer Pra-Sidang</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar"><canvas id="barPra"></canvas></div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-dark">Beban Penguji Sidang Akhir</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar"><canvas id="barSidang"></canvas></div>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="<?= base_url('assets/vendor/chart.js/Chart.min.js'); ?>"></script>
<script>
    // --- 1. SETTING PIE CHART (DOUGHNUT) ---
    var ctxPie = document.getElementById("myPieChart");
    var myPieChart = new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: ["Sempro", "Pra-Sidang", "Sidang Akhir"],
            datasets: [{
                data: [<?= $pie_sempro ?>, <?= $pie_prasidang ?>, <?= $pie_sidang ?>],
                backgroundColor: ['#4e73df', '#f6c23e', '#1cc88a'],
                hoverBackgroundColor: ['#2e59d9', '#dda20a', '#17a673'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
            },
            legend: {
                display: false
            },
            cutoutPercentage: 70,
        },
    });

    // --- 2. PALET WARNA (BIAR WARNA-WARNI KAYAK PERMINTAAN DOSEN) ---
    // Ini daftar warna yang bakal dipake gantian
    const palette = [
        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
        '#858796', '#5a5c69', '#6610f2', '#e83e8c', '#fd7e14',
        '#20c9a6', '#d63384', '#6f42c1', '#0d6efd', '#198754'
    ];

    function getColors(dataCount) {
        let colors = [];
        for (let i = 0; i < dataCount; i++) {
            // Ambil warna dari palette secara berulang kalau data lebih banyak dari warna
            colors.push(palette[i % palette.length]);
        }
        return colors;
    }

    // --- 3. FUNGSI BAR CHART WARNA-WARNI ---
    function buatBarChart(idCanvas, labels, data, judulTooltip) {
        if (labels.length === 0) {
            document.getElementById(idCanvas).parentElement.innerHTML = "<p class='text-center mt-4'>Belum ada data.</p>";
            return;
        }

        // Generate warna sebanyak jumlah data dosen
        let dynamicColors = getColors(labels.length);

        new Chart(document.getElementById(idCanvas), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: judulTooltip,
                    // Di sini kuncinya: backgroundColor kita isi Array warna-warni
                    backgroundColor: dynamicColors,
                    hoverBackgroundColor: dynamicColors,
                    borderColor: dynamicColors,
                    data: data,
                }],
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    xAxes: [{
                        gridLines: {
                            display: false,
                            drawBorder: false
                        },
                        maxBarThickness: 35 // Bar agak tebal dikit biar enak dilihat
                    }],
                    yAxes: [{
                        ticks: {
                            min: 0,
                            padding: 10,
                            stepSize: 1,
                            beginAtZero: true
                        },
                        gridLines: {
                            color: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2]
                        }
                    }],
                },
                legend: {
                    display: false
                }, // Legend disembunyikan karena label dosen ada di bawah
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, chart) {
                            return judulTooltip + ': ' + tooltipItem.yLabel;
                        }
                    }
                }
            }
        });
    }

    // --- 4. EKSEKUSI 4 BAR CHART ---
    // Bar Pembimbing
    buatBarChart("barPembimbing", <?= $lbl_pembimbing ?>, <?= $val_pembimbing ?>.map(Number), "Jml Bimbingan");

    // Bar Sempro
    buatBarChart("barSempro", <?= $lbl_sempro ?>, <?= $val_sempro ?>.map(Number), "Jml Menguji");

    // Bar Pra-Sidang
    buatBarChart("barPra", <?= $lbl_pra ?>, <?= $val_pra ?>.map(Number), "Jml Menguji");

    // Bar Sidang Akhir
    buatBarChart("barSidang", <?= $lbl_sidang ?>, <?= $val_sidang ?>.map(Number), "Jml Menguji");
</script>

<?= $this->endSection(); ?>