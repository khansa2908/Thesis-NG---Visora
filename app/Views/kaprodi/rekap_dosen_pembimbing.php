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
                <div class="col-lg-5"></div>

                <div class="col-lg-7 d-flex justify-content-end align-items-center">

                    <form class="form-inline" action="<?= base_url("Kaprodi/rekapDosenPembimbing") ?>" method="get" id="semesterForm">

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

                <table class="table table-bordered" id="tablePembimbing" width="100%" cellspacing="0">

                    <thead>

                        <tr>

                            <th>No</th>

                            <th>Inisial</th>

                            <th class="text-center">Nama</th>

                            <th>Jumlah Mahasiswa</th>

                            <th>Aksi</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php $counter = 1; ?>

                    <?php foreach($pembimbing as $mpp): ?>

                        <tr>

                            <td><?= $counter ?></td>

                            <td><?= $mpp['inisial_dosen'] ?></td>

                            <td><?= $mpp['nama_dosen'] ?></td>

                            <td><?= $mpp['jumlah_mahasiswa'] ?></td>

                            <td>

                                <a role="button" class="btn btn-primary" href="<?= base_url("kaprodi/detailRekapDospem/".$mpp['id_dosen']."/".$mpp['kode_semester']) ?>" data-toggle="tooltip" title="Detail"><i class="fas fa-info-circle"></i></a>

                            </td>

                        </tr>

                        <?php $counter++; ?>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

<?= $this->endSection(); ?>



<?= $this->section("scripts"); ?>

    <script src="<?= base_url("assets/js/kaprodi/rekapDosenPembimbing.js");?>"></script>

<?= $this->endSection(); ?>