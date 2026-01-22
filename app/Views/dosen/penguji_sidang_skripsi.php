<?= $this->extend("layout/template"); ?>



<?= $this->section("content"); ?>

<?php if (session()->getFlashdata("message")): ?>

    <div id="flashdata" data-open="true">

        <p id="icon" hidden>
            <?= session()->getFlashdata("message")["icon"]; ?>
        </p>

        <p id="title" hidden>
            <?= session()->getFlashdata("message")["title"]; ?>
        </p>

        <p id="text" hidden>
            <?= session()->getFlashdata("message")["text"]; ?>
        </p>

    </div>

<?php endif; ?>

<h1 class="h3 mb-2 text-gray-800">
    <?= $title ?>
</h1>

<div class="card shadow mb-4">

    <div class="card-header py-3">

        <?= $title ?>

    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-lg-5"></div>

            <div class="col-lg-7 d-flex justify-content-end align-items-center">

                <form class="form-inline" action="<?= base_url("dosen/pengujiSidangSkripsi") ?>" method="get"
                    id="semesterForm">
                    <div class="form-group">
                        <select class="form-control" id="semesterOption" name="semester">
                            <?php foreach ($semesterList as $semester): ?>
                                <?php if ($semester['status'] == 'AKTIF'): ?>
                                    <option value="<?= $semester['kode'] ?>" selected><?= $semester['nama'] ?> (Aktif)</option>
                                <?php else: ?>
                                    <option value="<?= $semester['kode'] ?>"><?= $semester['nama'] ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>

            </div>

        </div>

        <div class="table-responsive">

            <table class="table table-bordered" id="jadwalPengujiSidang" width="100%" cellspacing="0">

                <thead>

                    <tr>

                        <th>No</th>

                        <th>NPM</th>

                        <th>Nama</th>

                        <th>Judul</th>

                        <th>Bidang</th>

                        <th>Tanggal Sidang</th>

                        <th>Ruangan</th>

                        <th>Peran</th>

                        <th>Status</th>

                        <th>Aksi</th>

                    </tr>

                </thead>

                <tbody>

                    <?php $counter = 1; ?>

                    <?php foreach ($sidangSkripsi as $ss): ?>

                        <tr>

                            <td>
                                <?= $counter; ?>
                            </td>

                            <td class="npm">
                                <?= $ss['npm'] ?>
                            </td>

                            <td>
                                <?= $ss['nama_mahasiswa'] ?>
                            </td>

                            <td>
                                <?= $ss['judul'] ?>
                            </td>

                            <td data-toggle="tooltip" data-placement="top" title="<?= $ss['nama_bidang'] ?>"><?= $ss['inisial_bidang'] ?></td>



                            <td class="tanggal" style="min-width: 6vw">
                                <?= date_format(date_create($ss['tanggal']), 'd-m-Y H:i') ?> WIB
                            </td>



                            <?php if (preg_match('#^https?://#i', $ss['ruangan']) === 1): ?>

                                <td class="ruangan"><a href="<?= $ss['ruangan'] ?>" target="_blank">Klik disini!</a></td>

                            <?php else: ?>

                                <td class="ruangan">
                                    <?= $ss['ruangan'] ?>
                                </td>

                            <?php endif; ?>



                            <td>

                                <?php if ($dataAkun['id'] == $ss['id_pembimbing_1']): ?>

                                    Pembimbing Ilmu 1

                                <?php elseif ($dataAkun['id'] == $ss['id_pembimbing_2']): ?>

                                    Pembimbing Ilmu 2

                                <?php elseif ($dataAkun['id'] == $ss['id_pembimbing_agama']): ?>

                                    Pembimbing Agama

                                <?php elseif ($dataAkun['id'] == $ss['dosen_penguji']): ?>

                                    Penguji Murni

                                <?php elseif ($dataAkun['id'] == $ss['id_klien']): ?>

                                    Klien

                                <?php endif; ?>

                            </td>



                            <td>
                                <?= $ss['status_nilai'] == null ? 'Belum dinilai' : 'Sudah dinilai' ?>
                            </td>

                            <td>
                                <?php $tahunAkademik = explode("-", $ss['kode_semester'])[0] ?>
                                <?php $semester = explode("-", $ss['kode_semester'])[1] ?>

                                <?php if ($tahunAkademik <= BATAS_TAHUN_AKADEMIK_LAMA && $semester <= BATAS_SEMESTER_LAMA): ?>
                                    <a class="btn btn-primary"
                                        href="<?= base_url("dosen/penilaianSidangSkripsi/" . $ss['id']) ?>"
                                        data-toggle="tooltip" title="Penilaian"><i class="fas fa-clipboard-list"></i></a>
                                <?php else: ?>
                                    <a class="btn btn-primary"
                                        href="<?= base_url("dosen/penilaianSidangSkripsiN/" . $ss['id']) ?>"
                                        data-toggle="tooltip" title="Penilaian"><i class="fas fa-clipboard-list"></i></a>
                                <?php endif; ?>

                            </td>

                        </tr>

                        <?php $counter++ ?>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?= $this->endSection(); ?>



<?= $this->section("scripts"); ?>

<script src="<?= base_url("assets/js/dosen/pengujiSidangSkripsi.js"); ?>" defer></script>

<?= $this->endSection(); ?>