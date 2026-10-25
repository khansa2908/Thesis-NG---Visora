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

                    <a href="<?= base_url("Kaprodi/cetakBeritaAcaraBulk") ?>" target="_blank" role="button" class="btn btn-primary">Print Berita Acara Bulk</a>

                </div>

                <div class="col-lg-7 d-flex justify-content-end align-items-center">

                    <form class="form-inline" action="<?= base_url("Kaprodi/penilaianSidang") ?>" method="get" id="semesterForm">

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

                            <td><?= $mhs['judul'] ?></td>

                            <td style="min-width: 7vw;"><?= date_format(date_create($mhs['tanggal']), 'd-m-Y H:i') ?> WIB</td>

                            <?php if (preg_match('#^https?://#i', $mhs['ruangan']) === 1): ?>

                                <td class="ruangan"><a href="<?= $mhs['ruangan'] ?>" target="_blank">Link Zoom</a></td>

                            <?php else: ?>

                                <td class="ruangan"><?= $mhs['ruangan'] ?></td>

                            <?php endif; ?>

                            <td data-toggle="tooltip" data-placement="top" title="<?= $mhs['nama_penguji'] ?>"><?= $mhs['inisial_penguji'] ?></td>

                            <td><?= $mhs['total_nilai'] == null ? "0.00": $mhs['total_nilai'] ?></td>

                            <td><?= $mhs['status'] == null ? 'TERTUNDA' : $mhs['status'] ?></td>

                            <td style="min-width: 7vw">

                                <a role="button" class="btn btn-primary" href="<?= base_url("Kaprodi/hasilSidangSkripsi/".$mhs['id_skripsi']) ?>" data-toggle="tooltip" title="Hasil Sidang Skripsi" data-placement="top"><i class="fas fa-clipboard-list"></i></a>

                                <?php if($mhs['status'] != null): ?>

                                    <a role="button" class="btn btn-primary" href="<?= base_url("Kaprodi/cetakBeritaAcara/".$mhs['id_skripsi']) ?>" target="_blank" data-toggle="tooltip" title="Print Berita Acara" data-placement="top"><i class="fas fa-print"></i></a>

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



    <div class="modal fade" id="semesterModal" tabindex="-1" role="dialog" aria-labelledby="semesterModalLabel" aria-hidden="true">

        <div class="modal-dialog" role="document">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title" id="semesterModalLabel">Print Berita Acara Bulk</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                    <span aria-hidden="true">&times;</span>

                    </button>

                </div>

                <div class="modal-body">

                    <div class="col-lg-12">

                        <div class="form-group">

                            <label for="dari">Pilih Semester yang ingin dicetak</label>

                            <select class="form-control" id="semester" name="semester">
                                <?php foreach($semesterList as $semester) : ?>
                                    <?php if($semester['status'] == 'AKTIF'): ?>
                                        <option value="<?= $semester['kode'] ?>" selected><?= $semester['nama'] ?> (Aktif)</option>
                                    <?php else: ?>
                                        <option value="<?= $semester['kode'] ?>"><?= $semester['nama'] ?></option>
                                    <?php endif; ?>
                                <?php endforeach;?>
                            </select>

                        </div>

                    </div>
                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>

                    <a role="button" class="btn btn-primary" id="submitPrint">Cetak</a>

                </div>

            </div>

        </div>

    </div>

<?= $this->endSection(); ?>



<?= $this->section("scripts"); ?>

    <script src="<?= base_url("assets/js/kaprodi/penilaianSidang.js");?>"></script>

<?= $this->endSection(); ?>