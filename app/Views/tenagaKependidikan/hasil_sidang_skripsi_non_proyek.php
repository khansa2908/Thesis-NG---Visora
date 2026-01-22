<?= $this->extend("layout/template"); ?>



<?= $this->section("content"); ?>

<h1 class="h3 mb-2 text-gray-800">
    <?= $title ?>
</h1>

<div class="card shadow mb-4">

    <div class="card-header py-3">

        <?= $title ?>

    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-6">

                <div class="form-group">

                    <label for="nama">Nama Mahasiswa</label>

                    <input type="text" id="nama" name="nama" class="form-control" value="<?= $mahasiswa['nama'] ?>"
                        disabled>

                </div>

            </div>

            <div class="col-6">

                <div class="form-group">

                    <label for="npm">NPM</label>

                    <input type="text" id="npm" name="npm" class="form-control" value="<?= $mahasiswa['npm'] ?>"
                        disabled>

                </div>

            </div>

            <div class="col-12">

                <div class="form-group">

                    <label for="judul">Judul Skripsi</label>

                    <textarea name="judul" id="judul" rows="2" class="form-control"
                        disabled><?= $lastSkripsi['judul'] ?></textarea>

                </div>

            </div>

            <div class="col-3">

                <div class="form-group">

                    <label for="penguji">Penguji Ilmu</label>

                    <input type="text" name="penguji" id="penguji" class="form-control" value="<?= $penguji['nama'] ?>"
                        disabled>

                </div>

            </div>

            <div class="col-3">

                <div class="form-group">

                    <label for="pembimbing1">Pembimbing Ilmu 1</label>

                    <input type="text" name="pembimbing1" id="pembimbing1" class="form-control"
                        value="<?= $pembimbingIlmu1[0]['nama_dosen'] ?>" disabled>

                </div>

            </div>

            <div class="col-3">

                <div class="form-group">

                    <label for="pembimbing2">Pembimbing Ilmu 2</label>

                    <?php if ($pembimbingIlmu2[0]['id_dosen'] == null): ?>

                        <input type="text" name="pembimbing2" id="pembimbing2" class="form-control" value="-" disabled>

                    <?php else: ?>

                        <input type="text" name="pembimbing2" id="pembimbing2" class="form-control"
                            value="<?= $pembimbingIlmu2[0]['nama_dosen'] ?>" disabled>

                    <?php endif; ?>

                </div>

            </div>

            <div class="col-3">

                <div class="form-group">

                    <label for="pembimbingAgama">Pembimbing Agama</label>

                    <input type="text" name="pembimbingAgama" id="pembimbingAgama" class="form-control"
                        value="<?= $pembimbingAgama[0]['nama_dosen'] ?>" disabled>

                </div>

            </div>

        </div>

        <h4 class="my-3">Nilai Sidang Skripsi</h4>
        <div class="table-responsive mt-3">
            <table class="table table-sm table-bordered table-hover table-striped" id="hasilNilaiSidang" width="100%"
                cellspacing="0">

                <thead>
                    <th class="text-center align-middle">No</th>
                    <th class="text-center align-middle">Poin Penilaian</th>
                    <th class="text-center align-middle">Bobot</th>
                    <th class="text-center align-middle">Nilai Dosen Penguji</th>
                    <th class="text-center align-middle">Nilai Dosen Pembimbing Ilmu 1</th>

                    <?php if ($pembimbingIlmu2[0]['id_dosen'] != null): ?>
                        <th class="text-center align-middle">Nilai Dosen Pembimbing Ilmu 2</th>
                    <?php endif; ?>
        
                    <th class="text-center align-middle">Nilai Dosen Pembimbing Agama</th>
        
                </thead>
        
                <tbody>
                    <?php foreach ($rubrikNilai as $rn): ?>
                        <?php if ($rn['header']): ?>
                            <tr>
                                <td colspan="<?= $pembimbingIlmu2[0]['id_dosen'] != null ? 7 : 6 ?>"
                                    class="text-center align-middle">
                                    <strong>
                                        <?= $rn['header'] ?>
                                    </strong>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td class="text-center align-middle">
                                <?= $rn['urutan'] ?>.
                            </td>
        
                            <td class="align-middle">
                                <?= $rn['deskripsi'] ?>
                            </td>
        
                            <?php if ($rn['bobot'] != null): ?>
                                <td rowspan="3" class="text-center align-middle">
                                    <?= $rn['bobot'] ?>%
                                </td>
                            <?php endif; ?>
        
                            <td class="text-center align-middle">
                                <?php $indexPenguji = array_search($rn['urutan'], array_column($nilaiPenguji, 'id_rubrik')) ?>
                                <?= $indexPenguji >= 0 && $indexPenguji !== false ? $nilaiPenguji[$indexPenguji]['nilai'] : "-" ?>
                            </td>
        
                            <td class="text-center align-middle">
                                <?php $indexPembimbing1 = array_search($rn['urutan'], array_column($nilaiPembimbing1, 'id_rubrik')) ?>
                                <?= $indexPembimbing1 >= 0 && $indexPembimbing1 !== false ? $nilaiPembimbing1[$indexPembimbing1]['nilai'] : "-" ?>
                            </td>
        
                            <?php if ($pembimbingIlmu2[0]['id_dosen'] != null): ?>
                                <td class="text-center align-middle">
                                    <?php $indexPembimbing2 = array_search($rn['urutan'], array_column($nilaiPembimbing2, 'id_rubrik')) ?>
                                    <?= $indexPembimbing2 >= 0 && $indexPembimbing2 !== false ? $nilaiPembimbing2[$indexPembimbing2]['nilai'] : "-" ?>
                                </td>
                            <?php endif; ?>
        
                            <td class="text-center align-middle">
                                <?php $indexPembimbingAgama = array_search($rn['urutan'], array_column($nilaiPembimbingAgama, 'id_rubrik')) ?>
                                <?= $indexPembimbingAgama >= 0 && $indexPembimbingAgama !== false ? $nilaiPembimbingAgama[$indexPembimbingAgama]['nilai'] : "-" ?>
                            </td>
                        </tr>
        
                        <?php if ($rn['urutan'] % 3 == 0): ?>
                            <?php $section = ($rn['urutan'] / 3); ?>
                            <tr>
                                <td colspan="3"></td>
                                <td class="text-center align-middle">
                                    <strong>
                                        <?= $avgNilai['section' . $section . '_avg_penguji'] == null ? "-" : $avgNilai['section' . $section . '_avg_penguji'] ?>
                                    </strong>
                                </td>
                                <td class="text-center align-middle">
                                    <strong>
                                        <?= $avgNilai['section' . $section . '_avg_pembimbing1'] == null ? "-" : $avgNilai['section' . $section . '_avg_pembimbing1'] ?>
                                    </strong>
                                </td>
        
                                <?php if ($pembimbingIlmu2[0]['id_dosen'] != null): ?>
                                    <td class="text-center align-middle">
                                        <strong>
                                            <?= $avgNilai['section' . $section . '_avg_pembimbing2'] == null ? "-" : $avgNilai['section' . $section . '_avg_pembimbing2'] ?>
                                        </strong>
                                    </td>
                                <?php endif; ?>
        
                                <td class="text-center align-middle">
                                    <strong>
                                        <?= $avgNilai['section' . $section . '_avg_pembimbing_agama'] == null ? "-" : $avgNilai['section' . $section . '_avg_pembimbing_agama'] ?>
                                    </strong>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
        
                    <tr>
                        <td colspan="3" class="text-center align-middle"><b>Total Nilai</b></td>
        
                        <td class="text-center align-middle two-decimal-point">
                            <strong>
                                <?= $avgNilai['total_penguji'] == null ? "-" : $avgNilai['total_penguji'] ?>
                            </strong>
                        </td>
        
                        <td class="text-center align-middle two-decimal-point">
                            <strong>
                                <?= $avgNilai['total_pembimbing1'] == null ? "-" : $avgNilai['total_pembimbing1'] ?>
                            </strong>
                        </td>
        
                        <?php if ($pembimbingIlmu2[0]['id_dosen'] != null): ?>
                            <td class="text-center align-middle two-decimal-point">
                                <strong>
                                    <?= $avgNilai['total_pembimbing2'] == null ? "-" : $avgNilai['total_pembimbing2'] ?>
                                </strong>
                            </td>
                        <?php endif; ?>
        
                        <td class="text-center align-middle two-decimal-point">
                            <strong>
                                <?= $avgNilai['total_pembimbing_agama'] == null ? "-" : $avgNilai['total_pembimbing_agama'] ?>
                            </strong>
                        </td>
                    </tr>
        
                    <tr>
                        <td colspan="3" class="text-center align-middle"><b>Nilai Akhir</b></td>
        
                        <?php if ($pembimbingIlmu2[0]['id_dosen'] != null): ?>
                            <td colspan="4" class="text-center align-middle two-decimal-point">
                                <strong>
                                    <?= $avgNilai['nilai_akhir'] ?>
                                </strong>
                            </td>
                        <?php else: ?>
                            <td colspan="3" class="text-center align-middle two-decimal-point">
                                <strong>
                                    <?= $avgNilai['nilai_akhir'] ?>
                                </strong>
                            </td>
                        <?php endif; ?>
        
                    </tr>
        
                </tbody>
        
            </table>
        
        </div>
        
        
        
        <?php if (count($nilaiPembimbing1) != 0 && ($pembimbingIlmu2[0]['id_dosen'] == null || ($pembimbingIlmu2[0]['id_dosen'] != null && count($nilaiPembimbing2) != 0)) && count($nilaiPembimbingAgama) != 0 && count($nilaiPenguji) != 0): ?>
        
            <div class="row">
        
                <div class="col-lg-12 d-flex justify-content-center">
        
                    <h3><strong><?= $mahasiswa['nama'] ?></strong> dinyatakan <strong>
                            <?= $sidangSkripsi[0]['status'] ?>
                        </strong> dengan nilai <strong>
                            <?= $sidangSkripsi[0]['total_nilai'] ?>
                            (<?= $sidangSkripsi[0]['grade'] ?>)
                        </strong></h3>
        
                </div>
        
            </div>
        
        <?php endif; ?>



        <div class="col-lg-12 my-2 d-flex justify-content-end">

            <a class="btn btn-secondary mr-2" role="button"
                href="<?= base_url("TenagaKependidikan/penilaianSidang") ?>">Kembali</a>

        </div>

    </div>

</div>



<?= $this->endSection(); ?>



<?= $this->section("scripts"); ?>

<?= $this->endSection(); ?>