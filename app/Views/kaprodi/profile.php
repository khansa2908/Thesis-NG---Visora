<?= $this->extend("layout/template"); ?>

<?= $this->section("content"); ?>

<?php if (session()->getFlashdata("message")) : ?>

    <div id="flashdata" data-open="true">

        <p id="icon" hidden><?= session()->getFlashdata("message")["icon"]; ?></p>

        <p id="title" hidden><?= session()->getFlashdata("message")["title"]; ?></p>

        <p id="text" hidden><?= session()->getFlashdata("message")["text"]; ?></p>

    </div>

<?php endif; ?>

    <h1 class="h3 mb-2 text-gray-800 offset-lg-2"><?= $title ?></h1>

    <div class="card shadow mb-4 col-lg-8 offset-lg-2 p-0">

        <div class="card-header py-3">

            <?= $title ?>

        </div>

        <div class="card-body">

            <form action="<?= base_url("Kaprodi/saveProfile") ?>" method="post" id="formDataDiri">
                <input type="hidden" name="id" value="<?= $dataDiri["id"] ?>">

                <div class="col-lg-12">
                    <div class="form-group" >
                        <label>Nama Program Studi</label>
                        <input type="text" class="form-control" value="<?= $dataDiri["nama"] ?>" id="nama" name="nama">
                    </div>

                </div>

                <div class="col-lg-12">
                    <div class="form-group" >
                        <label for="email">Email</label>
                        <input type="text" class="form-control" id="email" name="email" value="<?= $dataDiri["email"] ?>">
                    </div>
                </div>

                <div class="col-lg-12 d-flex justify-content-end my-2">
                    <button class="btn btn-primary" type="submit">Simpan</button>
                </div>
            </form>


        </div>

    </div>
<?= $this->endSection(); ?>



<?= $this->section("scripts"); ?>
    <script src="<?= base_url("assets/js/kaprodi/profile.js");?>"></script>
<?= $this->endSection(); ?>