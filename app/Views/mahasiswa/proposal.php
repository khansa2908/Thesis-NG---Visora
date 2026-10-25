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

    <div class="row">

        <div class="col-lg-12">
            <?php if (($currentSemester) == null ): ?>
                <div class="alert alert-danger" role="alert">

                    <small>

                        Tidak ada <strong>Semester yang aktif</strong> saat ini. Silahkan hubungi Pihak Fakultas.

                    </small>

                </div>
            <?php elseif ($mahasiswa['status_persetujuan_skripsi'] == null || $mahasiswa['status_persetujuan_skripsi'] == 'Ditolak'): ?>

                <div class="alert alert-danger" role="alert">

                    <small>

                        Buat <strong>Pengajuan Penulisan Skripsi</strong> terlebih dahulu pada <a href="<?= base_url("mahasiswa/pengajuanPenulisanSkripsi") ?>">Menu Berikut</a> dan pastikan <strong>Pengajuan Penulisan Skripsi</strong> anda telah <strong>Diterima</strong>.

                    </small>

                </div>

            <?php elseif ($lastProposal == null): ?>

                <div class="alert alert-info" role="alert">

                    <small>

                        Silahkan kumpulkan <strong>Proposal</strong> pada halaman ini.

                    </small>

                </div>

            <?php elseif($lastProposal['status'] == "TERTUNDA" && $modeSempro != null && $modeSempro['mode'] != 'Asinkronus'): ?>

                <div class="alert alert-info" role="alert">

                    <small>

                        <strong>Proposal</strong> anda telah berhasil diunggah. Harap tunggu <a href="<?= base_url("mahasiswa/seminarProposal")?>">Jadwal Seminar Proposal</a> yang akan dibagikan oleh Kaprodi.

                    </small>

                </div>

            <?php elseif($lastProposal['status'] == "TERTUNDA" && $modeSempro != null && $modeSempro['mode'] == 'Asinkronus'): ?>

                <div class="alert alert-info" role="alert">

                    <small>

                        <strong>Proposal</strong> anda telah berhasil diunggah. Silahkan kumpulkan <strong>Video Seminar Proposal</strong> anda pada <a href="<?= base_url("mahasiswa/seminarProposal")?>">Menu Berikut</a>.

                    </small>

                </div>

            <?php elseif ($lastSkripsi != null && $lastSkripsi['status'] == 'Tidak Lulus'): ?>

                <div class="alert alert-danger" role="alert">

                    <small>

                        Anda dinyatakan <strong>Tidak Lulus</strong> pada semester ini. Silahkan kumpulkan <strong>Proposal</strong> pada Semester berikutnya.

                    </small>

                </div>

            <?php elseif($lastProposal['status'] == 'DITERIMA'): ?>

                <div class="alert alert-success" role="alert">

                    <small>

                        <strong>Proposal</strong> anda telah <strong>Diterima</strong>. Silahkan kumpulkan <strong>Judul Skripsi</strong> pada <a href="<?= base_url("mahasiswa/skripsi")?>">Menu Berikut</a>

                    </small>

                </div>

            <?php elseif($lastProposal['status'] == 'REVISI'): ?>

                <div class="alert alert-success" role="alert">

                    <small>

                        <strong>Proposal</strong> anda telah <strong>Diterima</strong> dengan catatan harus melakukan <strong>Revisi</strong>. Silahkan kumpulkan <strong>Judul Skripsi</strong> pada <a href="<?= base_url("mahasiswa/skripsi")?>">Menu Berikut</a>

                    </small>

                </div>

            <?php elseif($lastProposal['status'] == 'DITOLAK'): ?>

                <div class="alert alert-danger" role="alert">

                    <small>

                        <strong>Proposal</strong> anda telah <strong>Ditolak</strong>. Silahkan lakukan revisi dan mengumpulkan <strong>Proposal Revisi</strong> pada halaman ini.

                    </small>

                </div>

            <?php endif; ?>

        </div>

    </div>

    <div class="card shadow mb-4">

        <div class="card-header py-3">

            <?= $title ?>

        </div>

        <div class="card-body">

            <?php if (isset($is_open) && $is_open == false) : ?>
                <div class="alert alert-danger mb-4">
                    <i class="fas fa-lock"></i> <strong>Pendaftaran Ditutup.</strong> 
                    <br>Periode pengumpulan berkas proposal belum dibuka atau sudah berakhir.
                </div>
            <?php elseif (isset($deadline)) : ?>
                <div class="alert alert-info mb-4">
                    <i class="fas fa-clock"></i> Batas waktu pengajuan proposal: <strong><?= date('d F Y H:i', strtotime($deadline)); ?></strong>
                </div>
            <?php endif; ?>

            <div class="row mb-3">

                <div class="col-lg-3 d-flex align-items-center">

                   <?php
                        // Cek syarat administrasi (Asli)
                        $syaratAdministrasi = ($currentSemester != null && $mahasiswa['status_persetujuan_skripsi'] == 'Disetujui');
                        // Cek syarat proposal (Asli)
                        $syaratProposal = ($lastProposal == null || $lastProposal['status'] == 'DITOLAK');
                        // Cek JADWAL (Baru)
                        $jadwalBuka = (isset($is_open) && $is_open == true);
                    ?>

                    <?php if ($syaratAdministrasi && $syaratProposal && $jadwalBuka) : ?>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#tambahProposal">Tambahkan Proposal</button>
                    
                    <?php else: ?>
                        <?php
                            $alasan = "";
                            if(!$jadwalBuka) $alasan = " (Jadwal Tutup)";
                            elseif(!$syaratAdministrasi) $alasan = " (Syarat Administrasi Belum Lengkap)";
                            elseif(!$syaratProposal) $alasan = " (Proposal Masih Proses/Diterima)";
                        ?>
                        <button class="btn btn-secondary" disabled title="Tidak dapat menambah proposal<?= $alasan ?>">Tambahkan Proposal</button>
                    <?php endif; ?>

                </div>

            </div>



            <div class="table-responsive">

                <table class="table table-bordered" id="proposalMahasiswa" width="100%" cellspacing="0">

                    <thead>

                        <tr>

                            <th>No</th>

                            <th>Judul</th>

                            <th>Bidang</th>

                            <th>Sifat Penelitian</th>

                            <th>Sumber Penelitian</th>

                            <th>Dosen Usulan 1</th>

                            <th>Dosen Usulan 2</th>

                            <th>Status</th>

                            <th>Komentar Reviewer 1</th>

                            <th>Komentar Reviewer 2</th>

                            <th>Aksi</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php

                        $counter = 1;

                        foreach($proposal as $p) :

                        ?>

                            <tr>

                                <td><?= $counter ?></td>

                                <td class="judul" style="min-width: 10vw"><?= $p['judul'] ?></td>

                                <?php foreach($bidang as $b) : ?>

                                    <?php if ($b['id'] == $p['id_bidang']) : ?>

                                        <td data-toggle="tooltip" data-placement="top" title="<?= $b['nama'] ?>" class="bidang" data-id="<?= $p['id_bidang'] ?>"><?= $b['inisial'] ?></td>

                                    <?php endif; ?>

                                <?php endforeach; ?>

                                <td class="sifat"><?= $p['sifat'] ?></td>

                                <td class="sumber"><?= $p['sumber'] ?></td>

                                <?php foreach($dosen as $d) : ?>

                                    <?php if ($d['id'] == $p['dosen_usulan1']) : ?>

                                        <td data-toggle="tooltip" data-placement="top" title="<?= $d['nama'] ?>" class="dosen_usulan1" data-id="<?= $p['dosen_usulan1'] ?>"><?= $d['inisial'] ?></td>

                                    <?php endif; ?>

                                <?php endforeach; ?>

                                <?php foreach($dosen as $d) : ?>

                                    <?php if ($d['id'] == $p['dosen_usulan2']) : ?>

                                        <td data-toggle="tooltip" data-placement="top" title="<?= $d['nama'] ?>" class="dosen_usulan2" data-id="<?= $p['dosen_usulan2'] ?>"><?= $d['inisial'] ?></td>

                                    <?php endif; ?>

                                <?php endforeach; ?>

                                <td>
                                    <?php 
                                        $badgeColor = 'secondary';
                                        if($p['status'] == 'DITERIMA') $badgeColor = 'success';
                                        elseif($p['status'] == 'DITOLAK') $badgeColor = 'danger';
                                        elseif($p['status'] == 'REVISI') $badgeColor = 'warning';
                                    ?>
                                    <span class="badge badge-<?= $badgeColor ?>"><?= $p['status'] ?></span>
                                    </td>

                                    <td><?= (!empty($p['komentar'])) ? $p['komentar'] : "-" ?></td>

                                    <td><?= (!empty($p['komentar_2'])) ? $p['komentar_2'] : "-" ?></td>

                                    <td>

                                    <a role="button" class="btn btn-primary" href="<?= base_url("mahasiswa/detailProposal/".$p['id']) ?>" data-toggle="tooltip" title="Detail"><i class="fas fa-info-circle"></i></a>

                                </td>

                            </tr>

                        <?php 

                        $counter++;

                        endforeach ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>



    <!-- modal tambah proposal -->

    <div class="modal fade" id="tambahProposal" tabindex="-1" role="dialog" aria-labelledby="tambahProposalLabel" aria-hidden="true">

        <div class="modal-dialog" role="document">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title" id="tambahProposalLabel">Tambahkan Proposal</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                    <span aria-hidden="true">&times;</span>

                    </button>

                </div>

                <form action="<?= base_url("mahasiswa/insertProposal") ?>" method="post" id="formTambahProposal" enctype="multipart/form-data">

                    <div class="modal-body">

                        <div class="row">

                            <input type="hidden" id="npm" name="npm" value="<?= $mahasiswa['npm'] ?>">

                            <div class="col-lg-12">

                                <div class="form-group">

                                    <label for="judul">Judul</label>

                                    <textarea class="form-control" id="judul" name="judul" rows="2"></textarea>

                                </div>

                            </div>



                            <div class="col-lg-6">

                                <div class="form-group">

                                    <label for="sifat">Sifat Penelitian</label>

                                    <select class="form-control" id="sifat" name="sifat">

                                        <option value="none" selected disabled> - Pilih Sifat Penelitian - </option>

                                        <option value="Baru">Baru</option>

                                        <option value="Lanjutan">Lanjutan</option>

                                    </select>

                                </div>

                            </div>

                            <div class="col-lg-6">

                                <div class="form-group">

                                    <label for="sumber">Sumber Penelitian</label>

                                    <select class="form-control" id="sumber" name="sumber">

                                        <option value="none" selected disabled> - Pilih Sumber Penelitian - </option>

                                        <option value="Sendiri">Sendiri</option>

                                        <option value="Dosen">Dosen</option>

                                        <option value="Teman">Teman</option>

                                        <option value="Keluarga">Keluarga</option>

                                    </select>

                                </div>

                            </div>

                            <div class="col-lg-12">

                                <div class="form-group">

                                    <label for="bidang">Bidang</label>

                                    <select class="form-control" id="bidang" name="bidang">

                                        <option value="none" selected disabled> - Pilih Bidang - </option>

                                        <?php foreach($bidang as $b) : ?>

                                            <?php if ($b['id_prodi'] == $mahasiswa['id_prodi']): ?>

                                                <option value="<?= $b['id'] ?>"><?= $b['inisial']. " | " .$b['nama'] ?></option>

                                            <?php endif; ?>

                                        <?php endforeach; ?>

                                    </select>

                                </div>

                            </div>

                            <div class="col-lg-6">

                                <div class="form-group">

                                    <label for="dosen_usulan1">Dosen Usulan 1</label>

                                    <select class="form-control" id="dosen_usulan1" name="dosen_usulan1">

                                        <option value="none" selected disabled> - Pilih Dosen Usulan - </option>

                                        <?php foreach($dosen as $d) : ?>

                                            <option value="<?= $d['id'] ?>"><?= $d['inisial']. " | " .$d['nama'] ?></option>

                                        <?php endforeach; ?>

                                    </select>

                                </div>

                            </div>

                            <div class="col-lg-6">

                                <div class="form-group">

                                    <label for="dosen_usulan2">Dosen Usulan 2</label>

                                    <select class="form-control" id="dosen_usulan2" name="dosen_usulan2">

                                        <option value="none" selected disabled> - Pilih Dosen Usulan - </option>

                                        <?php foreach($dosen as $d) : ?>

                                            <option value="<?= $d['id'] ?>"><?= $d['inisial']. " | " .$d['nama'] ?></option>

                                        <?php endforeach; ?>

                                    </select>

                                </div>

                            </div>



                            <div class="col-lg-12">

                                <label>File Proposal</label>

                                <small class="text-muted d-block">Unggah Proposal Skripsi dalam format PDF dengan ukuran maksimal 10 MB</small>

                                <div class="input-group">

                                    <div class="custom-file">

                                        <input type="file" class="custom-file-input" id="file_proposal" name="file_proposal">

                                        <label class="custom-file-label" for="file_proposal">Pilih File Proposal</label>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>

                        <button type="submit" class="btn btn-primary">Simpan</button>

                    </div>

                </form>

            </div>

        </div>

    </div>



<?= $this->endSection(); ?>



<?= $this->section("scripts"); ?>

    <script src="<?= base_url("assets/vendor/daterangepicker/daterangepicker.js") ?>"></script>

    <script src="<?= base_url("assets/js/mahasiswa/proposal.js");?>"></script>

<?= $this->endSection(); ?>