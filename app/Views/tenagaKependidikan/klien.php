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

                <table class="table table-bordered" id="tableKlien" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th hidden>id</th>
                            <th>Inisial</th>
                            <th>Nama</th>
                            <th>Instansi</th>
                            <th>Email</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php foreach($listKlien as $lk): ?>
                        <tr>
                            <td></td>
                            <td class="id-klien" hidden><?= $lk['id'] ?></td>
                            <td class="inisial"><?= $lk['inisial'] ?></td>
                            <td class="nama"><?= $lk['nama'] ?></td>
                            <td class="instansi"><?= $lk['instansi'] ?></td>
                            <td class="email"><?= $lk['email'] ?></td>
                            <td style="min-width: 7vw">
                                <button class="btn btn-primary btn-edit-klien" data-toggle="tooltip" data-placement="top" title="Ubah"><i class="fas fa-pencil-alt"></i></button>

                                <?php if ($lk['status_akun'] == 'ACTIVE'): ?>
                                    <form action="<?= base_url("TenagaKependidikan/inactivatedKlien/" . $lk['id']) ?>" method="post" class="d-inline" id="formInactiveKlien">
                                        <button type="submit" class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Nonaktifkan Akun Klien"><i class="fa fa-lock"></i></button>
                                    </form>
                                <?php else : ?>
                                    <form action="<?= base_url("TenagaKependidikan/reactivatedKlien/" . $lk['id']) ?>" method="post" class="d-inline" id="formReactiveKlien">
                                        <button type="submit" class="btn btn-success" data-toggle="tooltip" data-placement="top" title="Aktifkan Akun Klien"><i class="fa fa-lock-open"></i></button>
                                    </form>
                                <?php endif; ?>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

            </div>
            
        </div>

    </div>
    

    <!-- MODAL UBAH UNTUK KLIEN -->
    <div class="modal fade" id="ubahKlien" tabindex="-1" role="dialog" aria-labelledby="ubahKlienLabel" aria-hidden="true">

        <div class="modal-dialog modal-lg" role="document">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title" id="ubahKlienLabel">Ubah Data Klien</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                    <span aria-hidden="true">&times;</span>

                    </button>

                </div>

                <form action="<?= base_url("TenagaKependidikan/updateKlien") ?>" method="post" id="formUbahKlien">
                    <input type="hidden" id="idKlien" name="idKlien">
                    <div class="modal-body">

                        <div class="row">

                            <div class="col-lg-12">
                                <div class="form-group" >
                                    <label for="instansi">Nama Instansi</label>
                                    <input type="text" class="form-control" id="instansi" name="instansi">
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group" >
                                    <label for="nama">Nama lengkap</label>
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

    <script src="<?= base_url("assets/js/tenagakependidikan/klien.js");?>"></script>

<?= $this->endSection(); ?>