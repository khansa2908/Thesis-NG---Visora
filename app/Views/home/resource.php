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

            <div class="row mb-3">

                <div class="col-lg-3 d-flex align-items-center">

                    <?php if ( in_array("kaprodi", $roles) || in_array("fakultas", $roles) ) : ?>

                        <button class="btn btn-primary" data-toggle="modal" data-target="#tambahSumberDaya">Tambahkan Sumber Daya</button>

                    <?php endif; ?>

                </div>

            </div>

            <div class="table-responsive">

                <table class="table table-bordered table-sm" id="sumberDaya" width="100%" cellspacing="0">

                    <thead>

                        <tr>

                            <th>No</th>

                            <th>Nama Sumber Daya</th>

                            <th>Aksi</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach($resources as $r) : ?>

                            <?php if ($r['visibilitas'] == 'TERLIHAT' || ($r['visibilitas'] == 'TERSEMBUNYI' && ( in_array("kaprodi", $roles) || in_array("fakultas", $roles) ))): ?>

                            <tr class="<?= ($r['visibilitas'] == 'TERLIHAT') ? 'table-default': 'table-active' ?>">

                                <td></td>

                                <td class="nama"><?= $r['nama'] ?></td>

                                <td>

                                    <form action="<?= base_url("home/downloadResource/".$r['id']) ?>" method="post" class="d-inline-block">

                                        <button class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="Unduh Berkas"><i class="fas fa-download"></i></button>

                                    </form>

                                <?php if ( in_array("kaprodi", $roles) || in_array("fakultas", $roles) ) : ?>

                                    <form action="<?= base_url("home/deleteResource/".$r['id']) ?>" method="post" class="d-inline-block">

                                        <button class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Hapus Berkas"><i class="fas fa-trash"></i></button>

                                    </form>

                                    <button class="btn btn-primary ubah-resource" data-toggle="tooltip" data-placement="top" title="Ubah Berkas" data-id="<?= $r['id'] ?>"><i class="fas fa-pencil-alt"></i></button>

                                    <?php if ($r['visibilitas'] == 'TERLIHAT') : ?>
                                        <form action="<?= base_url("home/hideResource/".$r['id']) ?>" method="post" class="d-inline-block">
                                            <button class="btn btn-secondary" data-toggle="tooltip" data-placement="top" title="Sembunyikan Berkas"><i class="fas fa-eye-slash"></i></button>
                                        </form>
                                    <?php elseif ($r['visibilitas'] == 'TERSEMBUNYI'): ?>
                                        <form action="<?= base_url("home/showResource/".$r['id']) ?>" method="post" class="d-inline-block">
                                            <button class="btn btn-success" data-toggle="tooltip" data-placement="top" title="Tampilkan Berkas"><i class="fas fa-eye"></i></button>
                                        </form>
                                    <?php endif; ?>

                                <?php endif; ?>

                                </td>

                            </tr>
                            <?php endif; ?>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>





    <?php if ( in_array("kaprodi", $roles) || in_array("fakultas", $roles) ) : ?>

        <!-- modal tambah sumber daya -->

        <div class="modal fade" id="tambahSumberDaya" tabindex="-1" role="dialog" aria-labelledby="tambahSumberDayaLabel" aria-hidden="true">

            <div class="modal-dialog" role="document">

                <div class="modal-content">

                    <div class="modal-header">

                        <h5 class="modal-title" id="tambahSumberDayaLabel">Tambahkan Sumber Daya</h5>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                        <span aria-hidden="true">&times;</span>

                        </button>

                    </div>

                    <form action="<?= base_url("home/insertResource") ?>" method="post" id="formTambahSumberDaya" enctype="multipart/form-data">

                        <div class="modal-body">

                            <div class="row">

                                <div class="col-lg-12">

                                    <div class="form-group">

                                        <label for="nama">Nama Sumber Daya</label>

                                        <input type="text" class="form-control" id="nama" name="nama">

                                    </div>

                                </div>



                                <div class="col-lg-12">

                                    <label>File Sumber Daya</label>

                                    <div class="input-group">

                                        <div class="custom-file">

                                            <input type="file" class="custom-file-input" id="file_resource" name="file_resource">

                                            <label class="custom-file-label" for="file_resource">Pilih File Sumber Daya</label>

                                        </div>

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



        <!-- modal ubah sumber daya -->

        <div class="modal fade" id="ubahSumberDaya" tabindex="-1" role="dialog" aria-labelledby="ubahSumberDayaLabel" aria-hidden="true">

            <div class="modal-dialog" role="document">

                <div class="modal-content">

                    <div class="modal-header">

                        <h5 class="modal-title" id="ubahSumberDayaLabel">Ubah Sumber Daya</h5>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                        <span aria-hidden="true">&times;</span>

                        </button>

                    </div>

                    <form action="" method="post" id="formUbahSumberDaya" enctype="multipart/form-data">

                        <div class="modal-body">

                            <div class="row">

                                <div class="col-lg-12">

                                    <div class="form-group">

                                        <label for="nama">Nama Sumber Daya</label>

                                        <input type="text" class="form-control" id="nama" name="nama">

                                    </div>

                                </div>



                                <div class="col-lg-12">

                                    <label>File Sumber Daya</label>

                                    <div class="input-group">

                                        <div class="custom-file">

                                            <input type="file" class="custom-file-input" id="file_resource" name="file_resource">

                                            <label class="custom-file-label" for="file_resource">Pilih File Sumber Daya</label>

                                        </div>

                                    </div>

                                    <small>Unggah file jika ingin memperbarui file sumber daya</small>

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

    <?php endif; ?>

<?= $this->endSection(); ?>



<?= $this->section("scripts"); ?>

    <script src="<?= base_url("assets/js/home/resource.js");?>"></script>

<?= $this->endSection(); ?>