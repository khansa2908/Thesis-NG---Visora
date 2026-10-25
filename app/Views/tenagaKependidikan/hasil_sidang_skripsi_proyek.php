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

                    <label for="pembimbing">Pembimbing Ilmu</label>

                    <input type="text" name="pembimbing" id="pembimbing" class="form-control"
                        value="<?= $pembimbingIlmu[0]['nama_dosen'] ?>" disabled>

                </div>

            </div>

            <div class="col-3">

                <div class="form-group">

                    <label for="pembimbingAgama">Pembimbing Agama</label>

                    <input type="text" name="pembimbingAgama" id="pembimbingAgama" class="form-control"
                        value="<?= $pembimbingAgama[0]['nama_dosen'] ?>" disabled>

                </div>

            </div>

            <div class="col-3">

                <div class="form-group">

                    <label for="klien">Klien</label>

                    <input type="text" name="klien" id="klien" class="form-control"
                        value="<?= $klien['instansi'] ?>" disabled>

                </div>

            </div>

        </div>

        <?php foreach(array_column($totalBobot, 'pemangku_kepentingan') as $pp): ?>
            <h4 class="my-3">Nilai Sidang Skripsi oleh <?= ucwords(strtolower($pp)) ?></h4>

            <div class="table-responsive mt-3">
                <table class="table table-bordered table-hover table-striped" width="100%" cellspacing="0">
                    <thead>
                        <th class="text-center align-middle">No</th>
                        <th class="text-center align-middle">Cakupan</th>
                        <th class="text-center align-middle">Bobot</th>
                        <th class="text-center align-middle" colspan="12">Indikator</th>
                        <th class="text-center align-middle">Nilai</th>
                    </thead>
                    <tbody>
                        <?php $counter = 1; ?>
                        <?php switch ($pp) {
                            case 'PENGUJI':
                                $arrNilai = $nilaiPenguji;
                                $arrRubrik = $rubrikPenguji;
                                $total = $totalNilai['total_penguji'];
                                break;
                            case 'PEMBIMBING ILMU':
                                $arrNilai = $nilaiPembimbing;
                                $arrRubrik = $rubrikPembimbing;
                                $total = $totalNilai['total_pembimbing_ilmu'];
                                break;
                            case 'PEMBIMBING AGAMA':
                                $arrNilai = $nilaiPembimbingAgama;
                                $arrRubrik = $rubrikPembimbingAgama;
                                $total = $totalNilai['total_pembimbing_agama'];
                                break;
                            case 'KLIEN':
                                $arrNilai = $nilaiKlien;
                                $arrRubrik = $rubrikKlien;
                                $total = $totalNilai['total_klien'];
                                break;
                        } ?>
                        <?php foreach($arrRubrik as $rub): ?>
                            <tr>
                                <td class="text-center align-middle"><?= $counter++ ?></td>
                                <td class="align-middle"><?= $rub['deskripsi'] ?></td>
                                <td class="text-center align-middle"><?= $rub['bobot'] ?>%</td>
                                <?php $arrIndikator = explode(";", $rub['indikator']); ?>
                                <?php foreach ($arrIndikator as $b): ?>
                                    <td class="text-center align-middle" colspan="<?= 12 / count($arrIndikator) ?>">
                                        <p class="mb-1"><?= explode("~", $b)[0] ?></p>
                                        <small><?= explode("~", $b)[1] ?></small>
                                    </td>
                                <?php endforeach; ?>
                                <td class="align-middle text-center">
                                    <?php $indexNilai = array_search($rub['urutan'], array_column($arrNilai, 'id_rubrik')) ?>
                                    <?= $indexNilai >= 0 && $indexNilai !== false ? $arrNilai[$indexNilai]['nilai'] : "-" ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td class="text-center align-middle font-weight-bold" colspan="15">TOTAL</td>
                            <td class="text-center align-middle font-weight-bold"><?= $total ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>


        <?php if (
            count($nilaiPembimbing) == $totalBobot[array_search('PEMBIMBING ILMU', array_column($totalBobot, 'pemangku_kepentingan'))]['count_poin_penilaian'] && 
            count($nilaiPembimbingAgama) == $totalBobot[array_search('PEMBIMBING AGAMA', array_column($totalBobot, 'pemangku_kepentingan'))]['count_poin_penilaian'] && 
            count($nilaiPenguji) == $totalBobot[array_search('PENGUJI', array_column($totalBobot, 'pemangku_kepentingan'))]['count_poin_penilaian'] && 
            count($nilaiKlien) == $totalBobot[array_search('KLIEN', array_column($totalBobot, 'pemangku_kepentingan'))]['count_poin_penilaian']
            ): ?>

            <div class="row">

                <div class="col-lg-12 d-flex justify-content-center">

                    <h3><strong><?= $mahasiswa['nama'] ?></strong> dinyatakan <strong>
                            <?= $sidangSkripsi[0]['status'] ?>
                        </strong> dengan nilai <strong>
                            <?= $sidangSkripsi[0]['total_nilai'] ?> (<?= $sidangSkripsi[0]['grade'] == null ? "-" : $sidangSkripsi[0]['grade'] ?>)
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