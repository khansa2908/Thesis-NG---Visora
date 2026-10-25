<?= $this->extend("layout/template"); ?>

<?= $this->section("content"); ?>

<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Kelola Pembimbing Eksternal</h1>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pembimbing Eksternal</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th>Nama Lengkap</th>
                            <th>Instansi</th>
                            <th>Email / Kontak</th>
                            <th style="width: 20%;">Status Dokumen</th>
                            <th style="width: 10%;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($pembimbing as $row) : ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $row['nama_lengkap']; ?></td>
                            <td><?= $row['instansi']; ?></td>
                            <td>
                                <?= $row['email']; ?><br>
                                <small class="text-muted"><?= $row['kontak']; ?></small>
                            </td>
                            <td>
                                <span class="badge badge-<?= $row['file_ktp'] ? 'primary' : 'secondary'; ?>">KTP</span>
                                <span class="badge badge-<?= $row['file_cv'] ? 'primary' : 'secondary'; ?>">CV</span>
                                <span class="badge badge-<?= $row['file_npwp'] ? 'primary' : 'secondary'; ?>">NPWP</span>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-info btn-sm btn-kelola-dokumen" 
                                    data-toggle="modal" 
                                    data-target="#modalDokumen"
                                    data-id="<?= $row['id']; ?>"
                                    data-nama="<?= $row['nama_lengkap']; ?>"
                                    data-ktp="<?= $row['file_ktp']; ?>"
                                    data-cv="<?= $row['file_cv']; ?>"
                                    data-npwp="<?= $row['file_npwp']; ?>"
                                    title="Kelola Dokumen">
                                    <i class="fas fa-folder-open"></i> Kelola
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<div class="modal fade" id="modalDokumen" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Kelola Dokumen: <span id="modalNamaUser" class="font-weight-bold text-primary"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                
                <?php 
                $dokumenList = ['ktp' => 'KTP', 'cv' => 'CV', 'npwp' => 'NPWP'];
                foreach($dokumenList as $key => $label): 
                ?>
                <div class="row mb-3 border-bottom pb-3 align-items-center">
                    <div class="col-md-3 font-weight-bold"><?= $label; ?></div>
                    <div class="col-md-5">
                        <div id="status-<?= $key; ?>">
                            <span class="text-muted font-italic">Belum ada file.</span>
                        </div>
                    </div>
                    <div class="col-md-4 text-right">
                        <form action="<?= base_url('TenagaKependidikan/uploadDokumen'); ?>" method="post" enctype="multipart/form-data" class="d-inline">
                            <input type="hidden" name="id_pembimbing" id="input-id-<?= $key; ?>">
                            <input type="hidden" name="jenis_dokumen" value="<?= $key; ?>">
                            
                            <input type="file" name="file_dokumen" id="file-<?= $key; ?>" style="display: none;" onchange="this.form.submit()">
                            
                            <label for="file-<?= $key; ?>" class="btn btn-primary btn-sm mb-0 shadow-sm" style="cursor: pointer;">
                                <i class="fas fa-upload"></i> Upload
                            </label>
                        </form>

                        <a href="#" id="btn-hapus-<?= $key; ?>" class="btn btn-danger btn-sm shadow-sm d-none" onclick="return confirm('Yakin ingin menghapus dokumen ini?')">
                            <i class="fas fa-trash"></i> Hapus
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section("scripts"); ?>
<script>
    $(document).ready(function() {
        // Event saat tombol Folder Biru diklik
        $(document).on("click", ".btn-kelola-dokumen", function () {
            
            var id = $(this).data('id');
            var nama = $(this).data('nama');
            var files = {
                'ktp': $(this).data('ktp'),
                'cv': $(this).data('cv'),
                'npwp': $(this).data('npwp')
            };

            $('#modalNamaUser').text(nama);

            $.each(files, function(key, filename) {
                $('#input-id-' + key).val(id);

                if (filename && filename !== "") {
                    // JIKA FILE ADA
                    var linkFile = '<?= base_url('uploads/pembimbing'); ?>/' + filename;
                    $('#status-' + key).html('<a href="' + linkFile + '" target="_blank" class="text-success font-weight-bold"><i class="fas fa-check-circle"></i> File Tersedia (Lihat)</a>');
                    
                    $('#btn-hapus-' + key).removeClass('d-none');
                    var linkHapus = '<?= base_url('TenagaKependidikan/hapusDokumen'); ?>/' + id + '/' + key;
                    $('#btn-hapus-' + key).attr('href', linkHapus);
                } else {
                    // JIKA FILE KOSONG
                    $('#status-' + key).html('<span class="text-muted font-italic"><i class="fas fa-times-circle"></i> Belum ada file</span>');
                    $('#btn-hapus-' + key).addClass('d-none');
                }
            });
        });
    });
</script>
<?= $this->endSection(); ?>