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

                <table class="table table-bordered" id="jadwalSeminarPrasidang" width="100%" cellspacing="0">

                    <thead>

                        <tr>

                            <th>No</th>

                            <th>NPM</th>

                            <th>Nama Mahasiswa</th>

                            <th>Prodi</th>

                            <th>Judul</th>
                            
                            <th>Bidang</th>

                            <th>Ruangan</th>
                            
                            <th>Tanggal Seminar</th>

                            <th>Reviewer</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php 

                    foreach($jadwalSeminarPrasidang as $jsp): ?>

                        <tr>

                            <td></td>

                            <td><?= $jsp['npm'] ?></td>

                            <td><?= $jsp['nama_mahasiswa'] ?></td>

                            <td data-toggle="tooltip" data-placement="top" title="<?= $jsp['nama_prodi'] ?>"><?= $jsp['inisial_prodi'] ?></td>

                            <td><?= $jsp['judul'] ?></td>
                            
                            <td data-toggle="tooltip" data-placement="top" title="<?= $jsp['nama_bidang'] ?>"><?= $jsp['inisial_bidang'] ?></td>

                            <?php if (preg_match('#^https?://#i', $jsp['ruangan']) === 1): ?>

                                <td class="ruangan"><a href="<?= $jsp['ruangan'] ?>" target="_blank">Klik disini!</a></td>

                            <?php else: ?>

                                <td class="ruangan"><?= $jsp['ruangan'] ?></td>

                            <?php endif; ?>

                            <td ><?= date_format(date_create($jsp['tanggal']), 'd-m-Y H:i') ?> WIB</td>

                            <td data-toggle="tooltip" data-placement="top" title="<?= $jsp['nama_reviewer'] ?>"><?= $jsp['inisial_reviewer'] ?></td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

<?= $this->endSection(); ?>



<?= $this->section("scripts"); ?>

    <script src="<?= base_url("assets/js/home/seminarPrasidang.js");?>"></script>

<?= $this->endSection(); ?>