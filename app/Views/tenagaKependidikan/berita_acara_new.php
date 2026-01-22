<?php

$bulanDict = [

    "01" => "Januari",

    "02" => "Februari",

    "03" => "Maret",

    "04" => "April",

    "05" => "Mei",

    "06" => "Juni",

    "07" => "Juli",

    "08" => "Agustus",

    "09" => "September",

    "10" => "Oktober",

    "11" => "November",

    "12" => "Desember",

];



$hariDict = [

    "0" => "Minggu",

    "1" => "Senin",

    "2" => "Selasa",

    "3" => "Rabu",

    "4" => "Kamis",

    "5" => "Jumat",

    "6" => "Sabtu",

];



$today = new DateTime("now", new DateTimeZone('Asia/Jakarta'));

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Berita Acara</title>

    <style>
        
        .text-center {

            text-align: center;

        }



        .table-center {

            margin-left: auto;

            margin-right: auto;

        }



        .panel-heading {

            margin-bottom: 0;

        }



        p {

            text-align: justify;

            text-justify: inter-word;

        }



        .container {

            display: inline-block;

            vertical-align: middle;

        }



        .box {

            display: inline-block;

            box-sizing: border-box;

            height: 50px;

            width: 75px;

            border: 1px solid;

            font-size: 24px;

            line-height: 50px;

        }



        input[type='checkbox'] {

            accent-color: #34495e
        }



        @media print {

            .new-page {

                page-break-before: always;

                break-before: page;

            }



            @page {

                size: A4;

            }

        }
    </style>

</head>

