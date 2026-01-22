<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">History Notifikasi</h1>
    
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Mahasiswa</th>
                            <th>Keterangan</th>
                            <th>Status Baca</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($skripsi as $s): ?>
                        <tr class="<?= ($s['is_read_admin'] == 1) ? 'text-muted' : 'font-weight-bold' ?>">
                            <td><?= $s['nama'] ?></td>
                            <td>Pengajuan Penulisan Skripsi</td>
                            <td>
                                <?php if($s['is_read_admin'] == 0): ?>
                                    <span class="badge badge-danger">Baru</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Sudah Dilihat</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>

                        <?php foreach($prasidang as $p): ?>
                        <tr class="<?= ($p['is_read_admin'] == 1) ? 'text-muted' : 'font-weight-bold' ?>">
                            <td><?= $p['nama'] ?></td>
                            <td>Pengajuan Prasidang</td>
                            <td>
                                <?php if($p['is_read_admin'] == 0): ?>
                                    <span class="badge badge-danger">Baru</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Sudah Dilihat</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>

                        <?php foreach($sidang as $sd): ?>
                        <tr class="<?= ($sd['is_read_admin'] == 1) ? 'text-muted' : 'font-weight-bold' ?>">
                            <td><?= $sd['nama'] ?></td>
                            <td>Pengajuan Sidang Skripsi</td>
                            <td>
                                <?php if($sd['is_read_admin'] == 0): ?>
                                    <span class="badge badge-danger">Baru</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Sudah Dilihat</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>

                        <?php if(empty($skripsi) && empty($prasidang) && empty($sidang)): ?>
                        <tr>
                            <td colspan="3" class="text-center">Belum ada history notifikasi.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>