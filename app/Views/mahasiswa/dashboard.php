<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .stepper-container { display: flex; justify-content: space-between; align-items: flex-start; margin: 40px 0; }
        .stepper-item { flex: 1; text-align: center; position: relative; }
        .stepper-item:not(:last-child):after { content: ""; position: absolute; top: 20px; left: 50%; width: 100%; height: 2px; background: #ddd; z-index: 1; }
        .stepper-item.completed:after { background: #28a745; }
        .step-circle { width: 40px; height: 40px; background: #fff; border: 2px solid #ddd; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; position: relative; z-index: 2; font-weight: bold; }
        .stepper-item.active .step-circle { border-color: #007bff; color: #007bff; box-shadow: 0 0 10px rgba(0,123,255,0.3); }
        .stepper-item.completed .step-circle { background: #28a745; border-color: #28a745; color: white; }
        .step-text { font-size: 10px; font-weight: 600; color: #777; text-transform: uppercase; }
    </style>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h3>Selamat Datang, Mahasiswa!</h3>
            <p class="text-muted">Berikut adalah progres pengajuan skripsi Anda saat ini.</p>
            
            <div class="card shadow border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Status Pengajuan Skripsi</h5>
                </div>
                <div class="card-body">
                    <?php if($current_step > 0): ?>
                        <div class="stepper-container">
                            <?php foreach ($tahapan as $index => $nama): 
                                $num = $index + 1;
                                $class = ($current_step > $num) ? 'completed' : (($current_step == $num) ? 'active' : '');
                            ?>
                                <div class="stepper-item <?= $class ?>">
                                    <div class="step-circle"><?= ($current_step > $num) ? '✓' : $num ?></div>
                                    <div class="step-text"><?= $nama ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="alert alert-info py-2">
                            <strong>Info:</strong> Anda saat ini berada pada tahap: <?= $tahapan[$current_step-1] ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">Data skripsi aktif tidak ditemukan. Silakan hubungi admin prodi.</div>
                    <?php endif; ?>
                </div>
            </div>
            
        </div>
    </div>
</div>

</body>
</html>