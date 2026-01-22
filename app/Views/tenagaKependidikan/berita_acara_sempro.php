<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Seminar Proposal - <?= $seminar['npm'] ?></title>
    <style>
        /* 1. HILANGKAN HEADER/FOOTER BROWSER & LOGO CI4 */
        @page { 
            size: A4; 
            margin: 0; /* Nol-kan margin browser agar tanggal/URL hilang */
        }
        @media print {
            /* Sembunyikan Debug Toolbar CodeIgniter */
            #debug-icon, #debug-bar, .debug-bar, [id^="debug-"] {
                display: none !important;
                visibility: hidden !important;
            }
            /* Sembunyikan tombol cetak saat diprint */
            .no-print { display: none !important; }
        }

        /* Reset & Page Setup */
        @page { size: A4; margin: 2cm 2cm 1cm 2cm; }
        body { 
            font-family: "Times New Roman", Times, serif; 
            font-size: 12pt; 
            line-height: 1.5; 
            color: #000;
            background: #fff;
        }

        /* Helper Classes */
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .no-print { display: block; margin-bottom: 20px; }

        /* KOP SURAT */
        header {
            text-align: center;
            margin-bottom: 20px;
        }
        .kop-univ { font-size: 14pt; font-weight: bold; }
        .kop-fak { font-size: 14pt; font-weight: bold; }
        
        /* ISI SURAT */
        .judul-surat {
            margin-top: 30px;
            margin-bottom: 30px;
            font-size: 14pt;
        }

        /* Tabel Data (Hari, Tanggal, Nama, dll) */
        .table-data {
            width: 100%;
            margin-left: 20px; /* Indentasi sedikit */
            margin-bottom: 10px;
        }
        .table-data td {
            vertical-align: top;
            padding: 2px 5px;
        }
        .label-col { width: 140px; }
        .separator-col { width: 10px; }

        /* Judul Skripsi Box */
        .judul-skripsi {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            margin: 10px 40px 30px 40px; /* Jarak kiri kanan agar menjorok */
            line-height: 1.3;
        }

        /* Status Keputusan */
        .status-decision {
            text-transform: uppercase;
        }

        /* Tanda Tangan */
        .ttd-wrapper {
            margin-top: 50px;
            width: 100%;
            display: table; /* Hack layouting for PDF/Print */
        }
        .ttd-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        .ttd-space {
            height: 80px; /* Ruang untuk tanda tangan */
        }
        .ttd-date {
            text-align: center;
            margin-bottom: 5px;
        }

        /* Footer */
        footer {
            margin-top: 100px; /* Dorong footer ke bawah */
            text-align: center;
            font-size: 9pt;
            border-top: 1px solid #000;
            padding-top: 5px;
            line-height: 1.2;
            color: #444;
            position: fixed; 
            bottom: 0; 
            left: 0; right: 0;
        }
        .footer-tagline {
            font-weight: bold;
            font-style: italic;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #0e8c47;
            font-size: 10pt;
            margin-bottom: 3px;
        }

        /* Print Mode */
        @media print {
            .no-print { display: none; }
            footer { position: fixed; bottom: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print text-center">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; background: #333; color: white; border: none; font-size: 14px;">
            🖨️ Cetak Dokumen / Simpan PDF
        </button>
    </div>

    <header>
        <div class="kop-univ">UNIVERSITAS YARSI</div>
        <div class="kop-fak">FAKULTAS TEKNOLOGI INFORMASI</div>
    </header>

    <div class="text-center text-bold judul-surat">
        BERITA ACARA SEMINAR PROPOSAL
    </div>

    <p>Pada hari ini:</p>
    
    <?php
        // Helper untuk memformat Hari & Tanggal Indonesia
        // $seminar['tanggal'] formatnya Y-m-d H:i:s dari database
        $time = strtotime($seminar['tanggal']);
        
        $hariArr = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
        $bulanArr = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        
        $hariEnglish = date('l', $time);
        $hariIndo = $hariArr[$hariEnglish];
        
        $tgl = date('d', $time);
        $bln = (int)date('m', $time);
        $thn = date('Y', $time);
        $tanggalFull = "$tgl-$bln-$thn"; // Format sesuai sampel: 27-09-2024
        
        $jam = date('H.i', $time); // Format: 10.00
    ?>

    <table class="table-data">
        <tr>
            <td class="label-col">Hari/Tanggal</td>
            <td class="separator-col">:</td>
            <td><?= $hariIndo ?>, <?= $tanggalFull ?></td>
        </tr>
        <tr>
            <td class="label-col">Waktu</td>
            <td class="separator-col">:</td>
            <td><?= $jam ?> WIB</td>
        </tr>
        <tr>
            <td class="label-col">Tempat</td>
            <td class="separator-col">:</td>
            <td>
                <?= !empty($seminar['ruangan']) ? $seminar['ruangan'] : 'Online / Daring' ?>
            </td>
        </tr>
    </table>

    <p>Telah diselenggarakan Seminar Proposal terhadap mahasiswa:</p>
    
    <table class="table-data">
        <tr>
            <td class="label-col">Nama</td>
            <td class="separator-col">:</td>
            <td><?= $seminar['nama_mahasiswa'] ?></td>
        </tr>
        <tr>
            <td class="label-col">NPM</td>
            <td class="separator-col">:</td>
            <td><?= $seminar['npm'] ?></td>
        </tr>
        <tr>
            <td class="label-col">Program Studi</td>
            <td class="separator-col">:</td>
            <td>Program Studi <?= ucwords(strtolower($seminar['nama_prodi'])) ?></td>
        </tr>
    </table>

    <p>Dengan Judul :   "<?= strtoupper($seminar['judul']) ?>"</p>

    <p>
        Berdasarkan hasil seminar proposal yang telah dilakukan, maka panitia seminar memutuskan bahwa judul tersebut 
        <span class="status-decision">"<?= strtoupper($seminar['status']) ?>"</span>.
    </p>

    <div class="ttd-wrapper">
        <div class="ttd-box">
            <br>Reviewer 1
            <div class="ttd-space"></div>
            ( <?= !empty($seminar['nama_penguji1']) ? $seminar['nama_penguji1'] : '..........................' ?> )
        </div>

        <div class="ttd-box">
            <div class="ttd-date">
                Jakarta, <?= $tanggalFull ?>
            </div>
            Reviewer 2
            <div class="ttd-space"></div>
            ( <?= !empty($seminar['nama_penguji2']) ? $seminar['nama_penguji2'] : '..........................' ?> )
        </div>
    </div>

    <footer>
        <div class="footer-tagline">Smart Campus That You Can Rely On</div>
        Jl. Letjend. Suprapto, Cempaka Putih, Jakarta 10510<br>
        Telp./Fax. 62.21-4269301 atau 62.21-4206674 (Hunting) Ext. 5005<br>
        www.yarsi.ac.id
    </footer>

</body>
</html>