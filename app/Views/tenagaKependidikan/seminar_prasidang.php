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

            Jadwal Seminar Prasidang

        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-lg-5">
                </div>

                <div class="col-lg-7 d-flex justify-content-end align-items-center">
                    <form class="form-inline" action="<?= base_url("TenagaKependidikan/seminarPrasidang") ?>" method="get" id="semesterForm">
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



            <!-- table seminar prasidang -->

            <div class="table-responsive">

                <table class="table table-bordered" id="jadwalSeminarPrasidang" width="100%" cellspacing="0">

                    <thead>

                        <tr>

                            <th>No</th>

                            <th>NPM</th>

                            <th>Nama</th>

                            <th>Program Studi</th>

                            <th>Judul</th>

                            <th>Bidang</th>

                            <th>Tanggal Seminar</th>

                            <th>Ruangan</th>

                            <th>Reviewer</th>

                            <th>Status</th>
                        </tr>

                    </thead>

                    <tbody>

                    <?php $counter = 1; ?>

                    <?php foreach($seminarPrasidang as $sp): ?>

                        <tr>

                            <td><?= $counter ?></td>

                            <td class="npm"><?= $sp['npm'] ?></td>

                            <td class="nama"><?= $sp['nama_mahasiswa'] ?></td>

                            <td data-toggle="tooltip" data-placement="top" title="<?= $sp['nama_prodi'] ?>"><?= $sp['inisial_prodi'] ?></td>

                            <td><?= $sp['judul'] ?></td>

                            <td data-toggle="tooltip" data-placement="top" title="<?= $sp['nama_bidang'] ?>"><?= $sp['inisial_bidang'] ?></td>                        

                            <td class="tanggal" style="min-width: 8vw"><?= date_format(date_create($sp['tanggal']), 'd-m-Y H:i') ?> WIB</td>

                            <?php if (preg_match('#^https?://#i', $sp['ruangan']) === 1): ?>

                                <td class="ruangan"><a href="<?= $sp['ruangan'] ?>" target="_blank">Klik disini!</a></td>

                            <?php else: ?>

                                <td class="ruangan"><?= $sp['ruangan'] ?></td>

                            <?php endif; ?>



                            <?php foreach($dosen as $d) : ?>

                                <?php if ($d['id'] == $sp['dosen_reviewer']) : ?>

                                    <td data-toggle="tooltip" data-placement="top" title="<?= $d['nama'] ?>" class="dosen_reviewer" data-id="<?= $d['id'] ?>"><?= $d['inisial'] ?></td>

                                <?php endif; ?>

                            <?php endforeach; ?>

                            <td><?= $sp['status'] ?></td>

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

    <script src="<?= base_url("assets/js/tenagakependidikan/seminarPrasidang.js");?>" defer></script>

<?= $this->endSection(); ?>