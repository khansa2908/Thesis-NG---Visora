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

            <input type="hidden" name="idSidangSkripsi" value="<?= $detailSidangSkripsi['id'] ?>">

            <input type="hidden" name="idDosen" value="<?= $dataAkun['id'] ?>">

            <div class="col-lg-6">

                <div class="form-group">

                    <label for="nama">Nama Mahasiswa</label>

                    <input type="text" class="form-control" id="nama" name="nama"
                        value="<?= $detailSidangSkripsi['nama_mahasiswa'] ?>" disabled>

                </div>

            </div>


            <div class="col-lg-6">

                <div class="form-group">

                    <label for="npm">NPM</label>

                    <input type="text" class="form-control" id="npm" name="npm"
                        value="<?= $detailSidangSkripsi['npm'] ?>" disabled>

                </div>

            </div>


            <div class="col-lg-6">

                <div class="form-group">

                    <label for="bidang">Bidang</label>

                    <input class="form-control" id="bidang" name="bidang"
                        value="<?= $detailSidangSkripsi['nama_bidang'] ?>" disabled>

                </div>

            </div>


            <div class="col-lg-6">

                <div class="form-group">

                    <label for="jenis_skripsi">Jenis Skripsi</label>

                    <input class="form-control" id="jenis_skripsi" name="jenis_skripsi"
                        value="<?= $detailSidangSkripsi['jenis_skripsi'] ?>" disabled>

                </div>

            </div>


            <div class="col-lg-12">

                <div class="form-group">

                    <label for="judul">Judul</label>

                    <textarea class="form-control" id="judul" name="judul"
                        disabled><?= $detailSidangSkripsi['judul'] ?></textarea>

                </div>

            </div>


            <div class="col-lg-6">

                <div class="form-group">

                    <label for="pembimbing1">Pembimbing Ilmu 1</label>

                    <input class="form-control" id="pembimbing1" name="pembimbing1"
                        value="<?= $detailSidangSkripsi['pembimbing1'] ?>" disabled>

                </div>

            </div>


            <div class="col-lg-6">

                <div class="form-group">

                    <label for="pembimbing2">Pembimbing Ilmu 2</label>

                    <input class="form-control" id="pembimbing2" name="pembimbing2"
                        value="<?= $detailSidangSkripsi['pembimbing2'] == null ? "-" : $detailSidangSkripsi['pembimbing2'] ?>"
                        disabled>

                </div>

            </div>


            <div class="col-lg-6">

                <div class="form-group">

                    <label for="pembimbingAgama">Pembimbing Agama</label>

                    <input class="form-control" id="pembimbingAgama" name="pembimbingAgama"
                        value="<?= $detailSidangSkripsi['pembimbing_agama'] ?>" disabled>

                </div>

            </div>


            <div class="col-lg-6">

                <div class="form-group">

                    <label for="penguji">Dosen Penguji</label>

                    <input class="form-control" id="penguji" name="penguji"
                        value="<?= $detailSidangSkripsi['nama_penguji'] ?>" disabled>

                </div>

            </div>


            <div class="col-lg-6">

                <div class="form-group">

                    <label for="klien">Klien</label>

                    <input class="form-control" id="klien" name="klien"
                        value="<?= $detailSidangSkripsi['instansi_klien'] == null ? "-" : $detailSidangSkripsi['instansi_klien'] ?>"
                        disabled>

                </div>

            </div>


            <div class="col-lg-6">

                <div class="form-group">

                    <label for="reviewer_sempro">Reviewer Seminar Proposal</label>

                    <input class="form-control" id="reviewer_sempro" name="reviewer_sempro"
                        value="<?= $detailSidangSkripsi['nama_reviewer_sempro'] ?>" disabled>

                </div>

            </div>



            <div class="col-lg-6">

                <div class="form-group">

                    <label for="reviewer_sempra">Reviewer Seminar Prasidang</label>

                    <input class="form-control" id="reviewer_sempra" name="reviewer_sempra"
                        value="<?= $detailSidangSkripsi['nama_reviewer_sempra'] ?>" disabled>

                </div>

            </div>



            <div class="col-lg-6">

                <div class="form-group">

                    <label for="rekomendasi_nilai">Rekomendasi Kluster Nilai</label>

                    <input class="form-control" id="rekomendasi_nilai" name="rekomendasi_nilai"
                        value="<?= $detailSidangSkripsi['rekomendasi_nilai'] ?>" disabled>

                </div>

            </div>



            <div class="col-lg-6">

                <div class="form-group">

                    <label for="komentar_sempro">Komentar Seminar Proposal</label>

                    <textarea class="form-control" id="komentar_sempro" name="komentar_sempro" rows="5"
                        disabled><?= $detailSidangSkripsi['komentar_sempro'] ?></textarea>

                </div>

            </div>



            <div class="col-lg-6">

                <div class="form-group">

                    <label for="komentar_sempra">Catatan Seminar Prasidang</label>

                    <textarea class="form-control" id="komentar_sempra" name="komentar_sempra" rows="5"
                        disabled><?= $detailSidangSkripsi['komentar_sempra'] ?></textarea>

                </div>

            </div>



            <div class="col-lg-12 mt-3">

                <h4 class="text-center">Penilaian Sidang</h4>

            </div>

            <div class="table-responsive">
                <table class="table table-sm table-striped table-bordered table-hover" id="tableRubrik" cellspacing="0">
                    <thead>
                        <tr>
                            <th rowspan="3" class="text-center align-middle">No</th>
                            <th rowspan="3" class="text-center align-middle">Poin Penilaian</th>
                            <th rowspan="3" class="text-center align-middle">Bobot</th>
                            <th class="text-center align-middle">Sangat Memuaskan</th>
                            <th class="text-center align-middle">Memuaskan</th>
                            <th class="text-center align-middle">Kurang Memuaskan</th>
                            <th class="text-center align-middle">Tidak Memuaskan</th>
                            <th rowspan="3" class="text-center align-middle">Nilai</th>
                        </tr>
                        <tr>
                            <th class="text-center align-middle">85-100</th>
                            <th class="text-center align-middle">70-84.99</th>
                            <th class="text-center align-middle">65-69.99</th>
                            <th class="text-center align-middle">&lt;65</th>
                        </tr>
                        <tr>
                            <th class="text-center align-middle">A</th>
                            <th class="text-center align-middle">A-, AB, B+</th>
                            <th class="text-center align-middle">B</th>
                            <th class="text-center align-middle">B-</th>
                        </tr>
                    </thead>
                    <tbody>
                        <form action="<?= base_url("dosen/insertNilaiSidangSkripsiN") ?>" method="post"
                            id="formPenilaianSidang">

                            <input type="hidden" value="<?= $dataAkun['id'] ?>" name="id_penilai">
                            <input type="hidden" value="<?= $peranPenilai ?>" name="peran_penilai">
                            <input type="hidden" value="<?= $detailSidangSkripsi['id'] ?>" name="id_sidang_skripsi">
                            <input type="hidden" value="<?= $detailSidangSkripsi['jenis_skripsi'] ?>"
                                name="jenis_skripsi">

                            <?php foreach ($rubrik as $rub): ?>
                                <?php if ($rub['header'] != null): ?>
                                    <tr>
                                        <td colspan="8" class="text-center align-middle">
                                            <strong>
                                                <?= $rub['header'] ?>
                                            </strong>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <tr>
                                    <td class="text-center align-middle">
                                        <?= $rub['urutan'] ?>.
                                    </td>
                                    <td class="align-middle">
                                        <?= $rub['deskripsi'] ?>
                                    </td>
                                    <?php if ($rub['bobot'] != null): ?>
                                        <td rowspan="3" class="text-center align-middle">
                                            <strong><?= $rub['bobot'] ?>%</strong>
                                        </td>
                                    <?php endif; ?>
                                    <td class="align-middle">
                                        <?= $rub['desk_sangat_memuaskan'] ?>%
                                    </td>
                                    <td class="align-middle">
                                        <?= $rub['desk_memuaskan'] ?>
                                    </td>
                                    <td class="align-middle">
                                        <?= $rub['desk_kurang_memuaskan'] ?>
                                    </td>
                                    <td class="align-middle">
                                        <?= $rub['desk_tidak_memuaskan'] ?>
                                    </td>
                                    <td class="align-middle">
                                        <?php $indexNilai = array_search($rub['urutan'], array_column($nilaiSaatIni, 'id_rubrik')) ?>

                                        <input type="number" name="nilai_<?= $rub["urutan"] ?>" min="0" max="100" step="1"
                                            value="<?= $indexNilai >= 0 && $indexNilai !== false ? $nilaiSaatIni[$indexNilai]['nilai'] : "" ?>"
                                            required>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td colspan="8" class="text-center align-middle">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </td>
                            </tr>
                        </form>
                    </tbody>
                </table>
            </div>


            <div class="col-lg-12 mb-3">

                <div class="form-group">

                    <label for="file_draft_final">Draft Final Skripsi</label>

                    <div class="embed-responsive embed-responsive-16by9 mx-auto" id="file_draft_final"
                        style="width: 90%">

                        <iframe class="embed-responsive-item"
                            src="<?= base_url("folderDraftFinal/" . $detailSidangSkripsi['file_draft_final']) ?>"></iframe>

                    </div>

                </div>

            </div>

            <div class="col-lg-12 mb-3">

                <div class="form-group">

                    <label for="file_form_bimbingan">Form Bimbingan Skripsi</label>

                    <div class="embed-responsive embed-responsive-16by9 mx-auto" id="file_form_bimbingan"
                        style="width: 90%">

                        <iframe class="embed-responsive-item"
                            src="<?= base_url("folderFormBimbingan/" . $detailSidangSkripsi['file_form_bimbingan']) ?>"></iframe>

                    </div>

                </div>

            </div>

            <div class="col-lg-12 mb-3">

                <div class="form-group">

                    <label for="file_persyaratan_sidang">Persyaratan Sidang</label>

                    <div class="embed-responsive embed-responsive-16by9 mx-auto" id="file_persyaratan_sidang"
                        style="width: 90%">

                        <iframe class="embed-responsive-item"
                            src="<?= base_url("folderPersyaratanSidang/" . $detailSidangSkripsi['file_persyaratan_sidang']) ?>"></iframe>

                    </div>

                </div>

            </div>



            <div class="col-lg-12 d-flex justify-content-end">

                <a role="button" class="btn btn-secondary mr-3"
                    href="<?= base_url("dosen/pengujiSidangSkripsi") ?>">Kembali</a>

            </div>

        </div>

    </div>

</div>

<?= $this->endSection(); ?>



<?= $this->section("scripts"); ?>

<script src="<?= base_url("assets/js/dosen/penilaianSidangNonProyek.js"); ?>" defer></script>

<?= $this->endSection(); ?>