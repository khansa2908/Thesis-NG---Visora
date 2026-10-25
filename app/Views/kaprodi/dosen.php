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

            <div class="table-responsive">

                <table class="table table-bordered" id="tableDosen" width="100%" cellspacing="0">

                    <thead>

                        <tr>
                            <th>No</th>
                            <th hidden>id</th>
                            <th>Inisial</th>
                            <th>Nama</th>
                            <th>Prodi</th>
                            <th>Email</th>
                            <th>Aksi</th>
                        </tr>

                    </thead>

                    <tbody>

                    <?php $counter = 1; ?>

                    <?php foreach($listDosen as $d): ?>

                        <tr>
                            <td><?= $counter ?></td>
                            <td class="id-dosen" hidden><?= $d['id'] ?></td>
                            <td class="inisial"><?= $d['inisial'] ?></td>
                            <td class="nama"><?= $d['nama'] ?></td>
                            <td class="prodi"><?= $d['nama_prodi'] ?></td>
                            <td class="email"><?= $d['email'] ?></td>
                            <td style="min-width: 7vw">
                                <button class="btn btn-primary btn-edit-dosen" data-toggle="tooltip" data-placement="top" title="Ubah"><i class="fas fa-pencil-alt"></i></button>
                            </td>
                        </tr>

                        <?php $counter++; ?>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>
    

    <div class="modal fade" id="ubahDosen" tabindex="-1" role="dialog" aria-labelledby="ubahDosenLabel" aria-hidden="true">

        <div class="modal-dialog modal-lg" role="document">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title" id="ubahDosenLabel">Ubah Data Dosen</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                    <span aria-hidden="true">&times;</span>

                    </button>

                </div>

                <form action="<?= base_url("Kaprodi/updateDosen") ?>" method="post" id="formUbahDosen">
                    <input type="hidden" id="idDosen" name="idDosen">
                    <div class="modal-body">

                        <div class="row">

                            <div class="col-lg-12">
                                <div class="form-group" >
                                    <label>Program Studi</label>
                                    <input type="text" class="form-control" id="prodi" disabled>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group" >
                                    <label for="nama">Nama lengkap beserta gelar</label>
                                    <input type="text" class="form-control" id="nama" name="nama">
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group" >
                                    <label for="email">Email</label>
                                    <input type="text" class="form-control" id="email" name="email">
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group" >
                                    <label for="inisial">Inisial nama</label>
                                    <input type="text" class="form-control" id="inisial" name="inisial">
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    </div>

                </form>

            </div>

        </div>

    </div>

<?= $this->endSection(); ?>



<?= $this->section("scripts"); ?>

    <script src="<?= base_url("assets/js/kaprodi/dosen.js");?>"></script>

<?= $this->endSection(); ?>