<body onload="window.print()">

    <?php $counter = 1; ?>

    <?php foreach ($arrBeritaAcara as $aba): ?>
        <?php if ($aba['lastSkripsi']['jenis'] == JENIS_SKRIPSI_PROYEK_MANDIRI || $aba['lastSkripsi']['jenis'] == JENIS_SKRIPSI_PROYEK_TIM): ?>
            <div>

                <td height="165" colspan="3" class="text-center">

                    <p class="panel-heading">

                        <img src="<?= base_url("assets/img/fti.jpg") ?>" width="157" height="62" align="absmiddle">

                    </p>

                </td>



                <table width="100%" style="border: 0" class="table-center">

                    <tr>

                        <td class="text-center">

                            <h4>BERITA ACARA SIDANG PROYEK AKHIR</h4>
                            <p style="margin: 0; text-align: center; font-weight: bold; font-size: 14px;">
                                Nomor: <?= !empty($aba['sidangSkripsi']['nomor_berita_acara']) ? $aba['sidangSkripsi']['nomor_berita_acara'] : '..................................................' ?>
                            </p>
                        </td>

                    </tr>



                    <tr>

                        <td>

                            <p>Yang bertanda tangan di bawah ini Komisi Penguji Proyek Akhir Program Studi <?= $aba['prodi']['nama'] ?> Program Sarjana Fakultas Teknologi Informasi Universitas YARSI dengan ini menyatakan dengan sebenarnya bahwa pada:</p>

                        </td>

                    </tr>



                    <tr>

                        <table width="80%" style="border: 0" class="table-center">

                            <tr width="100%">

                                <td width="20%">Hari/Tanggal</td>

                                <td width="4%">:</td>

                                <?php $tanggal = new DateTime($aba['sidangSkripsi']['tanggal'], new DateTimeZone('Asia/Jakarta')) ?>

                                <td width="76%">
                                    <?= $hariDict[$tanggal->format("w")] ?>,
                                    <?= $tanggal->format('d') ?>
                                    <?= $bulanDict[$tanggal->format("m")] ?>
                                    <?= $tanggal->format("Y") ?>
                                </td>

                            </tr>

                            <tr width="100%">

                                <td width="20%">Jam</td>

                                <td width="4%">:</td>

                                <td width="76%">
                                    <?= $tanggal->format('H:i') ?> WIB
                                </td>

                            </tr>

                            <tr width="100%">

                                <td width="20%">Bertempat di</td>

                                <td width="4%">:</td>

                                <td width="76%">
                                    <?= preg_match('#^https?://#i', $aba['sidangSkripsi']['ruangan']) === 1 ? 'Link Zoom' : $aba['sidangSkripsi']['ruangan'] ?>
                                </td>

                            </tr>

                        </table>

                    </tr>



                    <tr>

                        <td>

                            <p>Telah menyelengarakan Sidang Proyek Akhir terhadap Mahasiswa:</p>

                        </td>

                    </tr>



                    <tr>

                        <table width="80%" style="border: 0" class="table-center">

                            <tr width="100%">

                                <td width="20%">Nama</td>

                                <td width="4%">:</td>

                                <td width="76%">
                                    <?= $aba['mahasiswa']['nama'] ?>
                                </td>

                            </tr>

                            <tr width="100%">

                                <td width="20%">NPM</td>

                                <td width="4%">:</td>

                                <td width="76%">
                                    <?= $aba['mahasiswa']['npm'] ?>
                                </td>

                            </tr>

                            <tr width="100%">

                                <td width="20%">Program Studi</td>

                                <td width="4%">:</td>

                                <td width="76%">
                                    <?= $aba['prodi']['nama'] ?> - S1
                                </td>

                            </tr>

                        </table>

                    </tr>



                    <tr>

                        <td>

                            <p>Dengan judul : <strong>“<?= $aba['lastSkripsi']['judul'] ?>”</strong></p>

                        </td>

                    </tr>



                    <tr>

                        <td style="height: 150px" ;>

                            <p style="display: inline-block;">Hasil ujian dengan kualifikasi:</p>

                        </td>



                        <td>

                            <div class="container">

                                <div class="box text-center"><strong><?= $aba['sidangSkripsi']['grade'] == null ? "-" : $aba['sidangSkripsi']['grade'] ?></strong></div>

                                <div class="box text-center"><strong><?= number_format($aba['sidangSkripsi']['total_nilai'], 2, ".", "") ?></strong></div>

                            </div>

                        </td>

                    </tr>



                    <br><br>



                    <tr>

                        <td width="100%">

                            <p style="margin-bottom: 0;" class="text-center">Jakarta,
                                <?= $today->format("d") ?>
                                <?= $bulanDict[$today->format("m")] ?>
                                <?= $today->format("Y") ?>
                            </p>

                        </td>

                    </tr>



                    <tr>

                        <td>

                            <table width="100%" class="table-center">

                                <tr>

                                    <td class="text-center">Ketua Komisi Penguji</td>

                                </tr>

                                <tr>

                                    <td><br><br><br><br><br></td>

                                </tr>

                                <tr>

                                    <td class="text-center"><strong>(<?= $aba['penguji']['nama'] ?>)
                                        </strong></td>

                                </tr>

                            </table>

                        </td>

                    </tr>



                    <br><br>



                    <tr>

                        <td>

                            <table width="100%" class="table-center">

                                <tr>

                                    <td class="text-center" width="25%">Penguji Murni</td>

                                    <td class="text-center" width="25%">Pembimbing Ilmu / Penguji 1</td>

                                    <td class="text-center" width="25%">Pembimbing Agama / Penguji 2</td>

                                    <td class="text-center" width="25%">Klien</td>

                                </tr>

                                <tr>

                                    <td width="25%"><br><br><br><br><br></td>

                                    <td width="25%"><br><br><br><br><br></td>

                                    <td width="25%"><br><br><br><br><br></td>

                                    <td width="25%"><br><br><br><br><br></td>

                                </tr>

                                <tr>

                                    <td class="text-center" width="25%"><strong>(<?= $aba['penguji']['nama'] ?>)
                                        </strong></td>

                                    <td class="text-center" width="25%"><strong>(<?= $aba['pembimbingIlmu'][0]['nama_dosen'] ?>)
                                        </strong></td>

                                    <td class="text-center" width="25%"><strong>(<?= $aba['pembimbingAgama'][0]['nama_dosen'] ?>)
                                        </strong></td>

                                    <td class="text-center" width="25%"><strong>(<?= $aba['klien']['instansi'] ?>)
                                        </strong></td>

                                </tr>

                            </table>

                        </td>

                    </tr>

                </table>

            </div>



            <div class="new-page"></div>



                    <div>

                        <td height="165" colspan="3" class="text-center">

                            <p class="panel-heading">

                                <img src="<?= base_url("assets/img/fti.jpg") ?>" width="157" height="62" align="absmiddle">

                            </p>

                        </td>



                        <table width="100%" style="border: 0" class="table-center">

                            <tr>

                                <td class="text-center">

                                    <h4>PENILAIAN SIDANG PROYEK AKHIR</h4>

                                </td>

                            </tr>



                            <tr>

                                <table width="100%" style="border: 0">

                                    <tr width="100%">

                                        <td width="20%"><strong>Nama Mahasiswa</strong></td>

                                        <td width="5%">:</td>

                                        <td width="75%">
                                            <?= $aba['mahasiswa']['nama'] ?>
                                        </td>

                                    </tr>

                                    <tr width="100%">

                                        <td width="20%"><strong>NPM</strong></td>

                                        <td width="5%">:</td>

                                        <td width="75%">
                                            <?= $aba['mahasiswa']['npm'] ?>
                                        </td>

                                    </tr>

                                    <tr width="100%">

                                        <td width="20%"><strong>Program Studi</strong></td>

                                        <td width="5%">:</td>

                                        <td width="75%">
                                            <?= $aba['prodi']['nama'] ?> Program Sarjana
                                        </td>

                                    </tr>

                                    <tr width="100%">

                                        <td width="20%"><strong>Judul Proyek</strong></td>

                                        <td width="5%">:</td>

                                        <td width="75%">
                                            <?= $aba['lastSkripsi']['judul'] ?>
                                        </td>

                                    </tr>

                                </table>

                            </tr>


                            <tr>

                                <td>

                                    <p style="margin-bottom: 0"><STRONG>Penguji</STRONG></p>

                                    <table width="100%" style="border: 1px solid; border-collapse: collapse; font-size: .7em;"
                                        cellpadding="2" class="table-center">

                                        <tr>

                                            <th style="border: 1px solid;">CAKUPAN</th>

                                            <th style="border: 1px solid;">BOBOT</th>

                                            <th style="border: 1px solid;" colspan="12">INDIKATOR</th>

                                            <th style="border: 1px solid;">NILAI</th>


                                        </tr>

                                        <?php $arrNilai = $aba['nilaiPenguji']; ?>
                                        <?php foreach ($aba['rubrikPenguji'] as $rp): ?>
                                                <tr width="100%">

                                                    <td style="border: 1px solid;">
                                                        <?= $rp['deskripsi'] ?>
                                                    </td>

                                                    <td class="text-center" style="border: 1px solid;">
                                                        <?= $rp['bobot'] ?>%
                                                    </td>

                                                    <?php $arrIndikator = explode(";", $rp['indikator']); ?>
                                                    <?php foreach ($arrIndikator as $b): ?>
                                                            <td style="border: 1px solid;" colspan="<?= 12 / count($arrIndikator) ?>">
                                                                <p class="text-center">
                                                                    <?= explode("~", $b)[0] ?>
                                                                </p>
                                                                <p class="text-center">
                                                                    <?= explode("~", $b)[1] ?>
                                                                </p>
                                                            </td>
                                                    <?php endforeach; ?>

                                                    <td class="text-center" style="border: 1px solid;">
                                                        <?php $indexNilai = array_search($rp['urutan'], array_column($arrNilai, 'id_rubrik')) ?>
                                                        <?= $indexNilai >= 0 && $indexNilai !== false ? $arrNilai[$indexNilai]['nilai'] : "-" ?>
                                                    </td>

                                                </tr>
                                        <?php endforeach; ?>

                                        <tr>
                                            <td class="text-center" colspan="14" style="border: 1px solid; font-size: 1rem;">
                                                <strong>Total<Strong></td>
                                            <td class="text-center" style="border: 1px solid; font-size: 1rem;"><strong>
                                                    <?= $aba['nilaiAkhir']['total_penguji'] ?>
                                                </strong></td>
                                        </tr>


                                    </table>

                                </td>

                            </tr>

                            <br><br>

                            <tr>
                                <td>
                                    <table width="100%" class="table-center">

                                        <tr>
                                            <td width="60%"></td>
                                            <td width="40%" class="text-center">Penguji Murni</td>
                                        </tr>

                                        <tr>
                                            <td width="60%"></td>
                                            <td width="40%"><br><br><br><br></td>
                                        </tr>

                                        <tr>
                                            <td width="60%"></td>
                                            <td width="40%" class="text-center"><strong>(<?= $aba['penguji']['nama'] ?>)
                                                </strong></td>
                                        </tr>

                                    </table>
                                </td>
                            </tr>

                        </table>





                    </div>

                    <div class="new-page"></div>



                    <div>

                        <td height="165" colspan="3" class="text-center">

                            <p class="panel-heading">

                                <img src="<?= base_url("assets/img/fti.jpg") ?>" width="157" height="62" align="absmiddle">

                            </p>

                        </td>



                        <table width="100%" style="border: 0" class="table-center">

                            <tr>

                                <td class="text-center">

                                    <h4>PENILAIAN SIDANG PROYEK AKHIR</h4>

                                </td>

                            </tr>



                            <tr>

                                <table width="100%" style="border: 0">

                                    <tr width="100%">

                                        <td width="20%"><strong>Nama Mahasiswa</strong></td>

                                        <td width="5%">:</td>

                                        <td width="75%">
                                            <?= $aba['mahasiswa']['nama'] ?>
                                        </td>

                                    </tr>

                                    <tr width="100%">

                                        <td width="20%"><strong>NPM</strong></td>

                                        <td width="5%">:</td>

                                        <td width="75%">
                                            <?= $aba['mahasiswa']['npm'] ?>
                                        </td>

                                    </tr>

                                    <tr width="100%">

                                        <td width="20%"><strong>Program Studi</strong></td>

                                        <td width="5%">:</td>

                                        <td width="75%">
                                            <?= $aba['prodi']['nama'] ?> Program Sarjana
                                        </td>

                                    </tr>

                                    <tr width="100%">

                                        <td width="20%"><strong>Judul Proyek</strong></td>

                                        <td width="5%">:</td>

                                        <td width="75%">
                                            <?= $aba['lastSkripsi']['judul'] ?>
                                        </td>

                                    </tr>

                                </table>

                            </tr>


                            <tr>

                                <td>

                                    <p style="margin-bottom: 0"><STRONG>Pembimbing Ilmu</STRONG></p>

                                    <table width="100%" style="border: 1px solid; border-collapse: collapse; font-size: .7em;"
                                        cellpadding="2" class="table-center">

                                        <tr>

                                            <th style="border: 1px solid;">CAKUPAN</th>

                                            <th style="border: 1px solid;">BOBOT</th>

                                            <th style="border: 1px solid;" colspan="12">INDIKATOR</th>

                                            <th style="border: 1px solid;">NILAI</th>


                                        </tr>

                                        <?php $arrNilai = $aba['nilaiPembimbingIlmu'] ?>
                                        <?php foreach ($aba['rubrikPembimbingIlmu'] as $rp): ?>
                                                <tr width="100%">

                                                    <td style="border: 1px solid;">
                                                        <?= $rp['deskripsi'] ?>
                                                    </td>

                                                    <td class="text-center" style="border: 1px solid;">
                                                        <?= $rp['bobot'] ?>%
                                                    </td>

                                                    <?php $arrIndikator = explode(";", $rp['indikator']); ?>
                                                    <?php foreach ($arrIndikator as $b): ?>
                                                            <td style="border: 1px solid;" colspan="<?= 12 / count($arrIndikator) ?>">
                                                                <p class="text-center">
                                                                    <?= explode("~", $b)[0] ?>
                                                                </p>
                                                                <p class="text-center">
                                                                    <?= explode("~", $b)[1] ?>
                                                                </p>
                                                            </td>
                                                    <?php endforeach; ?>

                                                    <td class="text-center" style="border: 1px solid;">
                                                        <?php $indexNilai = array_search($rp['urutan'], array_column($arrNilai, 'id_rubrik')) ?>
                                                        <?= $indexNilai >= 0 && $indexNilai !== false ? $arrNilai[$indexNilai]['nilai'] : "-" ?>
                                                    </td>

                                                </tr>
                                        <?php endforeach; ?>

                                        <tr>
                                            <td class="text-center" colspan="14" style="border: 1px solid; font-size: 1rem;">
                                                <strong>Total<Strong></td>
                                            <td class="text-center" style="border: 1px solid; font-size: 1rem;"><strong>
                                                    <?= $aba['nilaiAkhir']['total_pembimbing_ilmu'] ?>
                                                </strong></td>
                                        </tr>
                                    </table>

                                </td>

                            </tr>


                            <br><br>

                            <tr>
                                <td>
                                    <table width="100%" class="table-center">

                                        <tr>
                                            <td width="60%"></td>
                                            <td width="40%" class="text-center">Pembimbing Ilmu</td>
                                        </tr>

                                        <tr>
                                            <td width="60%"></td>
                                            <td width="40%"><br><br><br><br></td>
                                        </tr>

                                        <tr>
                                            <td width="60%"></td>
                                            <td width="40%" class="text-center"><strong>(<?= $aba['pembimbingIlmu'][0]['nama_dosen'] ?>)
                                                </strong></td>
                                        </tr>

                                    </table>
                                </td>
                            </tr>

                        </table>





                    </div>

                    <div class="new-page"></div>



                    <div>

                        <td height="165" colspan="3" class="text-center">

                            <p class="panel-heading">

                                <img src="<?= base_url("assets/img/fti.jpg") ?>" width="157" height="62" align="absmiddle">

                            </p>

                        </td>



                        <table width="100%" style="border: 0" class="table-center">

                            <tr>

                                <td class="text-center">

                                    <h4>PENILAIAN SIDANG PROYEK AKHIR</h4>

                                </td>

                            </tr>



                            <tr>

                                <table width="100%" style="border: 0">

                                    <tr width="100%">

                                        <td width="20%"><strong>Nama Mahasiswa</strong></td>

                                        <td width="5%">:</td>

                                        <td width="75%">
                                            <?= $aba['mahasiswa']['nama'] ?>
                                        </td>

                                    </tr>

                                    <tr width="100%">

                                        <td width="20%"><strong>NPM</strong></td>

                                        <td width="5%">:</td>

                                        <td width="75%">
                                            <?= $aba['mahasiswa']['npm'] ?>
                                        </td>

                                    </tr>

                                    <tr width="100%">

                                        <td width="20%"><strong>Program Studi</strong></td>

                                        <td width="5%">:</td>

                                        <td width="75%">
                                            <?= $aba['prodi']['nama'] ?> Program Sarjana
                                        </td>

                                    </tr>

                                    <tr width="100%">

                                        <td width="20%"><strong>Judul Proyek</strong></td>

                                        <td width="5%">:</td>

                                        <td width="75%">
                                            <?= $aba['lastSkripsi']['judul'] ?>
                                        </td>

                                    </tr>

                                </table>

                            </tr>


                            <tr>

                                <td>

                                    <p style="margin-bottom: 0"><STRONG>Pembimbing Agama</STRONG></p>

                                    <table width="100%" style="border: 1px solid; border-collapse: collapse; font-size: .7em;"
                                        cellpadding="2" class="table-center">

                                        <tr>

                                            <th style="border: 1px solid;">CAKUPAN</th>

                                            <th style="border: 1px solid;">BOBOT</th>

                                            <th style="border: 1px solid;" colspan="12">INDIKATOR</th>

                                            <th style="border: 1px solid;">NILAI</th>


                                        </tr>

                                        <?php $arrNilai = $aba['nilaiPembimbingAgama'] ?>
                                        <?php foreach ($aba['rubrikPembimbingAgama'] as $rp): ?>
                                                <tr width="100%">

                                                    <td style="border: 1px solid;">
                                                        <?= $rp['deskripsi'] ?>
                                                    </td>

                                                    <td class="text-center" style="border: 1px solid;">
                                                        <?= $rp['bobot'] ?>%
                                                    </td>

                                                    <?php $arrIndikator = explode(";", $rp['indikator']); ?>
                                                    <?php foreach ($arrIndikator as $b): ?>
                                                            <td style="border: 1px solid;" colspan="<?= 12 / count($arrIndikator) ?>">
                                                                <p class="text-center">
                                                                    <?= explode("~", $b)[0] ?>
                                                                </p>
                                                                <p class="text-center">
                                                                    <?= explode("~", $b)[1] ?>
                                                                </p>
                                                            </td>
                                                    <?php endforeach; ?>

                                                    <td class="text-center" style="border: 1px solid;">
                                                        <?php $indexNilai = array_search($rp['urutan'], array_column($arrNilai, 'id_rubrik')) ?>
                                                        <?= $indexNilai >= 0 && $indexNilai !== false ? $arrNilai[$indexNilai]['nilai'] : "-" ?>
                                                    </td>

                                                </tr>
                                        <?php endforeach; ?>

                                        <tr>
                                            <td class="text-center" colspan="14" style="border: 1px solid; font-size: 1rem;">
                                                <strong>Total<Strong></td>
                                            <td class="text-center" style="border: 1px solid; font-size: 1rem;"><strong>
                                                    <?= $aba['nilaiAkhir']['total_pembimbing_agama'] ?>
                                                </strong></td>
                                        </tr>
                                    </table>

                                </td>

                            </tr>


                            <br><br>

                            <tr>
                                <td>
                                    <table width="100%" class="table-center">

                                        <tr>
                                            <td width="60%"></td>
                                            <td width="40%" class="text-center">Pembimbing Agama</td>
                                        </tr>

                                        <tr>
                                            <td width="60%"></td>
                                            <td width="40%"><br><br><br><br></td>
                                        </tr>

                                        <tr>
                                            <td width="60%"></td>
                                            <td width="40%" class="text-center"><strong>(<?= $aba['pembimbingAgama'][0]['nama_dosen'] ?>)
                                                </strong></td>
                                        </tr>

                                    </table>
                                </td>
                            </tr>

                        </table>





                    </div>

                    <div class="new-page"></div>



                    <div>

                        <td height="165" colspan="3" class="text-center">

                            <p class="panel-heading">

                                <img src="<?= base_url("assets/img/fti.jpg") ?>" width="157" height="62" align="absmiddle">

                            </p>

                        </td>



                        <table width="100%" style="border: 0" class="table-center">

                            <tr>

                                <td class="text-center">

                                    <h4>PENILAIAN SIDANG PROYEK AKHIR</h4>

                                </td>

                            </tr>



                            <tr>

                                <table width="100%" style="border: 0">

                                    <tr width="100%">

                                        <td width="20%"><strong>Nama Mahasiswa</strong></td>

                                        <td width="5%">:</td>

                                        <td width="75%">
                                            <?= $aba['mahasiswa']['nama'] ?>
                                        </td>

                                    </tr>

                                    <tr width="100%">

                                        <td width="20%"><strong>NPM</strong></td>

                                        <td width="5%">:</td>

                                        <td width="75%">
                                            <?= $aba['mahasiswa']['npm'] ?>
                                        </td>

                                    </tr>

                                    <tr width="100%">

                                        <td width="20%"><strong>Program Studi</strong></td>

                                        <td width="5%">:</td>

                                        <td width="75%">
                                            <?= $aba['prodi']['nama'] ?> Program Sarjana
                                        </td>

                                    </tr>

                                    <tr width="100%">

                                        <td width="20%"><strong>Judul Proyek</strong></td>

                                        <td width="5%">:</td>

                                        <td width="75%">
                                            <?= $aba['lastSkripsi']['judul'] ?>
                                        </td>

                                    </tr>

                                </table>

                            </tr>


                            <tr>

                                <td>

                                    <p style="margin-bottom: 0"><STRONG>Klien</STRONG></p>

                                    <table width="100%" style="border: 1px solid; border-collapse: collapse; font-size: .7em;"
                                        cellpadding="2" class="table-center">

                                        <tr>

                                            <th style="border: 1px solid;">CAKUPAN</th>

                                            <th style="border: 1px solid;">BOBOT</th>

                                            <th style="border: 1px solid;" colspan="12">INDIKATOR</th>

                                            <th style="border: 1px solid;">NILAI</th>


                                        </tr>

                                        <?php $arrNilai = $aba['nilaiKlien'] ?>
                                        <?php foreach ($aba['rubrikKlien'] as $rp): ?>
                                                <tr width="100%">

                                                    <td style="border: 1px solid;">
                                                        <?= $rp['deskripsi'] ?>
                                                    </td>

                                                    <td class="text-center" style="border: 1px solid;">
                                                        <?= $rp['bobot'] ?>%
                                                    </td>

                                                    <?php $arrIndikator = explode(";", $rp['indikator']); ?>
                                                    <?php foreach ($arrIndikator as $b): ?>
                                                            <td style="border: 1px solid;" colspan="<?= 12 / count($arrIndikator) ?>">
                                                                <p class="text-center">
                                                                    <?= explode("~", $b)[0] ?>
                                                                </p>
                                                                <p class="text-center">
                                                                    <?= explode("~", $b)[1] ?>
                                                                </p>
                                                            </td>
                                                    <?php endforeach; ?>

                                                    <td class="text-center" style="border: 1px solid;">
                                                        <?php $indexNilai = array_search($rp['urutan'], array_column($arrNilai, 'id_rubrik')) ?>
                                                        <?= $indexNilai >= 0 && $indexNilai !== false ? $arrNilai[$indexNilai]['nilai'] : "-" ?>
                                                    </td>

                                                </tr>
                                        <?php endforeach; ?>

                                        <tr>
                                            <td class="text-center" colspan="14" style="border: 1px solid; font-size: 1rem;">
                                                <strong>Total<Strong></td>
                                            <td class="text-center" style="border: 1px solid; font-size: 1rem;"><strong>
                                                    <?= $aba['nilaiAkhir']['total_klien'] ?>
                                                </strong></td>
                                        </tr>
                                    </table>

                                </td>

                            </tr>


                            <br><br>

                            <tr>
                                <td>
                                    <table width="100%" class="table-center">

                                        <tr>
                                            <td width="60%"></td>
                                            <td width="40%" class="text-center">Klien</td>
                                        </tr>

                                        <tr>
                                            <td width="60%"></td>
                                            <td width="40%"><br><br><br><br></td>
                                        </tr>

                                        <tr>
                                            <td width="60%"></td>
                                            <td width="40%" class="text-center"><strong>(<?= $aba['klien']['instansi'] ?>)
                                                </strong></td>
                                        </tr>

                                    </table>
                                </td>
                            </tr>

                        </table>





                    </div>

                    <div class="new-page"></div>

                    <div>
                        <td height="165" colspan="3" class="text-center">
                            <p class="panel-heading">
                                <img src="<?= base_url("assets/img/fti.jpg") ?>" width="157" height="62" align="absmiddle">
                            </p>
                        </td>

                        <table width="100%" style="border: 0" class="table-center">
                            <tr>
                                <td class="text-center">
                                    <h4>PENILAIAN SIDANG PROYEK AKHIR</h4>
                                </td>
                            </tr>

                            <tr>
                                <table width="100%" style="border: 0">
                                    <tr width="100%">
                                        <td width="20%"><strong>Nama Mahasiswa</strong></td>
                                        <td width="5%">:</td>
                                        <td width="75%">
                                            <?= $aba['mahasiswa']['nama'] ?>
                                        </td>
                                    </tr>

                                    <tr width="100%">
                                        <td width="20%"><strong>NPM</strong></td>
                                        <td width="5%">:</td>
                                        <td width="75%">
                                            <?= $aba['mahasiswa']['npm'] ?>
                                        </td>
                                    </tr>

                                    <tr width="100%">
                                        <td width="20%"><strong>Program Studi</strong></td>
                                        <td width="5%">:</td>
                                        <td width="75%">
                                            <?= $aba['prodi']['nama'] ?> Program Sarjana
                                        </td>
                                    </tr>

                                    <tr width="100%">
                                        <td width="20%"><strong>Judul Proyek</strong></td>
                                        <td width="5%">:</td>
                                        <td width="75%">
                                            <?= $aba['lastSkripsi']['judul'] ?>
                                        </td>
                                    </tr>
                                </table>
                            </tr>


                            <tr>
                                <td>
                                    <p style="margin-bottom: 0"></p>
                                    <table width="100%" style="border: 1px solid; border-collapse: collapse;" cellpadding="2"
                                        class="table-center">
                                        <tr>
                                            <th style="border: 1px solid;">NO</th>
                                            <th style="border: 1px solid;">PENILAI</th>
                                            <th style="border: 1px solid;">NILAI</th>
                                        </tr>

                                        <tr width="100%">
                                            <td class="text-center" style="border: 1px solid;">1</td>
                                            <td style="border: 1px solid;">Penguji Murni / Ketua Penguji</td>
                                            <td class="text-center" style="border: 1px solid;">
                                                <?= $aba['nilaiAkhir']['total_penguji'] ?>
                                            </td>
                                        </tr>

                                        <tr width="100%">
                                            <td class="text-center" style="border: 1px solid;">2</td>
                                            <td style="border: 1px solid;">Pembimbing Ilmu / Penguji 1</td>
                                            <td class="text-center" style="border: 1px solid;">
                                                <?= $aba['nilaiAkhir']['total_pembimbing_ilmu'] ?>
                                            </td>
                                        </tr>

                                        <tr width="100%">
                                            <td class="text-center" style="border: 1px solid;">3</td>
                                            <td style="border: 1px solid;">Pembimbing Agama / Penguji 2</td>
                                            <td class="text-center" style="border: 1px solid;">
                                                <?= $aba['nilaiAkhir']['total_pembimbing_agama'] ?>
                                            </td>
                                        </tr>

                                        <tr width="100%">
                                            <td class="text-center" style="border: 1px solid;">4</td>
                                            <td style="border: 1px solid;">Klien</td>
                                            <td class="text-center" style="border: 1px solid;">
                                                <?= $aba['nilaiAkhir']['total_klien'] ?>
                                            </td>
                                        </tr>

                                        <tr width="100%">
                                            <td class="text-center" style="border: 1px solid;"></td>
                                            <td style="border: 1px solid;"><strong>Total</strong></td>
                                            <td class="text-center" style="border: 1px solid;"><strong>
                                                    <?= $aba['nilaiAkhir']['nilai_akhir'] ?>
                                                </strong></td>
                                        </tr>

                                        <tr width="100%">
                                            <td class="text-center" style="border: 1px solid;"></td>
                                            <td style="border: 1px solid;"><strong>Nilai Dengan Huruf</strong></td>
                                            <td class="text-center" style="border: 1px solid;"><strong>
                                                    <?= $aba['sidangSkripsi']['grade'] ?>
                                                </strong></td>
                                        </tr>

                                    </table>

                                </td>

                            </tr>


                            <br><br>

                            <tr>
                                <td>
                                    <table width="100%" class="table-center">
                                        <tr>
                                            <td width="60%"></td>
                                            <td>
                                                <p style="margin-bottom: 0;" class="text-center">Jakarta,
                                                    <?= $today->format("d") ?>
                                                    <?= $bulanDict[$today->format("m")] ?>
                                                    <?= $today->format("Y") ?>
                                                </p>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="60%"></td>
                                            <td width="40%" class="text-center">Ketua Komisi Penguji</td>
                                        </tr>

                                        <tr>
                                            <td width="60%"></td>
                                            <td width="40%"><br><br><br><br><br></td>
                                        </tr>

                                        <tr>
                                            <td width="60%"></td>
                                            <td width="40%" class="text-center"><strong>(<?= $aba['penguji']['nama'] ?>)
                                                </strong></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                        </table>
                    </div>
            <?php elseif ($aba['lastSkripsi']['jenis'] == JENIS_SKRIPSI_REGULER): ?>
                    <div>

                    <td height="165" colspan="3" class="text-center">

                    <p class="panel-heading">

                        <img src="<?= base_url("assets/img/fti.jpg") ?>" width="157" height="62" align="absmiddle">

                            </p>

                        </td>



                        <table width="100%" style="border: 0" class="table-center">

                            <tr>

                                <td class="text-center">

                                    <h4>BERITA ACARA UJIAN SKRIPSI</h4>
                                    <p style="margin: 0; text-align: center; font-weight: bold; font-size: 14px;">
                                        NO. : <?= !empty($aba['sidangSkripsi']['nomor_berita_acara']) ? $aba['sidangSkripsi']['nomor_berita_acara'] : '..................................................' ?>
                                    </p>
                                </td>

                            </tr>



                            <tr>

                                <td>

                                    <p>

                                        Yang bertanda tangan di bawah ini Komisi Penguji Skripsi Program Studi
                                        <?= $aba['prodi']['nama'] ?> Program Sarjana Fakultas Teknologi Informasi Universitas YARSI
                                        dengan ini menyatakan dengan sebenarnya bahwa pada:

                                    </p>

                                </td>

                            </tr>



                            <tr>

                                <table width="80%" style="border: 0" class="table-center">

                                    <tr width="100%">

                                        <td width="20%">Hari/Tanggal</td>

                                        <td width="4%">:</td>

                                        <?php $tanggal = new DateTime($aba['sidangSkripsi']['tanggal'], new DateTimeZone('Asia/Jakarta')) ?>

                                        <td width="76%">
                                            <?= $hariDict[$tanggal->format("w")] ?>,
                                            <?= $tanggal->format('d') ?>
                                            <?= $bulanDict[$tanggal->format("m")] ?>
                                            <?= $tanggal->format("Y") ?>
                                        </td>

                                    </tr>

                                    <tr width="100%">

                                        <td width="20%">Jam</td>

                                        <td width="4%">:</td>

                                        <td width="76%">
                                            <?= $tanggal->format('H:i') ?> WIB
                                        </td>

                                    </tr>

                                    <tr width="100%">

                                        <td width="20%">Bertempat di</td>

                                        <td width="4%">:</td>

                                        <td width="76%">
                                            <?= preg_match('#^https?://#i', $aba['sidangSkripsi']['ruangan']) === 1 ? 'Link Zoom' : $aba['sidangSkripsi']['ruangan'] ?>
                                        </td>

                                    </tr>

                                </table>

                            </tr>



                            <tr>

                                <td>

                                    <p>

                                        Telah menyelengarakan Ujian Skripsi terhadap mahasiswa:

                                    </p>

                                </td>

                            </tr>



                            <tr>

                                <table width="80%" style="border: 0" class="table-center">

                                    <tr width="100%">

                                        <td width="20%">Nama</td>

                                        <td width="4%">:</td>

                                        <td width="76%">
                                            <?= $aba['mahasiswa']['nama'] ?>
                                        </td>

                                    </tr>

                                    <tr width="100%">

                                        <td width="20%">NPM</td>

                                        <td width="4%">:</td>

                                        <td width="76%">
                                            <?= $aba['mahasiswa']['npm'] ?>
                                        </td>

                                    </tr>

                                    <tr width="100%">

                                        <td width="20%">Program Studi</td>

                                        <td width="4%">:</td>

                                        <td width="76%">
                                            <?= $aba['prodi']['nama'] ?> Program Sarjana
                                        </td>

                                    </tr>

                                </table>

                            </tr>



                            <tr>

                                <td>

                                    <p>Dengan judul : <strong>“<?= $aba['lastSkripsi']['judul'] ?>”</strong></p>

                                </td>

                            </tr>



                            <tr>

                                <td style="height: 150px" ;>

                                    <p style="display: inline-block;">

                                        Hasil ujian dengan kualifikasi:

                                    </p>

                                </td>



                                <td>

                                    <div class="container">

                                        <div class="box text-center"><strong>
                                                <?= $aba['sidangSkripsi']['grade'] == null ? "-" : $aba['sidangSkripsi']['grade'] ?>
                                            </strong></div>

                                        <div class="box text-center"><strong>
                                                <?= number_format($aba['sidangSkripsi']['total_nilai'], 2, ".", "") ?>
                                            </strong></div>

                                    </div>

                                </td>

                            </tr>



                            <br><br>



                            <tr>

                                <td width="100%">

                                    <p style="margin-bottom: 0;" class="text-center">Jakarta,
                                        <?= $tanggal->format('d') ?>
                                        <?= $bulanDict[$tanggal->format("m")] ?>
                                        <?= $tanggal->format("Y") ?>
                                    </p>

                                </td>

                            </tr>



                            <tr>

                                <td>

                                    <table width="100%" class="table-center">

                                        <tr>

                                            <td class="text-center">Ketua Komisi Penguji</td>

                                        </tr>

                                        <tr>

                                            <td><br><br><br><br><br></td>

                                        </tr>

                                        <tr>

                                            <td class="text-center"><strong>(<?= $aba['penguji']['nama'] ?>)
                                                </strong></td>

                                        </tr>

                                    </table>

                                </td>

                            </tr>



                            <br><br>



                            <tr>

                                <td>

                                    <?php $width = ($aba['pembimbingIlmu2'] == null) ? "33%" : "25%" ?>

                                    <table width="100%" class="table-center">

                                        <tr>

                                            <td class="text-center" width="<?= $width ?>">Penguji Murni</td>

                                            <?php if ($aba['pembimbingIlmu2'] == null): ?>

                                                        <td class="text-center" width="<?= $width ?>">Pembimbing Ilmu / Penguji</td>

                                            <?php else: ?>

                                                        <td class="text-center" width="<?= $width ?>">Pembimbing Ilmu 1 / Penguji</td>

                                                        <td class="text-center" width="<?= $width ?>">Pembimbing Ilmu 2 / Penguji</td>

                                            <?php endif; ?>

                                            <td class="text-center" width="<?= $width ?>">Pembimbing Agama / Penguji</td>

                                        </tr>

                                        <tr>

                                            <td><br><br><br><br><br></td>

                                            <?php if ($aba['pembimbingIlmu2'] == null): ?>

                                                        <td width="<?= $width ?>"><br><br><br><br><br></td>

                                            <?php else: ?>

                                                        <td width="<?= $width ?>"><br><br><br><br><br></td>

                                                        <td width="<?= $width ?>"><br><br><br><br><br></td>

                                            <?php endif; ?>

                                            <td width="<?= $width ?>"><br><br><br><br><br></td>

                                        </tr>

                                        <tr>

                                            <td class="text-center" width="<?= $width ?>"><strong>(<?= $aba['penguji']['nama'] ?>)
                                                </strong></td>

                                            <?php if ($aba['pembimbingIlmu2'] == null): ?>

                                                <td class="text-center" width="<?= $width ?>"><strong>(<?= $aba['pembimbingIlmu1']['nama_dosen'] ?>)
                                                    </strong></td>

                                            <?php else: ?>

                                                <td class="text-center" width="<?= $width ?>"><strong>(<?= $aba['pembimbingIlmu1']['nama_dosen'] ?>)
                                                    </strong></td>

                                                <td class="text-center" width="<?= $width ?>"><strong>(<?= $aba['pembimbingIlmu2']['nama_dosen'] ?>)
                                                    </strong></td>

                                            <?php endif; ?>

                                            <td class="text-center" width="<?= $width ?>"><strong>(<?= $aba['pembimbingAgama']['nama_dosen'] ?>)
                                                </strong></td>

                                        </tr>

                                    </table>

                                </td>

                            </tr>

                        </table>

                    </div>



                        <div class="new-page"></div>



                        <div>

                            <td height="165" colspan="3" class="text-center">

                                <p class="panel-heading">

                                    <img src="<?= base_url("assets/img/fti.jpg") ?>" width="157" height="62" align="absmiddle">

                                </p>

                            </td>



                            <table width="100%" style="border: 0" class="table-center">

                                <tr>

                                    <td class="text-center">

                                        <h4>PENILAIAN UJIAN SKRIPSI</h4>

                                    </td>

                                </tr>



                                <tr>

                                    <table width="100%" style="border: 0">

                                        <tr width="100%">

                                            <td width="20%"><strong>Nama</strong></td>

                                            <td width="5%">:</td>

                                            <td width="75%">
                                                <?= $aba['mahasiswa']['nama'] ?>
                                            </td>

                                        </tr>

                                        <tr width="100%">

                                            <td width="20%"><strong>NPM</strong></td>

                                            <td width="5%">:</td>

                                            <td width="75%">
                                                <?= $aba['mahasiswa']['npm'] ?>
                                            </td>

                                        </tr>

                                        <tr width="100%">

                                            <td width="20%"><strong>Program Studi</strong></td>

                                            <td width="5%">:</td>

                                            <td width="75%">
                                                <?= $aba['prodi']['nama'] ?> Program Sarjana
                                            </td>

                                        </tr>

                                    </table>

                                </tr>


                                <?php for ($i = 0; $i < 4; $i += 1): ?>
                                            <tr>

                                                <td>

                                                    <p style="margin-bottom: 0"><STRONG>
                                                            <?= $i + 1 ?>.
                                                            <?= $aba['rubrikNilai'][$i * 3 + 0]['header'] ?>
                                                        </STRONG></p>

                                                    <table width="100%" style="border: 1px solid; border-collapse: collapse" class="table-center">

                                                        <tr>

                                                            <th style="border: 1px solid;width: 5%">NO</th>

                                                            <th style="border: 1px solid;width: 70%">PERINCIAN ASPEK</th>

                                                            <th style="border: 1px solid;width: 25%">NILAI (SKALA 0...100)</th>

                                                        </tr>

                                                        <tr width="100%">

                                                            <td class="text-center" style="border: 1px solid;">1</td>

                                                            <td style="border: 1px solid;">
                                                                <?= $aba['rubrikNilai'][$i * 3 + 0]['deskripsi'] ?>
                                                            </td>

                                                            <td class="text-center" style="border: 1px solid;">
                                                                <?= isset($aba['nilaiPenguji'][$i * 3 + 0]['nilai']) ? number_format($aba['nilaiPenguji'][$i * 3 + 0]['nilai'], 2, ".", "") : "0.00" ?>
                                                            </td>

                                                        </tr>

                                                        <tr width="100%">

                                                            <td class="text-center" style="border: 1px solid;">2</td>

                                                            <td style="border: 1px solid;">
                                                                <?= $aba['rubrikNilai'][$i * 3 + 1]['deskripsi'] ?>
                                                            </td>

                                                            <td class="text-center" style="border: 1px solid;">
                                                                <?= isset($aba['nilaiPenguji'][$i * 3 + 1]['nilai']) ? number_format($aba['nilaiPenguji'][$i * 3 + 1]['nilai'], 2, ".", "") : "0.00" ?>
                                                            </td>

                                                        </tr>

                                                        <tr width="100%">

                                                            <td class="text-center" style="border: 1px solid;">3</td>

                                                            <td style="border: 1px solid;">
                                                                <?= $aba['rubrikNilai'][$i * 3 + 2]['deskripsi'] ?>
                                                            </td>

                                                            <td class="text-center" style="border: 1px solid;">
                                                                <?= isset($aba['nilaiPenguji'][$i * 3 + 2]['nilai']) ? number_format($aba['nilaiPenguji'][$i * 3 + 2]['nilai'], 2, ".", "") : "0.00" ?>
                                                            </td>

                                                        </tr>

                                                        <tr>

                                                            <td colspan="3" class="text-center">

                                                                <strong>NILAI: ( </strong>
                                                                <?= isset($aba['nilaiPenguji'][$i * 3 + 0]['nilai']) ? number_format($aba['nilaiPenguji'][$i * 3 + 0]['nilai'], 2, ".", "") : "0.00" ?> +
                                                                <?= isset($aba['nilaiPenguji'][$i * 3 + 1]['nilai']) ? number_format($aba['nilaiPenguji'][$i * 3 + 1]['nilai'], 2, ".", "") : "0.00" ?> +
                                                                <?= isset($aba['nilaiPenguji'][$i * 3 + 2]['nilai']) ? number_format($aba['nilaiPenguji'][$i * 3 + 2]['nilai'], 2, ".", "") : "0.00" ?><strong> ) X
                                                                    <?= $aba['rubrikNilai'][$i * 3 + 0]['bobot'] ?>% =
                                                                    <?= number_format($aba['avgNilai']['section' . ($i + 1) . '_avg_penguji'], 2, ".", "") ?>
                                                                </strong>

                                                            </td>

                                                        </tr>

                                                    </table>

                                                </td>

                                            </tr>
                                <?php endfor; ?>


                                <tr style="margin: auto">

                                    <div style="width: 100%; margin: 15px auto 5px;" class="text-center">

                                        <td>

                                            <strong>Nilai Akhir</strong> =

                                        </td>


                                        <td>( <strong>
                                                <?= number_format($aba['avgNilai']['section1_avg_penguji'], 2, ".", "") ?>
                                            </strong> + <strong>
                                                <?= number_format($aba['avgNilai']['section2_avg_penguji'], 2, ".", "") ?>
                                            </strong> + <strong>
                                                <?= number_format($aba['avgNilai']['section3_avg_penguji'], 2, ".", "") ?>
                                            </strong> + <strong>
                                                <?= number_format($aba['avgNilai']['section4_avg_penguji'], 2, ".", "") ?>
                                            </strong> )
                                        </td>

                                        <td>

                                            = <strong>
                                                <?= number_format($aba['avgNilai']['total_penguji'], 2, ".", "") ?>
                                            </strong>

                                        </td>

                                    </div>

                                </tr>



                                <tr>

                                    <td>

                                        <div style="display: inline-block; float: left">

                                            <table>

                                                <tr>
                                                    <td>Mahasiswa ini dinyatakan: </td>
                                                </tr>

                                                <tr>
                                                    <td style="padding-left: 50px;"><input type="checkbox"
                                                            <?= $aba['avgNilai']['total_penguji'] >= 65 ? "checked" : "" ?>><strong>Lulus</strong></td>
                                                </tr>

                                                <tr>
                                                    <td style="padding-left: 50px;"><input type="checkbox"
                                                            <?= $aba['avgNilai']['total_penguji'] < 65 ? "checked" : "" ?>><strong>Tidak
                                                            Lulus</strong></td>
                                                </tr>

                                            </table>

                                        </div>

                                    </td>



                                    <td>

                                        <div style="display: inline-block; float: right;">

                                            <table>

                                                <tr>

                                                    <td class="text-center">Jakarta,
                                                        <?= $tanggal->format('d') ?>
                                                        <?= $bulanDict[$tanggal->format("m")] ?>
                                                        <?= $tanggal->format("Y") ?>
                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td class="text-center">Ketua Komisi Penguji / Penguji Murni</td>

                                                </tr>

                                                <tr>

                                                    <td><br><br><br><br><br></td>

                                                </tr>

                                                <tr>

                                                    <td class="text-center"><strong>(<?= $aba['penguji']['nama'] ?>)
                                                        </strong></td>

                                                </tr>

                                            </table>

                                        </div>

                                    </td>

                                </tr>

                            </table>

                        </div>



                        <div class="new-page"></div>



                        <div>

                            <td height="165" colspan="3" class="text-center">

                                <p class="panel-heading">

                                    <img src="<?= base_url("assets/img/fti.jpg") ?>" width="157" height="62" align="absmiddle">

                                </p>

                            </td>



                            <table width="100%" style="border: 0" class="table-center">

                                <tr>

                                    <td class="text-center">

                                        <h4>PENILAIAN UJIAN SKRIPSI</h4>

                                    </td>

                                </tr>



                                <tr>

                                    <table width="100%" style="border: 0">

                                        <tr width="100%">

                                            <td width="20%"><strong>Nama</strong></td>

                                            <td width="5%">:</td>

                                            <td width="75%">
                                                <?= $aba['mahasiswa']['nama'] ?>
                                            </td>

                                        </tr>

                                        <tr width="100%">

                                            <td width="20%"><strong>NPM</strong></td>

                                            <td width="5%">:</td>

                                            <td width="75%">
                                                <?= $aba['mahasiswa']['npm'] ?>
                                            </td>

                                        </tr>

                                        <tr width="100%">

                                            <td width="20%"><strong>Program Studi</strong></td>

                                            <td width="5%">:</td>

                                            <td width="75%">
                                                <?= $aba['prodi']['nama'] ?> Program Sarjana
                                            </td>

                                        </tr>

                                    </table>

                                </tr>



                                <?php for ($i = 0; $i < 4; $i += 1): ?>
                                            <tr>

                                                <td>

                                                    <p style="margin-bottom: 0"><STRONG>
                                                            <?= $i + 1 ?>.
                                                            <?= $aba['rubrikNilai'][$i * 3 + 0]['header'] ?>
                                                        </STRONG></p>

                                                    <table width="100%" style="border: 1px solid; border-collapse: collapse" class="table-center">

                                                        <tr>

                                                            <th style="border: 1px solid;width: 5%">NO</th>

                                                            <th style="border: 1px solid;width: 70%">PERINCIAN ASPEK</th>

                                                            <th style="border: 1px solid;width: 25%">NILAI (SKALA 0...100)</th>

                                                        </tr>

                                                        <tr width="100%">

                                                            <td class="text-center" style="border: 1px solid;">1</td>

                                                            <td style="border: 1px solid;">
                                                                <?= $aba['rubrikNilai'][$i * 3 + 0]['deskripsi'] ?>
                                                            </td>

                                                            <td class="text-center" style="border: 1px solid;">
                                                                <?= isset($aba['nilaiPembimbing1'][$i * 3 + 0]['nilai']) ? number_format($aba['nilaiPembimbing1'][$i * 3 + 0]['nilai'], 2, ".", "") : "0.00" ?>
                                                            </td>

                                                        </tr>

                                                        <tr width="100%">

                                                            <td class="text-center" style="border: 1px solid;">2</td>

                                                            <td style="border: 1px solid;">
                                                                <?= $aba['rubrikNilai'][$i * 3 + 1]['deskripsi'] ?>
                                                            </td>

                                                            <td class="text-center" style="border: 1px solid;">
                                                                <?= isset($aba['nilaiPembimbing1'][$i * 3 + 1]['nilai']) ? number_format($aba['nilaiPembimbing1'][$i * 3 + 1]['nilai'], 2, ".", "") : "0.00" ?>
                                                            </td>

                                                        </tr>

                                                        <tr width="100%">

                                                            <td class="text-center" style="border: 1px solid;">3</td>

                                                            <td style="border: 1px solid;">
                                                                <?= $aba['rubrikNilai'][$i * 3 + 2]['deskripsi'] ?>
                                                            </td>

                                                            <td class="text-center" style="border: 1px solid;">
                                                                <?= isset($aba['nilaiPembimbing1'][$i * 3 + 2]['nilai']) ? number_format($aba['nilaiPembimbing1'][$i * 3 + 2]['nilai'], 2, ".", "") : "0.00" ?>
                                                            </td>

                                                        </tr>

                                                        <tr>

                                                            <td colspan="3" class="text-center">

                                                                <strong>NILAI: ( </strong>
                                                                <?= isset($aba['nilaiPembimbing1'][$i * 3 + 0]['nilai']) ? number_format($aba['nilaiPembimbing1'][$i * 3 + 0]['nilai'], 2, ".", "") : "0.00" ?> +
                                                                <?= isset($aba['nilaiPembimbing1'][$i * 3 + 1]['nilai']) ? number_format($aba['nilaiPembimbing1'][$i * 3 + 1]['nilai'], 2, ".", "") : "0.00" ?> +
                                                                <?= isset($aba['nilaiPembimbing1'][$i * 3 + 2]['nilai']) ? number_format($aba['nilaiPembimbing1'][$i * 3 + 2]['nilai'], 2, ".", "") : "0.00" ?><strong>
                                                                    ) X
                                                                    <?= $aba['rubrikNilai'][$i * 3 + 0]['bobot'] ?>% =
                                                                    <?= number_format($aba['avgNilai']['section' . ($i + 1) . '_avg_pembimbing1'], 2, ".", "") ?>
                                                                </strong>

                                                            </td>

                                                        </tr>

                                                    </table>

                                                </td>

                                            </tr>
                                <?php endfor; ?>



                                <tr style="margin: auto">

                                    <div style="width: 100%; margin: 15px auto 5px;" class="text-center">

                                        <td>

                                            <strong>Nilai Akhir</strong> =

                                        </td>

                                        <td>( <strong>
                                                <?= number_format($aba['avgNilai']['section1_avg_pembimbing1'], 2, ".", "") ?>
                                            </strong> + <strong>
                                                <?= number_format($aba['avgNilai']['section2_avg_pembimbing1'], 2, ".", "") ?>
                                            </strong> + <strong>
                                                <?= number_format($aba['avgNilai']['section3_avg_pembimbing1'], 2, ".", "") ?>
                                            </strong> + <strong>
                                                <?= number_format($aba['avgNilai']['section4_avg_pembimbing1'], 2, ".", "") ?>
                                            </strong> )
                                        </td>

                                        <td>

                                            = <strong>
                                                <?= number_format($aba['avgNilai']['total_pembimbing1'], 2, ".", "") ?>
                                            </strong>

                                        </td>

                                    </div>

                                </tr>



                                <tr>

                                    <td>

                                        <div style="display: inline-block; float: left">

                                            <table>

                                                <tr>
                                                    <td>Mahasiswa ini dinyatakan: </td>
                                                </tr>

                                                <tr>
                                                    <td style="padding-left: 50px;"><input type="checkbox"
                                                            <?= $aba['avgNilai']['total_pembimbing1'] >= 65 ? "checked" : "" ?>><strong>Lulus</strong></td>
                                                </tr>

                                                <tr>
                                                    <td style="padding-left: 50px;"><input type="checkbox"
                                                            <?= $aba['avgNilai']['total_pembimbing1'] < 65 ? "checked" : "" ?>><strong>Tidak
                                                            Lulus</strong></td>
                                                </tr>

                                            </table>

                                        </div>

                                    </td>



                                    <td>

                                        <div style="display: inline-block; float: right;">

                                            <table>

                                                <tr>

                                                    <td class="text-center">Jakarta,
                                                        <?= $tanggal->format('d') ?>
                                                        <?= $bulanDict[$tanggal->format("m")] ?>
                                                        <?= $tanggal->format("Y") ?>
                                                    </td>

                                                </tr>

                                                <tr>

                                                    <?php if ($aba['pembimbingIlmu2'] == null): ?>

                                                                <td class="text-center">Pembimbing Ilmu / Penguji</td>

                                                    <?php else: ?>

                                                                <td class="text-center">Pembimbing Ilmu 1 / Penguji</td>

                                                    <?php endif; ?>

                                                </tr>

                                                <tr>

                                                    <td><br><br><br><br><br></td>

                                                </tr>

                                                <tr>

                                                    <td class="text-center"><strong>(<?= $aba['pembimbingIlmu1']['nama_dosen'] ?>)
                                                        </strong></td>

                                                </tr>

                                            </table>

                                        </div>

                                    </td>

                                </tr>

                            </table>

                        </div>



                        <div class="new-page"></div>



                        <?php if ($aba['pembimbingIlmu2'] != null): ?>

                                    <div>

                                        <td height="165" colspan="3" class="text-center">

                                            <p class="panel-heading">

                                                <img src="<?= base_url("assets/img/fti.jpg") ?>" width="157" height="62" align="absmiddle">

                                            </p>

                                        </td>



                                        <table width="100%" style="border: 0" class="table-center">

                                            <tr>

                                                <td class="text-center">

                                                    <h4>PENILAIAN UJIAN SKRIPSI</h4>

                                                </td>

                                            </tr>



                                            <tr>

                                                <table width="100%" style="border: 0">

                                                    <tr width="100%">

                                                        <td width="20%"><strong>Nama</strong></td>

                                                        <td width="5%">:</td>

                                                        <td width="75%">
                                                            <?= $aba['mahasiswa']['nama'] ?>
                                                        </td>

                                                    </tr>

                                                    <tr width="100%">

                                                        <td width="20%"><strong>NPM</strong></td>

                                                        <td width="5%">:</td>

                                                        <td width="75%">
                                                            <?= $aba['mahasiswa']['npm'] ?>
                                                        </td>

                                                    </tr>

                                                    <tr width="100%">

                                                        <td width="20%"><strong>Program Studi</strong></td>

                                                        <td width="5%">:</td>

                                                        <td width="75%">
                                                            <?= $aba['prodi']['nama'] ?> Program Sarjana
                                                        </td>

                                                    </tr>

                                                </table>

                                            </tr>



                                            <?php for ($i = 0; $i < 4; $i += 1): ?>
                                                        <tr>
                                                            <td>
                                                                <p style="margin-bottom: 0"><STRONG>
                                                                        <?= $i + 1 ?>.
                                                                        <?= $aba['rubrikNilai'][$i * 3 + 0]['header'] ?>
                                                                    </STRONG></p>

                                                                <table width="100%" style="border: 1px solid; border-collapse: collapse" class="table-center">

                                                                    <tr>

                                                                        <th style="border: 1px solid;width: 5%">NO</th>

                                                                        <th style="border: 1px solid;width: 70%">PERINCIAN ASPEK</th>

                                                                        <th style="border: 1px solid;width: 25%">NILAI (SKALA 0...100)</th>

                                                                    </tr>

                                                                    <tr width="100%">

                                                                        <td class="text-center" style="border: 1px solid;">1</td>

                                                                        <td style="border: 1px solid;">
                                                                            <?= $aba['rubrikNilai'][$i * 3 + 0]['deskripsi'] ?>
                                                                        </td>

                                                                        <td class="text-center" style="border: 1px solid;">
                                                                            <?php if (!empty($aba['nilaiPembimbing2'])): ?>
                                                                                <?= isset($aba['nilaiPembimbing2'][$i * 3 + 0]['nilai']) ? number_format($aba['nilaiPembimbing2'][$i * 3 + 0]['nilai'], 2, ".", "") : "0.00" ?>
                                                                            <?php else: ?>
                                                                                -
                                                                            <?php endif; ?>
                                                                        </td>

                                                                    </tr>

                                                                    <tr width="100%">

                                                                        <td class="text-center" style="border: 1px solid;">2</td>

                                                                        <td style="border: 1px solid;">
                                                                            <?= $aba['rubrikNilai'][$i * 3 + 1]['deskripsi'] ?>
                                                                        </td>

                                                                        <td class="text-center" style="border: 1px solid;">
                                                                            <?php if (!empty($aba['nilaiPembimbing2'])): ?>
                                                                                <?= isset($aba['nilaiPembimbing2'][$i * 3 + 1]['nilai']) ? number_format($aba['nilaiPembimbing2'][$i * 3 + 1]['nilai'], 2, ".", "") : "0.00" ?>
                                                                            <?php else: ?>
                                                                                -
                                                                            <?php endif; ?>
                                                                        </td>

                                                                    </tr>

                                                                    <tr width="100%">

                                                                        <td class="text-center" style="border: 1px solid;">3</td>

                                                                        <td style="border: 1px solid;">
                                                                            <?= $aba['rubrikNilai'][$i * 3 + 2]['deskripsi'] ?>
                                                                        </td>

                                                                        <td class="text-center" style="border: 1px solid;">
                                                                            <?php if (!empty($aba['nilaiPembimbing2'])): ?>
                                                                                <?= isset($aba['nilaiPembimbing2'][$i * 3 + 2]['nilai']) ? number_format($aba['nilaiPembimbing2'][$i * 3 + 2]['nilai'], 2, ".", "") : "0.00" ?>
                                                                            <?php else: ?>
                                                                                -
                                                                            <?php endif; ?>
                                                                        </td>

                                                                    </tr>

                                                                    <tr>

                                                                        <td colspan="3" class="text-center">

                                                                            <strong>NILAI: ( </strong>
                                                                                <?php if (!empty($aba['nilaiPembimbing2'])): ?>
                                                                                    <?= isset($aba['nilaiPembimbing2'][$i * 3 + 0]['nilai']) ? number_format($aba['nilaiPembimbing2'][$i * 3 + 0]['nilai'], 2, ".", "") : "0.00" ?> +
                                                                                    <?= isset($aba['nilaiPembimbing2'][$i * 3 + 1]['nilai']) ? number_format($aba['nilaiPembimbing2'][$i * 3 + 1]['nilai'], 2, ".", "") : "0.00" ?> +
                                                                                    <?= isset($aba['nilaiPembimbing2'][$i * 3 + 2]['nilai']) ? number_format($aba['nilaiPembimbing2'][$i * 3 + 2]['nilai'], 2, ".", "") : "0.00" ?><strong>
                                                                                <?php else: ?>
                                                                                    -
                                                                                <?php endif; ?>
                                                                                ) X
                                                                                <?= $aba['rubrikNilai'][$i * 3 + 0]['bobot'] ?>% =
                                                                                <?= number_format($aba['avgNilai']['section' . ($i + 1) . '_avg_pembimbing2'], 2, ".", "") ?>
                                                                            </strong>

                                                                        </td>

                                                                    </tr>

                                                                </table>

                                                            </td>

                                                        </tr>
                                            <?php endfor; ?>



                                            <tr style="margin: auto">

                                                <div style="width: 100%; margin: 15px auto 5px;" class="text-center">

                                                    <td>

                                                        <strong>Nilai Akhir</strong> =

                                                    </td>

                                                    <td>( <strong>
                                                            <?= number_format($aba['avgNilai']['section1_avg_pembimbing2'], 2, ".", "") ?>
                                                        </strong> + <strong>
                                                            <?= number_format($aba['avgNilai']['section2_avg_pembimbing2'], 2, ".", "") ?>
                                                        </strong> + <strong>
                                                            <?= number_format($aba['avgNilai']['section3_avg_pembimbing2'], 2, ".", "") ?>
                                                        </strong> + <strong>
                                                            <?= number_format($aba['avgNilai']['section4_avg_pembimbing2'], 2, ".", "") ?>
                                                        </strong> )

                                                    </td>

                                                    <td>

                                                        = <strong>
                                                            <?= number_format($aba['avgNilai']['total_pembimbing2'], 2, ".", "") ?>
                                                        </strong>

                                                    </td>

                                                </div>

                                            </tr>



                                            <tr>

                                                <td>

                                                    <div style="display: inline-block; float: left">

                                                        <table>

                                                            <tr>
                                                                <td>Mahasiswa ini dinyatakan: </td>
                                                            </tr>

                                                            <tr>
                                                                <td style="padding-left: 50px;"><input type="checkbox"
                                                                        <?= $aba['avgNilai']['total_pembimbing2'] >= 65 ? "checked" : "" ?>><strong>Lulus</strong></td>
                                                            </tr>

                                                            <tr>
                                                                <td style="padding-left: 50px;"><input type="checkbox"
                                                                        <?= $aba['avgNilai']['total_pembimbing2'] < 65 ? "checked" : "" ?>><strong>Tidak
                                                                        Lulus</strong></td>
                                                            </tr>

                                                        </table>


                                                    </div>

                                                </td>



                                                <td>

                                                    <div style="display: inline-block; float: right;">

                                                        <table>

                                                            <tr>

                                                                <td class="text-center">Jakarta,
                                                                    <?= $tanggal->format('d') ?>
                                                                    <?= $bulanDict[$tanggal->format("m")] ?>
                                                                    <?= $tanggal->format("Y") ?>
                                                                </td>

                                                            </tr>

                                                            <tr>

                                                                <td class="text-center">Pembimbing Ilmu 2 / Penguji</td>

                                                            </tr>

                                                            <tr>

                                                                <td><br><br><br><br><br></td>

                                                            </tr>

                                                            <tr>

                                                                <td class="text-center"><strong>(<?= $aba['pembimbingIlmu2']['nama_dosen'] ?>)
                                                                    </strong></td>

                                                            </tr>

                                                        </table>

                                                    </div>

                                                </td>

                                            </tr>

                                        </table>

                                    </div>



                                    <div class="new-page"></div>



                        <?php endif; ?>





                        <div>

                            <td height="165" colspan="3" class="text-center">

                                <p class="panel-heading">

                                    <img src="<?= base_url("assets/img/fti.jpg") ?>" width="157" height="62" align="absmiddle">

                                </p>

                            </td>



                            <table width="100%" style="border: 0" class="table-center">

                                <tr>

                                    <td class="text-center">

                                        <h4>PENILAIAN UJIAN SKRIPSI</h4>

                                    </td>

                                </tr>



                                <tr>

                                    <table width="100%" style="border: 0">

                                        <tr width="100%">

                                            <td width="20%"><strong>Nama</strong></td>

                                            <td width="5%">:</td>

                                            <td width="75%">
                                                <?= $aba['mahasiswa']['nama'] ?>
                                            </td>

                                        </tr>

                                        <tr width="100%">

                                            <td width="20%"><strong>NPM</strong></td>

                                            <td width="5%">:</td>

                                            <td width="75%">
                                                <?= $aba['mahasiswa']['npm'] ?>
                                            </td>

                                        </tr>

                                        <tr width="100%">

                                            <td width="20%"><strong>Program Studi</strong></td>

                                            <td width="5%">:</td>

                                            <td width="75%">
                                                <?= $aba['prodi']['nama'] ?> Program Sarjana
                                            </td>

                                        </tr>

                                    </table>

                                </tr>



                                <?php for ($i = 0; $i < 4; $i += 1): ?>
                                            <tr>
                                                <td>
                                                    <p style="margin-bottom: 0"><STRONG>
                                                            <?= $i + 1 ?>.
                                                            <?= $aba['rubrikNilai'][$i * 3 + 0]['header'] ?>
                                                        </STRONG></p>

                                                    <table width="100%" style="border: 1px solid; border-collapse: collapse" class="table-center">

                                                        <tr>

                                                            <th style="border: 1px solid;width: 5%">NO</th>

                                                            <th style="border: 1px solid;width: 70%">PERINCIAN ASPEK</th>

                                                            <th style="border: 1px solid;width: 25%">NILAI (SKALA 0...100)</th>

                                                        </tr>

                                                        <tr width="100%">

                                                            <td class="text-center" style="border: 1px solid;">1</td>

                                                            <td style="border: 1px solid;">
                                                                <?= $aba['rubrikNilai'][$i * 3 + 0]['deskripsi'] ?>
                                                            </td>



                                                            <td class="text-center" style="border: 1px solid;">
                                                                <?= isset($aba['nilaiPembimbingAgama'][$i * 3 + 0]['nilai']) ? number_format($aba['nilaiPembimbingAgama'][$i * 3 + 0]['nilai'], 2, ".", "") : "0.00" ?>
                                                            </td>

                                                        </tr>

                                                        <tr width="100%">

                                                            <td class="text-center" style="border: 1px solid;">2</td>

                                                            <td style="border: 1px solid;">
                                                                <?= $aba['rubrikNilai'][$i * 3 + 1]['deskripsi'] ?>
                                                            </td>



                                                            <td class="text-center" style="border: 1px solid;">
                                                                <?= isset($aba['nilaiPembimbingAgama'][$i * 3 + 1]['nilai']) ? number_format($aba['nilaiPembimbingAgama'][$i * 3 + 1]['nilai'], 2, ".", "") : "0.00" ?>
                                                            </td>

                                                        </tr>

                                                        <tr width="100%">

                                                            <td class="text-center" style="border: 1px solid;">3</td>

                                                            <td style="border: 1px solid;">
                                                                <?= $aba['rubrikNilai'][$i * 3 + 2]['deskripsi'] ?>
                                                            </td>

                                                            <td class="text-center" style="border: 1px solid;">
                                                                <?= isset($aba['nilaiPembimbingAgama'][$i * 3 + 2]['nilai']) ? number_format($aba['nilaiPembimbingAgama'][$i * 3 + 2]['nilai'], 2, ".", "") : "0.00" ?>
                                                            </td>

                                                        </tr>

                                                        <tr>

                                                            <td colspan="3" class="text-center">

                                                                <strong>NILAI: ( </strong>
                                                                <?= isset($aba['nilaiPembimbingAgama'][$i * 3 + 0]['nilai']) ? number_format($aba['nilaiPembimbingAgama'][$i * 3 + 0]['nilai'], 2, ".", "") : "0.00" ?> +
                                                                <?= isset($aba['nilaiPembimbingAgama'][$i * 3 + 1]['nilai']) ? number_format($aba['nilaiPembimbingAgama'][$i * 3 + 1]['nilai'], 2, ".", "") : "0.00" ?> +
                                                                <?= isset($aba['nilaiPembimbingAgama'][$i * 3 + 2]['nilai']) ? number_format($aba['nilaiPembimbingAgama'][$i * 3 + 2]['nilai'], 2, ".", "") : "0.00" ?><strong>
                                                                    ) X
                                                                    <?= $aba['rubrikNilai'][$i * 3 + 0]['bobot'] ?>% =
                                                                    <?= number_format($aba['avgNilai']['section' . ($i + 1) . '_avg_pembimbing_agama'], 2, ".", "") ?>
                                                                </strong>

                                                            </td>

                                                        </tr>

                                                    </table>

                                                </td>

                                            </tr>
                                <?php endfor; ?>



                                <tr style="margin: auto">

                                    <div style="width: 100%; margin: 15px auto 5px;" class="text-center">

                                        <td>

                                            <strong>Nilai Akhir</strong> =

                                        </td>

                                        <td>
                                            ( <strong>
                                                <?= number_format($aba['avgNilai']['section1_avg_pembimbing_agama'], 2, ".", "") ?>
                                            </strong> + <strong>
                                                <?= number_format($aba['avgNilai']['section2_avg_pembimbing_agama'], 2, ".", "") ?>
                                            </strong> + <strong>
                                                <?= number_format($aba['avgNilai']['section3_avg_pembimbing_agama'], 2, ".", "") ?>
                                            </strong> + <strong>
                                                <?= number_format($aba['avgNilai']['section4_avg_pembimbing_agama'], 2, ".", "") ?>
                                            </strong> )
                                        </td>

                                        <td>

                                            = <strong>
                                                <?= number_format($aba['avgNilai']['total_pembimbing_agama'], 2, ".", "") ?>
                                            </strong>

                                        </td>

                                    </div>

                                </tr>



                                <tr>

                                    <td>

                                        <div style="display: inline-block; float: left">

                                            <table>

                                                <tr>
                                                    <td>Mahasiswa ini dinyatakan: </td>
                                                </tr>

                                                <tr>
                                                    <td style="padding-left: 50px;"><input type="checkbox"
                                                            <?= $aba['avgNilai']['total_pembimbing_agama'] >= 65 ? "checked" : "" ?>><strong>Lulus</strong></td>
                                                </tr>

                                                <tr>
                                                    <td style="padding-left: 50px;"><input type="checkbox"
                                                            <?= $aba['avgNilai']['total_pembimbing_agama'] < 65 ? "checked" : "" ?>><strong>Tidak
                                                            Lulus</strong></td>
                                                </tr>

                                            </table>

                                        </div>

                                    </td>



                                    <td>

                                        <div style="display: inline-block; float: right;">

                                            <table>

                                                <tr>

                                                    <td class="text-center">Jakarta,
                                                        <?= $tanggal->format('d') ?>
                                                        <?= $bulanDict[$tanggal->format("m")] ?>
                                                        <?= $tanggal->format("Y") ?>
                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td class="text-center">Pembimbing Agama / Penguji</td>

                                                </tr>

                                                <tr>

                                                    <td><br><br><br><br><br></td>

                                                </tr>

                                                <tr>

                                                    <td class="text-center"><strong>(<?= $aba['pembimbingAgama']['nama_dosen'] ?>)
                                                        </strong></td>

                                                </tr>

                                            </table>

                                        </div>

                                    </td>

                                </tr>

                            </table>

                        </div>
            <?php endif; ?>

            <?php $counter++; ?>

            <?php if ($counter <= count($arrBeritaAcara)): ?>

                    <div class="new-page"></div>

            <?php endif; ?>

    <?php endforeach; ?>

</body>

</html>