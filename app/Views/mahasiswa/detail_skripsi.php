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

            <?php if (in_array($detailSkripsi['status'], [STATUS_SKRIPSI_DALAMPENGERJAAN, STATUS_SKRIPSI_LULUS])): ?>

                <form action="<?= base_url("mahasiswa/updateSkripsi/".$detailSkripsi['id']) ?>" id="editSkripsi" method="post" enctype="multipart/form-data">

            <?php endif; ?>

                <div class="row">

                    <input type="hidden" id="id_skripsi" name="id_skripsi" value="<?= $detailSkripsi['id'] ?>">

                    <input type="hidden" id="npm" name="npm" value="<?= $detailSkripsi['npm'] ?>">



                    <div class="col-lg-12">

                        <div class="form-group">

                            <label for="judul">Judul</label>

                            <textarea name="judul" id="judul" rows="3" class="form-control" <?= in_array($detailSkripsi['status'], [STATUS_SKRIPSI_DALAMPENGERJAAN, STATUS_SKRIPSI_LULUS]) ? '': 'readonly'; ?>><?= $detailSkripsi['judul'] ?></textarea>

                        </div>

                    </div>

                    <div class="col-lg-6">

                        <div class="form-group">

                            <label for="bidang">Bidang Ilmu</label>

                            <select name="bidang" id="bidang" class="form-control" <?= $detailSkripsi['status'] != STATUS_SKRIPSI_DALAMPENGERJAAN ? 'readonly': ''; ?>>

                                <?php foreach($bidang as $b) : ?>

                                    <?php if ($b['id'] == $detailSkripsi['id_bidang']) : ?>

                                        <option value="<?= $b['id'];?>" selected><?= $b['inisial'];?> | <?= $b['nama']; ?></option>

                                    <?php else: ?>

                                        <option value="<?= $b['id'];?>"><?= $b['inisial'];?> | <?= $b['nama']; ?></option>

                                    <?php endif; ?>

                                <?php endforeach; ?>

                            </select>

                        </div>

                    </div>

                    <div class="col-lg-6">

                        <div class="form-group">

                            <label for="status">Status Skripsi</label>

                            <input type="text" class="form-control" id="status" name="status" value="<?= $detailSkripsi['status'] ?>" disabled>

                        </div>

                    </div>

                    <div class="col-lg-6">

                        <div class="form-group">

                            <label for="sifat">Sifat Penelitian</label>

                            <select class="form-control" id="sifat" name="sifat" <?= $detailSkripsi['status'] != STATUS_SKRIPSI_DALAMPENGERJAAN ? 'readonly': ''; ?>>

                                <option value="Baru" <?= $detailSkripsi['sifat'] == 'Baru' ? 'selected' : ""; ?>>Baru</option>

                                <option value="Lanjutan" <?= $detailSkripsi['sifat'] == 'Lanjutan' ? 'selected' : ""; ?>>Lanjutan</option>

                            </select>

                        </div>

                    </div>

                    <div class="col-lg-6">

                        <div class="form-group">

                            <label for="sumber">Sumber Penelitian</label>

                            <select class="form-control" id="sumber" name="sumber" <?= $detailSkripsi['status'] != STATUS_SKRIPSI_DALAMPENGERJAAN ? 'readonly': ''; ?>>

                                <option value="Sendiri" <?= $detailSkripsi['sumber'] == 'Sendiri' ? "selected": "" ?>>Sendiri</option>

                                <option value="Dosen" <?= $detailSkripsi['sumber'] == 'Dosen' ? "selected": "" ?>>Dosen</option>

                                <option value="Teman" <?= $detailSkripsi['sumber'] == 'Teman' ? "selected": "" ?>>Teman</option>

                                <option value="Keluarga" <?= $detailSkripsi['sumber'] == 'Keluarga' ? "selected": "" ?>>Keluarga</option>

                            </select>

                        </div>

                    </div>

                    <div class="col-lg-6">

                        <div class="form-group">

                            <label for="jenis">Jenis Skripsi</label>

                            <select class="form-control" id="jenis" name="jenis" <?= $detailSkripsi['status'] != STATUS_SKRIPSI_DALAMPENGERJAAN ? 'readonly': ''; ?>>

                                <option value="<?= JENIS_SKRIPSI_REGULER ?>" <?= $detailSkripsi['jenis'] == JENIS_SKRIPSI_REGULER ? "selected": "" ?>><?= JENIS_SKRIPSI_REGULER ?></option>

                                <option value="<?= JENIS_SKRIPSI_PROYEK_TIM ?>" <?= $detailSkripsi['jenis'] == JENIS_SKRIPSI_PROYEK_TIM ? "selected": "" ?>><?= JENIS_SKRIPSI_PROYEK_TIM ?></option>

                                <option value="<?= JENIS_SKRIPSI_PROYEK_MANDIRI ?>" <?= $detailSkripsi['jenis'] == JENIS_SKRIPSI_PROYEK_MANDIRI ? "selected": "" ?>><?= JENIS_SKRIPSI_PROYEK_MANDIRI ?></option>

                            </select>

                        </div>

                    </div>

                    <div class="col-lg-6">

                        <div class="form-group">

                            <label for="rekomendasi">Rekomendasi Jenis Skripsi</label>

                            <input type="text" class="form-control" id="rekomendasi" name="rekomendasi" value="<?= $detailSkripsi['rekomendasi_jenis'] ?>" disabled>

                        </div>

                    </div>

                    <div class="col-lg-12" style="display: none">
                        <div class="form-group">
                            <label for="klien">Nama Klien</label>
                            <input type="text" class="form-control" id="klien" name="klien" value="<?= $detailSkripsi['klien'] ?>">
                        </div>
                    </div>

                    <div class="col-lg-6">

                        <div class="form-group">

                            <label for="pembimbing1">Pembimbing Ilmu 1</label>

                            <input type="text" class="form-control" id="pembimbing1" name="pembimbing1" value="<?= $detailSkripsi['inisial_pembimbing1'] ?> <?= $detailSkripsi['inisial_pembimbing1'] == null ? "-": "|" ?> <?= $detailSkripsi['nama_pembimbing1'] ?>" disabled>

                        </div>

                    </div>

                    <div class="col-lg-6">

                        <div class="form-group">

                            <label for="total_bimbingan_ilmu1">Total Bimbingan dengan Pembimbing Ilmu 1 yang Disetujui</label>

                            <input type="text" class="form-control" id="total_bimbingan_ilmu1" name="total_bimbingan_ilmu1" value="<?= $detailSkripsi['total_bimbingan1'] ?>" disabled>

                        </div>

                    </div>

                    <div class="col-lg-6">

                        <div class="form-group">

                            <label for="pembimbing2">Pembimbing Ilmu 2</label>

                            <input type="text" class="form-control" id="pembimbing2" name="pembimbing2" value="<?= $detailSkripsi['inisial_pembimbing2'] ?> <?= $detailSkripsi['inisial_pembimbing2'] == null ? "-": "|" ?> <?= $detailSkripsi['nama_pembimbing2'] ?>" disabled>

                        </div>

                    </div>

                    <div class="col-lg-6">

                        <div class="form-group">

                            <label for="total_bimbingan_ilmu2">Total Bimbingan dengan Pembimbing Ilmu 2 yang Disetujui</label>

                            <input type="text" class="form-control" id="total_bimbingan_ilmu2" name="total_bimbingan_ilmu2" value="<?= $detailSkripsi['total_bimbingan2'] ?>" disabled>

                        </div>

                    </div>

                    <div class="col-lg-6">

                        <div class="form-group">

                            <label for="pembimbing3">Pembimbing Agama</label>

                            <input type="text" class="form-control" id="pembimbing3" name="pembimbing3" value="<?= $detailSkripsi['inisial_pembimbing3'] ?> <?= $detailSkripsi['inisial_pembimbing3'] == null ? "-": "|" ?> <?= $detailSkripsi['nama_pembimbing3'] ?>" disabled>

                        </div>

                    </div>

                    <div class="col-lg-6">

                        <div class="form-group">

                            <label for="total_bimbingan_agama">Total Bimbingan dengan Pembimbing Agama yang Disetujui</label>

                            <input type="text" class="form-control" id="total_bimbingan_agama" name="total_bimbingan_agama" value="<?= $detailSkripsi['total_bimbingan3']?>" disabled>

                        </div>

                    </div>

                    <div class="col-lg-12 my-1">

                        <label>File Final Skripsi</label>

                        <?php if ($detailSkripsi['file_skripsi'] == null && $detailSkripsi['status'] == STATUS_SKRIPSI_LULUS) : ?>

                            <small class="text-muted d-block">Unggah Final Skripsi dalam format PDF dengan ukuran maksimal 10 MB</small>

                            <div class="input-group">

                                <div class="custom-file">

                                    <input type="file" class="custom-file-input" id="file_skripsi" name="file_skripsi">

                                    <label class="custom-file-label" for="file_skripsi">Pilih File Final Skripsi</label>

                                </div>

                            </div>

                        <?php elseif ($detailSkripsi['file_skripsi'] != null && $detailSkripsi['status'] == STATUS_SKRIPSI_LULUS): ?>

                            <div class="input-group">

                                <input type="text" class="form-control" value="<?= $detailSkripsi['file_skripsi'] ?>" readonly>

                                <div class="input-group-append">

                                    <a role="button" class="btn btn-outline-primary" id="tinjauSkripsi" data-id="<?= $detailSkripsi['id'] ?>">Tinjau</a>

                                </div>

                            </div>

                        <?php elseif (in_array($detailSkripsi['status'], [STATUS_SKRIPSI_DALAMPENGERJAAN, STATUS_SKRIPSI_TIDAKLULUS])) : ?>

                            <div class="input-group">

                                <div class="custom-file">

                                    <input type="file" class="custom-file-input" id="file_skripsi" name="file_skripsi" disabled>

                                    <label class="custom-file-label" for="file_skripsi">Pilih File Final Skripsi</label>

                                </div>

                            </div>

                        <?php endif; ?>

                    </div>



                    <div class="col-lg-12 my-2 d-flex justify-content-end">

                        <a class="btn btn-secondary mr-2" role="button" href="<?= base_url("mahasiswa/skripsi") ?>">Kembali</a>

                        <?php if (in_array($detailSkripsi['status'], [STATUS_SKRIPSI_DALAMPENGERJAAN, STATUS_SKRIPSI_LULUS])): ?>

                            <button class="btn btn-primary" type="submit">Simpan</button>

                        <?php endif; ?>

                    </div>

                </div>

        <?php if (in_array($detailSkripsi['status'], [STATUS_SKRIPSI_DALAMPENGERJAAN, STATUS_SKRIPSI_LULUS])): ?>

            </form>

        <?php endif; ?>

        </div>

    </div>



    <!-- modal untuk preview file -->

    <div class="modal fade" id="filePreview" tabindex="-1" role="dialog" aria-labelledby="filePreviewLabel" aria-hidden="true">

        <div class="modal-dialog modal-lg" role="document">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title" id="filePreviewLabel"></h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                    <span aria-hidden="true">&times;</span>

                    </button>

                </div>

                <div class="modal-body">

                    <div class="row">

                        <div class="col-lg-12">

                            <div class="form-group">

                                <div class="embed-responsive embed-responsive-16by9" id="previewContainer">

                                    <iframe class="embed-responsive-item" src=""></iframe>

                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Kembali</button>

                        <form action="" id="formUnduh" method="post">

                            <button class="btn btn-primary" type="submit">Unduh</button>

                        </form>

                        <?php if ($detailSkripsi['status'] == STATUS_SKRIPSI_LULUS): ?>

                            <form action="" id="formHapus" method="post">

                                <button class="btn btn-danger" type="submit">Hapus</button>

                            </form>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

        </div>

    </div>



<?= $this->endSection(); ?>



<?= $this->section("scripts"); ?>

    <script src="<?= base_url("assets/js/mahasiswa/detailSkripsi.js");?>"></script>

<?= $this->endSection(); ?>