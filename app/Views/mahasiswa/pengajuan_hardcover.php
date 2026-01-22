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

<div class="row">
    <div class="col-lg-12">
        <?php if ($skripsi == null || $skripsi['status'] != 'Lulus'): ?>
            <div class="alert alert-danger" role="alert">
                <small>
                    Anda belum dinyatakan <strong>LULUS</strong> Sidang Skripsi. Silahkan selesaikan proses sidang terlebih dahulu sebelum mengunggah Hard Cover.
                </small>
            </div>
        <?php elseif (empty($hardcoverList)): ?>
            <div class="alert alert-info" role="alert">
                <small>
                    Silahkan kumpulkan file <strong>Hard Cover (Draft Final)</strong> pada halaman ini untuk diperiksa oleh Tendik.
                </small>
            </div>
        <?php else: ?>
            <?php $latest = $hardcoverList[0]; ?>
            
            <?php if($latest['status'] == 'DISETUJUI'): ?>
                <div class="alert alert-success" role="alert">
                    <small>
                        <strong>Hard Cover</strong> anda telah <strong>Disetujui</strong>. Proses administrasi kelulusan selesai.
                    </small>
                </div>
            <?php elseif($latest['status'] == 'DITOLAK'): ?>
                <div class="alert alert-danger" role="alert">
                    <small>
                        <strong>Hard Cover</strong> anda <strong>Ditolak</strong>. Silahkan perbaiki sesuai catatan revisi dan unggah ulang.
                    </small>
                </div>
            <?php else: ?>
                <div class="alert alert-warning" role="alert">
                    <small>
                        <strong>Hard Cover</strong> sedang dalam proses <strong>Verifikasi</strong> oleh Tenaga Kependidikan. Harap cek berkala.
                    </small>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <?= $title ?>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-lg-3 d-flex align-items-center">
                <?php 
                    $allowUpload = false;
                    if ($skripsi != null && $skripsi['status'] == 'Lulus') {
                        if (empty($hardcoverList)) {
                            $allowUpload = true;
                        } else {
                            // Jika status terakhir DITOLAK, boleh upload lagi (Revisi)
                            if ($hardcoverList[0]['status'] == 'DITOLAK') {
                                $allowUpload = true;
                            }
                        }
                    }
                ?>

                <?php if ($allowUpload) : ?>
                    <button class="btn btn-primary" data-toggle="modal" data-target="#tambahHardcover">Tambahkan Hard Cover</button>
                <?php else: ?>
                    <button class="btn btn-primary" disabled>Tambahkan Hard Cover</button>
                <?php endif; ?>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered" id="tabelHardcover" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul Skripsi</th>
                        <th>Tanggal Upload</th>
                        <th>Status</th>
                        <th>Catatan Revisi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach($hardcoverList as $hc) : ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td style="min-width: 20vw"><?= $skripsi['judul'] ?></td>
                            <td><?= date('d M Y H:i', strtotime($hc['tanggal_pengajuan'])) ?></td>
                            <td>
                                <?php if($hc['status'] == 'DISETUJUI'): ?>
                                    <span class="badge badge-success">DISETUJUI</span>
                                <?php elseif($hc['status'] == 'DITOLAK'): ?>
                                    <span class="badge badge-danger">DITOLAK</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">TERTUNDA</span>
                                <?php endif; ?>
                            </td>
                            <td><?= ($hc['komentar_penolakan']) ? $hc['komentar_penolakan'] : '-' ?></td>
                            <td>
                                <a href="<?= base_url('folderHardcover/' . $hc['file_hardcover']) ?>" target="_blank" class="btn btn-info btn-sm" title="Lihat File">
                                    <i class="fas fa-file-pdf"></i> Lihat
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="tambahHardcover" tabindex="-1" role="dialog" aria-labelledby="tambahHardcoverLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahHardcoverLabel">Unggah Hard Cover</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url("Mahasiswa/insertHardcover") ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id_skripsi" value="<?= ($skripsi) ? $skripsi['id'] : '' ?>">
                    <input type="hidden" name="npm" value="<?= $mahasiswa['npm'] ?>">

                    <div class="form-group">
                        <label>Judul Skripsi</label>
                        <textarea class="form-control" rows="3" disabled><?= ($skripsi) ? $skripsi['judul'] : '-' ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>File Hard Cover (PDF)</label>
                        <small class="text-muted d-block">Unggah file lengkap (Cover s/d Lampiran) dalam format PDF (Max 10MB)</small>
                        <div class="input-group mt-2">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="file_hardcover" name="file_hardcover" accept=".pdf" required>
                                <label class="custom-file-label" for="file_hardcover">Pilih File...</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section("scripts"); ?>
<script>
    $(document).ready(function() {
        $('#tabelHardcover').DataTable();

        // Script agar nama file muncul saat dipilih
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
    });
</script>
<?= $this->endSection(); ?>