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

            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link active" id="nav-internal-tab" data-toggle="tab" href="#nav-internal" role="tab" aria-controls="nav-internal" aria-selected="true">Dosen Internal</a>
                    <a class="nav-item nav-link" id="nav-eksternal-tab" data-toggle="tab" href="#nav-eksternal" role="tab" aria-controls="nav-eksternal" aria-selected="false">Dosen Eksternal</a>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-internal" role="tabpanel" aria-labelledby="nav-internal-tab">
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
                            <?php foreach($listDosen as $d): ?>
                                <tr>
                                    <td></td>
                                    <td class="id-dosen" hidden><?= $d['id'] ?></td>
                                    <td class="inisial"><?= $d['inisial'] ?></td>
                                    <td class="nama"><?= $d['nama'] ?></td>
                                    <td class="prodi" data-toggle="tooltip" data-placement="top" title="<?= $d['nama_prodi'] ?>" data-id="<?= $d['id_prodi'] ?>"><?= $d['inisial_prodi'] ?></td>
                                    <td class="email"><?= $d['email'] ?></td>
                                    <td style="min-width: 7vw">
                                        <button class="btn btn-primary btn-edit-dosen" data-toggle="tooltip" data-placement="top" title="Ubah"><i class="fas fa-pencil-alt"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>

                    </div>
                </div>


                <div class="tab-pane fade" id="nav-eksternal" role="tabpanel" aria-labelledby="nav-eksternal-tab">
                    <div class="table-responsive">

                        <table class="table table-bordered" id="tableDosenEksternal" width="100%" cellspacing="0">
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
                            <?php foreach($listDosenEksternal as $de): ?>
                                <tr>
                                    <td></td>
                                    <td class="id-dosen" hidden><?= $de['id'] ?></td>
                                    <td class="inisial"><?= $de['inisial'] ?></td>
                                    <td class="nama"><?= $de['nama'] ?></td>
                                    <td class="instansi"><?= $de['instansi'] ?></td>
                                    <td class="email"><?= $de['email'] ?></td>
                                    <td style="min-width: 7vw">
                                        <button class="btn btn-primary btn-edit-dosen" data-toggle="tooltip" data-placement="top" title="Ubah"><i class="fas fa-pencil-alt"></i></button>

                                        <?php if ($de['status_akun'] == 'ACTIVE'): ?>
                                            <form action="<?= base_url("TenagaKependidikan/inactivatedDosenEksternal/" . $de['id']) ?>" method="post" class="d-inline" id="formInactiveDosenEksternal">
                                                <button type="submit" class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Nonaktifkan Akun Dosen"><i class="fa fa-lock"></i></button>
                                            </form>
                                        <?php else : ?>
                                            <form action="<?= base_url("TenagaKependidikan/reactivatedDosenEksternal/" . $de['id']) ?>" method="post" class="d-inline" id="formReactiveDosenEksternal">
                                                <button type="submit" class="btn btn-success" data-toggle="tooltip" data-placement="top" title="Aktifkan Akun Dosen"><i class="fa fa-lock-open"></i></button>
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
        </div>

    </div>
    

    <!-- MODAL UBAH UNTUK DOSEN INTERNAL -->
    <div class="modal fade" id="ubahDosen" tabindex="-1" role="dialog" aria-labelledby="ubahDosenLabel" aria-hidden="true">

        <div class="modal-dialog modal-lg" role="document">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title" id="ubahDosenLabel">Ubah Data Dosen</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                    <span aria-hidden="true">&times;</span>

                    </button>

                </div>

                <form action="<?= base_url("TenagaKependidikan/updateDosen") ?>" method="post" id="formUbahDosen">
                    <input type="hidden" id="idDosen" name="idDosen">
                    <div class="modal-body">

                        <div class="row">

                            <div class="col-lg-12">
                                <div class="form-group" >
                                    <label>Program Studi</label>
                                    <select name="prodi" id="prodi" class="form-control">
                                        <?php foreach ($listProdi as $p) : ?>
                                            <option value="<?= $p['id'] ?>"><?= $p['inisial'] ?> | <?= $p['nama'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
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


    <!-- MODAL UBAH UNTUK DOSEN EKSTERNAL -->
    <div class="modal fade" id="ubahDosenEksternal" tabindex="-1" role="dialog" aria-labelledby="ubahDosenEksternalLabel" aria-hidden="true">

        <div class="modal-dialog modal-lg" role="document">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title" id="ubahDosenEksternalLabel">Ubah Data Dosen Eksternal</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                    <span aria-hidden="true">&times;</span>

                    </button>

                </div>

                <form action="<?= base_url("TenagaKependidikan/updateDosenEksternal") ?>" method="post" id="formUbahDosenEksternal">
                    <input type="hidden" id="idDosen" name="idDosen">
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

    <script src="<?= base_url("assets/js/tenagakependidikan/dosen.js");?>"></script>

<?= $this->endSection(); ?>