<?= $this->extend("layout/template"); ?>

<?= $this->section("content"); ?>

<?php if (session()->getFlashdata("message")) : ?>
    <div id="flashdata" data-open="true">
        <p id="icon" hidden><?= session()->getFlashdata("message")["icon"]; ?></p>
        <p id="title" hidden><?= session()->getFlashdata("message")["title"]; ?></p>
        <p id="text" hidden><?= session()->getFlashdata("message")["text"]; ?></p>
    </div>
<?php endif; ?>

<h1 class="h3 mb-2 text-gray-800"><?= $title ?></h1>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between">
        <?= $title ?>
        
        <?php if ($detail['status'] == 'TERTUNDA'): ?>
            <span class="p-2 badge badge-secondary">TERTUNDA</span>
        <?php elseif ($detail['status'] == 'DITOLAK'): ?>
            <span class="p-2 badge badge-danger">DITOLAK</span>
        <?php elseif ($detail['status'] == 'DISETUJUI'): ?>
            <span class="p-2 badge badge-success">DISETUJUI</span>
        <?php endif; ?>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-lg-6">
                <div class="form-group">
                    <label for="nama">Nama Mahasiswa</label>
                    <input type="text" class="form-control" id="nama" name="nama" value="<?= $detail['nama_mhs'] ?>" disabled>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="form-group">
                    <label for="npm">NPM</label>
                    <input type="text" class="form-control" id="npm" name="npm" value="<?= $detail['npm'] ?>" disabled>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="form-group">
                    <label for="prodi">Program Studi</label>
                    <input type="text" class="form-control" id="prodi" name="prodi" value="<?= $detail['nama_prodi'] ?>" disabled>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="form-group">
                    <label for="judul">Judul Skripsi</label>
                    <textarea name="judul" id="judul" rows="2" class="form-control" disabled><?= $detail['judul'] ?></textarea>
                </div>
            </div>

            <?php if ($detail['status'] == 'DITOLAK'): ?>
                <div class="col-lg-12">
                    <div class="alert alert-danger">
                        <strong>Alasan Penolakan:</strong> <br>
                        <?= $detail['komentar_penolakan'] ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="col-lg-12"><hr></div>

            <div class="col-lg-12 mb-3">
                <div class="form-group">
                    <label for="file_hardcover" class="font-weight-bold">File Draft Hard Cover</label>
                    <div class="embed-responsive embed-responsive-16by9 mx-auto" id="file_hardcover" style="width: 90%; border: 1px solid #ddd;">
                        <iframe class="embed-responsive-item" src="<?= base_url("folderHardcover/" . $detail['file_hardcover']) ?>"></iframe>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 d-flex justify-content-end mt-3">
                <a role="button" href="<?= base_url("TenagaKependidikan/verifikasiHardcover") ?>" class="btn btn-secondary mr-2">Kembali</a>

                <?php if ($detail['status'] == 'TERTUNDA'): ?>
                    <button type="button" class="btn btn-danger mr-2" data-toggle="modal" data-target="#modalTolak">
                        Tolak Pengajuan
                    </button>

                    <form action="<?= base_url("TenagaKependidikan/setujuiHardcover/" . $detail['id']) ?>" method="post">
                        <?= csrf_field() ?>
                        <button class="btn btn-success" type="submit" onclick="return confirm('Apakah Anda yakin data ini sudah sesuai?')">Setujui Pengajuan</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTolak" tabindex="-1" role="dialog" aria-labelledby="modalTolakLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalTolakLabel">Tolak Pengajuan Hard Cover</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url("TenagaKependidikan/tolakHardcover/" . $detail['id']) ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="komentar_penolakan">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="komentar_penolakan" rows="4" required placeholder="Contoh: Format margin salah, cover depan tidak sesuai standar..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Kirim Penolakan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>