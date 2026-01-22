<?= $this->extend("layout/template"); ?>



<?= $this->section("content"); ?>

<h1 class="h3 mb-2 text-gray-800"><?= $title ?></h1>

<?php if (session()->getFlashdata("message")) : ?>

    <div id="flashdata" data-open="true">

        <p id="icon" hidden><?= session()->getFlashdata("message")["icon"]; ?></p>

        <p id="title" hidden><?= session()->getFlashdata("message")["title"]; ?></p>

        <p id="text" hidden><?= session()->getFlashdata("message")["text"]; ?></p>

    </div>

<?php endif; ?>

<div class="card shadow mb-4">

        <div class="card-header py-3">

            <?= $title ?>

        </div>

        <div class="card-body">

            <div class="d-flex justify-content-start mb-3">

                <button class="btn btn-primary" data-toggle="modal" data-target="#tambahSemester">Tambahkan Semester</button>

            </div>

            <div class="table-responsive">

                <table class="table table-bordered" id="tableSemester" width="100%" cellspacing="0">

                    <thead>

                        <tr>

                            <th>No</th>

                            <th>Nama</th>

                            <th>Status</th>

                            <th>Aksi</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php 
                        foreach($semester as $s):?>

                            <tr>

                                <td></td>

                                <td class="nama-semester"><?= $s['nama'] ?></td>

                                <td class="status-semester"><?= $s['status'] ?></td>

                                <td>

                                    <form action="<?= base_url("fakultas/deleteSemester/" . $s['kode']) ?>" method="post" class="d-inline" id="formHapusSemester">

                                        <button type="submit" class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Hapus"><i class="fa fa-trash"></i></button>

                                    </form>

                                    <?php if ($s['status'] == 'NON-AKTIF') : ?>
                                        <form action="<?= base_url("fakultas/activateSemester/" . $s['kode']) ?>" method="post" class="d-inline" id="formAktivasiSemester">
                                            <button type="submit" class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="Aktifkan Semester"><i class="fas fa-check"></i></button>
                                        </form>
                                    <?php elseif ($s['status'] == 'AKTIF'): ?>
                                        <form action="<?= base_url("fakultas/nonactivateSemester/" . $s['kode']) ?>" method="post" class="d-inline" id="formNonaktivasiSemester">
                                            <button type="submit" class="btn btn-secondary" data-toggle="tooltip" data-placement="top" title="Nonaktifkan Semester"><i class="fas fa-times"></i></button>
                                        </form>
                                    <?php endif; ?>

                                </td>

                            </tr>    

                        <?php 

                        endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>



    <!-- modal tambah semester -->

    <div class="modal fade" id="tambahSemester" tabindex="-1" role="dialog" aria-labelledby="tambahSemesterLabel" aria-hidden="true">

        <div class="modal-dialog" role="document">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title" id="tambahSemesterLabel">Tambah Semester</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                    <span aria-hidden="true">&times;</span>

                    </button>

                </div>

                <form id="formTambahSemester" method="post" action="<?= base_url("fakultas/insertSemester")?>">

                    <?= csrf_field() ?>

                    <div class="modal-body">

                        <div class="form-group">

                            <label for="tahunAkademik">Tahun Akademik</label>

                            <input type="text" class="form-control" id="tahunAkademik" name="tahunAkademik">

                        </div>

                        <div class="form-group">

                            <label for="semester">Semester</label>

                            <select name="semester" id="semester" class="form-control">
                                <option selected disabled> - Pilih Semester - </option>
                                <option value="1">Ganjil</option>
                                <option value="2">Genap</option>
                            </select>

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

    <script src="<?= base_url("assets/js/fakultas/semester.js");?>"></script>

<?= $this->endSection(); ?>