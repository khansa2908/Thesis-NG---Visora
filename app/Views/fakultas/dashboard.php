<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>

<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dasbor Akademik</h1>
        <form action="" method="get" class="form-inline">
            <label class="mr-2 text-gray-800">Pilih Tahun Akademik:</label>
            <select name="filter_semester" class="form-control custom-select shadow-sm" onchange="this.form.submit()">
                <?php if (isset($list_semester)) : ?>
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
    // --- 1. HITUNG DATA TI ---
    $ti_sempro    = $ti_sempro ?? 0;
    $ti_prasidang = $ti_prasidang ?? 0;
    $ti_sidang    = $ti_sidang ?? 0;

    $total_ti = $ti_sempro + $ti_prasidang + $ti_sidang;
    if ($total_ti > 0) {
        $pct_ti_sempro = round(($ti_sempro / $total_ti) * 100, 1);
        $pct_ti_prasidang = round(($ti_prasidang / $total_ti) * 100, 1);
        $pct_ti_sidang = round(($ti_sidang / $total_ti) * 100, 1);
    } else {
        $pct_ti_sempro = $pct_ti_prasidang = $pct_ti_sidang = 0;
    }

    // --- 2. HITUNG DATA PDSI ---
    $pdsi_sempro    = $pdsi_sempro ?? 0;
    $pdsi_prasidang = $pdsi_prasidang ?? 0;
    $pdsi_sidang    = $pdsi_sidang ?? 0;

    $total_pdsi = $pdsi_sempro + $pdsi_prasidang + $pdsi_sidang;
    if ($total_pdsi > 0) {
        $pct_pdsi_sempro = round(($pdsi_sempro / $total_pdsi) * 100, 1);
        $pct_pdsi_prasidang = round(($pdsi_prasidang / $total_pdsi) * 100, 1);
        $pct_pdsi_sidang = round(($pdsi_sidang / $total_pdsi) * 100, 1);
    } else {
        $pct_pdsi_sempro = $pct_pdsi_prasidang = $pct_pdsi_sidang = 0;
    }
    ?>

    <div class="row">

        <div class="col-xl-6 col-lg-12 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status Tugas Akhir Prodi TI</h6>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-5 mb-3 mb-md-0">
                            <div class="chart-pie pt-2 pb-2" style="height: 250px;">
                                <canvas id="myPieChartTI"></canvas>
                            </div>
                            <div class="mt-2 text-center font-weight-bold small">
                                Total: <?= $total_ti ?> Mahasiswa
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm mb-0" width="100%" cellspacing="0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Status</th>
                                            <th class="text-center">Jml</th>
                                            <th class="text-center">%</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="small"><i class="fas fa-circle text-primary mr-1"></i> Sempro</td>
                                            <td class="text-center font-weight-bold small"><?= $ti_sempro ?></td>
                                            <td class="text-center small"><?= $pct_ti_sempro ?>%</td>
                                        </tr>
                                        <tr>
                                            <td class="small"><i class="fas fa-circle text-warning mr-1"></i> Pra-Sidang</td>
                                            <td class="text-center font-weight-bold small"><?= $ti_prasidang ?></td>
                                            <td class="text-center small"><?= $pct_ti_prasidang ?>%</td>
                                        </tr>
                                        <tr>
                                            <td class="small"><i class="fas fa-circle text-success mr-1"></i> Sidang</td>
                                            <td class="text-center font-weight-bold small"><?= $ti_sidang ?></td>
                                            <td class="text-center small"><?= $pct_ti_sidang ?>%</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-12 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Status Tugas Akhir Prodi PdSI</h6>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-5 mb-3 mb-md-0">
                            <div class="chart-pie pt-2 pb-2" style="height: 250px;">
                                <canvas id="myPieChartPdSI"></canvas>
                            </div>
                            <div class="mt-2 text-center font-weight-bold small">
                                Total: <?= $total_pdsi ?> Mahasiswa
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm mb-0" width="100%" cellspacing="0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Status</th>
                                            <th class="text-center">Jml</th>
                                            <th class="text-center">%</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="small"><i class="fas fa-circle text-primary mr-1"></i> Sempro</td>
                                            <td class="text-center font-weight-bold small"><?= $pdsi_sempro ?></td>
                                            <td class="text-center small"><?= $pct_pdsi_sempro ?>%</td>
                                        </tr>
                                        <tr>
                                            <td class="small"><i class="fas fa-circle text-warning mr-1"></i> Pra-Sidang</td>
                                            <td class="text-center font-weight-bold small"><?= $pdsi_prasidang ?></td>
                                            <td class="text-center small"><?= $pct_pdsi_prasidang ?>%</td>
                                        </tr>
                                        <tr>
                                            <td class="small"><i class="fas fa-circle text-success mr-1"></i> Sidang</td>
                                            <td class="text-center font-weight-bold small"><?= $pdsi_sidang ?></td>
                                            <td class="text-center small"><?= $pct_pdsi_sidang ?>%</td>
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
                    <h6 class="m-0 font-weight-bold text-primary">Beban Pembimbing Prodi TI</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar" style="height: 350px;">
                        <canvas id="myBarChartTI"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Beban Pembimbing Prodi PdSI</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar" style="height: 350px;">
                        <canvas id="myBarChartPdSI"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Beban Reviewer Sempro Prodi TI</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar" style="height: 350px;">
                        <canvas id="myBarChartReviewerTI"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">Beban Reviewer Sempro Prodi PdSI</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar" style="height: 350px;">
                        <canvas id="myBarChartReviewerPdSI"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-secondary">Beban Reviewer Pra-Sidang Prodi TI</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar" style="height: 350px;">
                        <canvas id="myBarChartPraTI"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">Beban Reviewer Pra-Sidang Prodi PdSI</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar" style="height: 350px;">
                        <canvas id="myBarChartPraPdSI"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-dark">Beban Penguji Sidang Akhir Prodi TI</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar" style="height: 350px;">
                        <canvas id="myBarChartSidangTI"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-dark">Beban Penguji Sidang Akhir Prodi PdSI</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar" style="height: 350px;">
                        <canvas id="myBarChartSidangPdSI"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // ==========================================
    // 1. CONFIG DOUGHNUT CHARTS (Status TA)
    // ==========================================
    var dataTI_Values = [
        <?= $ti_sempro ?? 0 ?>,
        <?= $ti_prasidang ?? 0 ?>,
        <?= $ti_sidang ?? 0 ?>
    ];

    var dataPdSI_Values = [
        <?= $pdsi_sempro ?? 0 ?>,
        <?= $pdsi_prasidang ?? 0 ?>,
        <?= $pdsi_sidang ?? 0 ?>
    ];

    function createDoughnutChart(canvasId, dataValues) {
        var canvas = document.getElementById(canvasId);
        if (!canvas) return;

        new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels: ["Seminar Proposal", "Seminar Prasidang", "Sidang Skripsi"],
                datasets: [{
                    data: dataValues,
                    backgroundColor: ['#4e73df', '#f6c23e', '#1cc88a'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: true
                    }
                }
            },
        });
    }

    createDoughnutChart("myPieChartTI", dataTI_Values);
    createDoughnutChart("myPieChartPdSI", dataPdSI_Values);


    // ==========================================
    // 2. FUNGSI UTAMA BAR CHART (WARNA-WARNI A-Z)
    // ==========================================

    // Palet Warna untuk Bar Chart
    const palette = [
        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
        '#858796', '#5a5c69', '#6610f2', '#e83e8c', '#fd7e14',
        '#20c9a6', '#d63384', '#6f42c1', '#0d6efd', '#198754'
    ];

    function getColors(dataCount) {
        let colors = [];
        for (let i = 0; i < dataCount; i++) {
            colors.push(palette[i % palette.length]);
        }
        return colors;
    }

    function createBarChart(canvasId, labels, data, title) {
        var canvas = document.getElementById(canvasId);
        if (!canvas) return;

        if (labels.length === 0) {
            canvas.parentElement.innerHTML = "<p class='text-center mt-5 small text-gray-500'>Belum ada data " + title + ".</p>";
            return;
        }

        // Generate warna pelangi
        let dynamicColors = getColors(labels.length);
        const maxValue = Math.max(...data);

        new Chart(canvas, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: title,
                    data: data,
                    backgroundColor: dynamicColors, // <--- Warna Warni!
                    borderColor: dynamicColors,
                    borderWidth: 1
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        min: 0,
                        max: maxValue + 1,
                        ticks: {
                            stepSize: 1,
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.raw;
                            }
                        }
                    }
                }
            }
        });
    }


    // ==========================================
    // 3. RENDER BAR CHARTS: PEMBIMBING (US 34)
    // ==========================================

    createBarChart("myBarChartTI", <?= $label_dosen_ti ?>, <?= $value_dosen_ti ?>.map(Number), "Beban Pembimbing TI");
    createBarChart("myBarChartPdSI", <?= $label_dosen_pdsi ?>, <?= $value_dosen_pdsi ?>.map(Number), "Beban Pembimbing PdSI");


    // ==========================================
    // 4. RENDER BAR CHARTS: REVIEWER SEMPRO (US 35)
    // ==========================================

    createBarChart("myBarChartReviewerTI", <?= $label_reviewer_ti ?>, <?= $value_reviewer_ti ?>.map(Number), "Reviewer Sempro TI");
    createBarChart("myBarChartReviewerPdSI", <?= $label_reviewer_pdsi ?>, <?= $value_reviewer_pdsi ?>.map(Number), "Reviewer Sempro PdSI");


    // ==========================================
    // 5. RENDER BAR CHARTS: REVIEWER PRA-SIDANG (US 36)
    // ==========================================

    createBarChart("myBarChartPraTI", <?= $label_pra_ti ?>, <?= $value_pra_ti ?>.map(Number), "Reviewer Pra-Sidang TI");
    createBarChart("myBarChartPraPdSI", <?= $label_pra_pdsi ?>, <?= $value_pra_pdsi ?>.map(Number), "Reviewer Pra-Sidang PdSI");


    // ==========================================
    // 6. RENDER BAR CHARTS: PENGUJI SIDANG AKHIR (US 37)
    // ==========================================

    createBarChart("myBarChartSidangTI", <?= $label_sidang_ti ?>, <?= $value_sidang_ti ?>.map(Number), "Penguji Sidang TI");
    createBarChart("myBarChartSidangPdSI", <?= $label_sidang_pdsi ?>, <?= $value_sidang_pdsi ?>.map(Number), "Penguji Sidang PdSI");
</script>

<?= $this->endSection(); ?>