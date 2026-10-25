<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Tugas Pembimbing</title>
    <style>
        /* MARGIN HALAMAN */
        @page {
            margin: 2cm 2.5cm 2cm 2.5cm; 
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt; 
            line-height: 1.2; 
            color: #000;
        }
        
        /* HEADER (KOP SURAT) */
        .header {
            margin-bottom: 20px;
            /* Debug: border: 1px solid red; */
        }
        .header img {
            /* Sesuaikan ukuran logo fti.png */
            /* Biasanya lebar 250px - 300px sudah cukup besar untuk Kop Surat */
            height: 90px; 
            width: auto;
        }

        /* JUDUL SURAT */
        .judul-container {
            text-align: center;
            margin-bottom: 20px;
            margin-top: 10px;
        }
        .judul-surat {
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            margin: 0;
            font-size: 12pt;
        }
        .nomor-surat {
            font-weight: bold;
            margin: 2px 0 0 0;
            font-size: 11pt;
        }

        /* ISI SURAT */
        .content {
            text-align: justify;
            line-height: 1.3;
        }
        
        /* TABEL DATA */
        .tabel-info {
            width: 100%;
            margin-left: 20px; 
            margin-bottom: 10px;
            border-collapse: collapse;
        }
        .tabel-info td {
            vertical-align: top;
            padding: 2px 0;
            font-size: 11pt;
        }
        .col-label {
            width: 70px;
            font-weight: bold;
        }
        .col-sep {
            width: 15px;
            text-align: center;
            font-weight: bold;
        }
        .col-val {
            font-weight: bold;
        }

        /* JUDUL SKRIPSI */
        .judul-skripsi {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            margin: 10px 40px; 
            line-height: 1.3;
        }

        /* TANDA TANGAN */
        .ttd-wrapper {
            margin-top: 20px;
            width: 100%;
            position: relative;
            height: 120px; 
        }
        .ttd-box {
            float: right;
            width: 45%;
            text-align: left;
        }
        .ttd-jabatan { margin-bottom: 60px; }
        .ttd-nama { font-weight: bold; }

        /* TEMBUSAN */
        .tembusan {
            margin-top: 10px;
            font-size: 10pt;
        }
        .tembusan-title {
            font-weight: bold;
            margin-bottom: 2px;
        }
        .tembusan-list {
            margin: 0;
            padding-left: 15px;
        }

        /* FOOTER */
        .page-footer {
            position: fixed;
            bottom: -30px; 
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9pt;
            line-height: 1.2;
        }
        .smart-campus {
            color: #00913f; 
            font-weight: bold;
            font-style: italic;
            font-size: 10pt;
            margin-bottom: 2px;
        }
    </style>
</head>
<body>

    <div class="header">
        <img src="<?= $logo_src ?>" alt="Kop Surat">
    </div>

    <div class="judul-container">
        <div class="judul-surat">SURAT TUGAS PEMBIMBING TUGAS AKHIR/SKRIPSI</div>
        <div class="nomor-surat">No. <?= $no_surat ?></div>
    </div>

    <div class="content">
        <p>Assalamu'alaikum. Wr. Wb.</p>

        <p>Dalam rangka penulisan tugas akhir/skripsi, Kepala Program Studi Teknik Informatika Fakultas Teknologi Informasi, Universitas YARSI, dengan ini menugaskan:</p>

        <table class="tabel-info">
            <tr>
                <td class="col-label">Nama</td>
                <td class="col-sep">:</td>
                <td class="col-val"><?= $data['pembimbing_ilmu1'] ?></td>
            </tr>
            <tr>
                <td class="col-label">Sebagai</td>
                <td class="col-sep">:</td>
                <td class="col-val">Pembimbing Ilmu</td>
            </tr>
        </table>

        <?php if(!empty($data['pembimbing_ilmu2']) && $data['pembimbing_ilmu2'] != '-'): ?>
        <table class="tabel-info">
            <tr>
                <td class="col-label">Nama</td>
                <td class="col-sep">:</td>
                <td class="col-val"><?= $data['pembimbing_ilmu2'] ?></td>
            </tr>
            <tr>
                <td class="col-label">Sebagai</td>
                <td class="col-sep">:</td>
                <td class="col-val">Pembimbing Ilmu 2</td>
            </tr>
        </table>
        <?php endif; ?>

        <table class="tabel-info">
            <tr>
                <td class="col-label">Nama</td>
                <td class="col-sep">:</td>
                <td class="col-val"><?= $data['pembimbing_agama'] ?></td>
            </tr>
            <tr>
                <td class="col-label">Sebagai</td>
                <td class="col-sep">:</td>
                <td class="col-val">Pembimbing Agama</td>
            </tr>
        </table>

        <p>bagi mahasiswa berikut ini:</p>

        <table class="tabel-info">
            <tr>
                <td class="col-label">Nama</td>
                <td class="col-sep">:</td>
                <td class="col-val"><?= $data['nama_mhs'] ?></td>
            </tr>
            <tr>
                <td class="col-label">NPM</td>
                <td class="col-sep">:</td>
                <td class="col-val"><?= $data['npm'] ?></td>
            </tr>
        </table>

        <div style="margin-top: 10px;">Judul Tugas Akhir/Skripsi:</div>

        <div class="judul-skripsi">
            “<?= $data['judul'] ?>”.
        </div>

        <p>
            Tugas Akhir/Skripsi tersebut diharapkan telah selesai selambat-lambatnya 6 (enam) bulan setelah surat tugas ini dikeluarkan.
        </p>

        <p>
            Demikianlah surat tugas ini diberikan agar pihak-pihak yang terkait dapat membantu seperlunya.
        </p>

        <p>Wassalamu'alaikum. Wr. Wb.</p>
    </div>

    <div class="ttd-wrapper">
        <div class="ttd-box">
            <div class="ttd-date">Jakarta, <?= $tanggal_surat ?></div>
            <div class="ttd-jabatan"><?= $jabatan_kaprodi ?></div>            
            <div class="ttd-nama"><?= $kaprodi ?></div>
        </div>
    </div>

    <div class="tembusan">
        <div class="tembusan-title">Tembusan :</div>
        <ol class="tembusan-list">
            <li>Dekan FTI</li>
            <li>Mahasiswa/i ybs</li>
            <li>Arsip</li>
        </ol>
    </div>

    <div class="page-footer">
        <div class="smart-campus">Smart Campus That You Can Rely On</div>
        <div>Jl. Letjend. Suprapto, Cempaka Putih, Jakarta 10510</div>
        <div>Telp./Fax. 62.21-4269301 atau 62.21-4206674 (Hunting) Ext. 5005</div>
        <div>www.yarsi.ac.id</div>
    </div>

</body>
</html>