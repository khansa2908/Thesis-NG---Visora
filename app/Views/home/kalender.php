<?= $this->extend("layout/template"); ?>



<?= $this->section("content"); ?>

    <link href="<?= base_url("assets/vendor/fullcalendar/main.css") ?>" rel="stylesheet">

    <h1 class="h3 mb-2 text-gray-800"><?= $title ?></h1>

    <div class="card shadow mb-4">

        <div class="card-header py-3">

            <?= $title ?>

        </div>

        <div class="card-body">

            <table id="tableKegiatan" hidden>

                <?php foreach($kegiatanSkripsi as $k):?>

                    <tr class="kegiatan">

                        <td class="id-kegiatan"><?= $k['id'] ?></td>

                        <td class="nama-kegiatan"><?= $k["nama_kegiatan"]?></td>

                        <td class="tanggal-kegiatan"><?= $k["tanggal_mulai"] . "_" . $k["tanggal_selesai"] ?></td>

                    </tr>    

                <?php endforeach; ?>

            </table>

            <div id='calendar'></div>

        </div>

    </div>

<?= $this->endSection(); ?>



<?= $this->section("scripts") ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

    <script src="<?= base_url("assets/vendor/fullcalendar/main.js") ?>"></script>

    <script src="<?= base_url("assets/js/home/kalender.js");?>"></script>

<?= $this->endSection() ?>