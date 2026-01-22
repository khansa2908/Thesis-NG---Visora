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

                <table class="table table-bordered" id="rubrikNilai" width="100%" cellspacing="0">

                    <thead>

                        <tr>

                            <th>No</th>

                            <th>CPL</th>

                            <th>Bobot</th>

                            <th>Penilaian</th>

                            <th>Kurang (<= 60)</th>
                            
                            <th>Sedang (60 - 80)</th>

                            <th>Baik (>= 80)</th>

                        </tr>

                    </thead>

                    <tbody>
                        <form action="post" class="d-inline">
                            <input type="hidden" name="jenis_skripsi" id="jenis_skripsi" value="<?= $jenisSkripsi ?>">
                            <input type="hidden" name="pembimbing" id="pembimbing" value="<?= $pembimbing ?>">

                        <?php foreach ($rubrikPenilaian as $rb) : ?>

                            <tr>

                                <td></td>

                                <td><?= $rb['cpl'] ?></td>
                                <td><?= $rb['bobot'] ?></td>
                                <td><?= $rb['indikator_penilaian'] ?></td>
                                <td><?= $rb['indikator_penilaian'] ?></td>
                                <td><?= $rb['kriteria_kurang'] ?></td>
                                <td><?= $rb['kriteria_sedang'] ?></td>
                                <td><?= $rb['kriteria_baik'] ?></td>

                                <td>
                                    <input type="text" name="nilai_<?= $rb['id']?>" id="nilai_<?= $rb['id']?>">
                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </form>

                    </tbody>

                </table>

            </div>
    
            <div class="col-lg-12 mt-3 d-flex justify-content-end">
                <form action="<?= base_url("dosen/setujuiSemua/".$pembimbing['id']) ?>" method="post" class="d-inline mr-2">
                    <button class="btn btn-success" type="submit"><i class="fa fa-times-circle" aria-hidden="true"></i> Setujui Semua</button>
                </form>
    
                <a role="button" href="<?= base_url("dosen/bimbingan") ?>"class="btn btn-secondary">Kembali</a>
            </div>

        </div>

    </div>



<?= $this->endSection(); ?>



<?= $this->section("scripts"); ?>

    <script src="<?= base_url("assets/js/dosen/rubrikPenilaian.js");?>"></script>

<?= $this->endSection(); ?>