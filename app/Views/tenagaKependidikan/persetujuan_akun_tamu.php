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

                <table class="table table-bordered" id="listAkunTamu" width="100%" cellspacing="0">

                    <thead>

                        <tr>

                            <th>No</th>

                            <th>Nama</th>

                            <th>Nama Instansi</th>

                            <th>Email</th>

                            <th>Tanggal Pendaftaran</th>

                            <th>Aksi</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($listAkunTamu as $lat) : ?>

                            <tr>

                                <td></td>
                                <td><?= $lat['nama'] ?> (<?= $lat['inisial'] ?>)</td>
                                <td><?= $lat['instansi'] ?></td>
                                <td><?= $lat['email'] ?></td>
                                <td><?= date_format(date_create($lat['tanggal_pendaftaran']), 'd-m-Y H:i:s') ?> WIB</td>

                                <td>
                                    <form action="<?= base_url("TenagaKependidikan/setujuiAkunTamu/".$lat['id']."/dospem") ?>" method="post" id="setujuiAkunDosPem" class="d-inline">
                                        <button class="btn btn-success" type="button" data-toggle="tooltip" data-placement="top" title="Setujui akun sebagai DosPem Eksternal"><i class="fas fa-user-graduate" aria-hidden="true"></i></button>
                                    </form>

                                    <form action="<?= base_url("TenagaKependidikan/setujuiAkunTamu/".$lat['id']."/klien") ?>" method="post" id="setujuiAkunKlien" class="d-inline">
                                        <button class="btn btn-success" type="button" data-toggle="tooltip" data-placement="top" title="Setujui akun sebagai Klien"><i class="fas fa-user-tie" aria-hidden="true"></i></button>
                                    </form>

                                    <form action="<?= base_url("TenagaKependidikan/tolakAkunTamu/".$lat['id']) ?>" method="post" id="tolakAkun" class="d-inline">
                                        <button class="btn btn-danger" type="button" data-toggle="tooltip" data-placement="top" title="Tolak akun"><i class="fa fa-times-circle" aria-hidden="true"></i></button>
                                    </form>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>
    
        </div>

    </div>



<?= $this->endSection(); ?>



<?= $this->section("scripts"); ?>

    <script src="<?= base_url("assets/js/tenagaKependidikan/persetujuanAkunTamu.js");?>"></script>

<?= $this->endSection(); ?>