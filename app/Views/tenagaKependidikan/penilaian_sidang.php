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

        <div class="card-header py-3">

            <?= $title ?>

        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-lg-5 d-flex align-items-center">
                    <a href="<?= base_url("TenagaKependidikan/cetakBeritaAcaraBulk") ?>" target="_blank" role="button" class="btn btn-primary">Print Berita Acara Bulk</a>
                </div>

                <div class="col-lg-7 d-flex justify-content-end align-items-center">

                    <form class="form-inline" action="<?= base_url("TenagaKependidikan/penilaianSidang") ?>" method="get" id="semesterForm">

                        <div class="form-group">
                            <select class="form-control" id="semesterOption" name="semester">
                                <?php foreach($semesterList as $semester) : ?>
                                    <?php if($semester['status'] == 'AKTIF'): ?>
                                        <option value="<?= $semester['kode'] ?>" selected><?= $semester['nama'] ?> (Aktif)</option>
                                    <?php else: ?>
                                        <option value="<?= $semester['kode'] ?>"><?= $semester['nama'] ?></option>
                                    <?php endif; ?>
                                <?php endforeach;?>
                            </select>
                        </div>

                    </form>

                </div>

            </div>



            <div class="table-responsive">

                <table class="table table-bordered" id="tablePenilaianSidang" width="100%" cellspacing="0">

                    <thead>

                        <tr>

                            <th>No</th>

                            <th>NPM</th>

                            <th class="text-center">Nama</th>

                            <th>Program Studi</th>

                            <th>Judul</th>

                            <th>Tanggal Sidang</th>

                            <th>Ruangan</th>

                            <th>Penguji</th>

                            <th>Total Nilai</th>

                            <th>Status</th>

                            <th>Aksi</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php $counter = 1; ?>

                    <?php foreach($mahasiswa as $mhs): ?>

                        <tr>

                            <td><?= $counter ?></td>

                            <td><?= $mhs['npm'] ?></td>
                            <td><?= $mhs['nama_mahasiswa'] ?></td>
                            <td data-toggle="tooltip" data-placement="top" title="<?= $mhs['nama_prodi'] ?>"><?= $mhs['inisial_prodi'] ?></td>
                            <td><?= $mhs['judul'] ?></td>
                            <td style="min-width: 7vw;"><?= date_format(date_create($mhs['tanggal']), 'd-m-Y H:i') ?> WIB</td>

                            <?php if (preg_match('#^https?://#i', $mhs['ruangan']) === 1): ?>

                                <td class="ruangan"><a href="<?= $mhs['ruangan'] ?>" target="_blank">Link Zoom</a></td>

                            <?php else: ?>

                                <td class="ruangan"><?= $mhs['ruangan'] ?></td>

                            <?php endif; ?>

                            <td data-toggle="tooltip" data-placement="top" title="<?= $mhs['nama_penguji'] ?>"><?= $mhs['inisial_penguji'] ?></td>

                            <td><?= $mhs['total_nilai'] ?></td>

                            <td><?= $mhs['status'] == null ? 'TERTUNDA' : $mhs['status'] ?></td>

                            <td style="min-width: 7vw">

                                <a role="button" class="btn btn-primary" href="<?= base_url("TenagaKependidikan/hasilSidangSkripsi/".$mhs['id_skripsi']) ?>" data-toggle="tooltip" title="Hasil Sidang Skripsi" data-placement="top"><i class="fas fa-clipboard-list"></i></a>

                                <?php if($mhs['status'] != null): ?>

                                    <button type="button" 
                                            class="btn btn-primary btn-cetak-ba-sidang" 
                                            data-toggle="modal" 
                                            data-target="#modalCetakBASidang"
                                            data-idskripsi="<?= $mhs['id_skripsi']; ?>"
                                            data-nomor="<?= isset($mhs['nomor_berita_acara']) ? $mhs['nomor_berita_acara'] : ''; ?>"
                                            title="Print Berita Acara">
                                        <i class="fas fa-print"></i>
                                    </button>

                                <?php else: ?>

                                    <button class="btn btn-primary" disabled><i class="fas fa-print"></i></button>

                                <?php endif; ?>

                            </td>

                        </tr>

                        <?php $counter++; ?>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    <!-- Modal Cetak BA Sidang -->
     <div class="modal fade" id="modalCetakBASidang" tabindex="-1" role="dialog" aria-labelledby="modalLabelSidang" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabelSidang">Cetak BA Sidang Skripsi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?= base_url('TenagaKependidikan/updateAndCetakBASidang'); ?>" method="post" target="_blank">
                    <div class="modal-body">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="id_skripsi" id="id_skripsi_input">
                        
                        <div class="form-group">
                            <label for="nomor_ba_sidang">Nomor Berita Acara Sidang</label>
                            <input type="text" class="form-control" name="nomor_berita_acara" id="nomor_ba_sidang_input" required placeholder="Contoh: 005/BA-SIDANG/FTI/2025">
                            <small class="text-muted">Nomor ini akan tercetak di dokumen PDF.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-print"></i> Simpan & Cetak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?= $this->endSection(); ?>



<?= $this->section("scripts"); ?>

    <script src="<?= base_url("assets/js/tenagakependidikan/penilaianSidang.js");?>"></script>

    <script>
        $(document).ready(function() {
            // Ketika tombol print diklik
            $('.btn-cetak-ba-sidang').on('click', function() {
                // Ambil data dari atribut tombol
                const idSkripsi = $(this).data('idskripsi');
                const nomor = $(this).data('nomor');

                // Masukkan ke dalam input form modal
                $('#id_skripsi_input').val(idSkripsi);
                $('#nomor_ba_sidang_input').val(nomor);
            });
        });
    </script>
<?= $this->endSection(); ?>