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

            <div class="row">

                <div class="col-lg-5"></div>

                <div class="col-lg-7 d-flex justify-content-end align-items-center">

                    <form class="form-inline" action="<?= base_url("TenagaKependidikan/seminarProposal") ?>" method="get" id="semesterForm">

                        <div class="form-group">
                            <select class="form-control" id="semesterOption" name="semester">
                                <?php foreach($semesterList as $semester) : ?>
                                    <?php if($semester['status'] == 'AKTIF'): ?>
                                        <option value="<?= $semester['kode'] ?>" selected><?= $semester['nama'] ?> (Aktif)</option>
                                    <?php else: ?>
                                        <option value="<?= $semester['kode'] ?>"><?= $semester['nama'] ?></option>
                                    <?php endif; ?>
                                <?php endforeach;?>
                            </select>
                        </div>

                    </form>

                </div>

            </div>



            <!-- table sempro -->

            <div>

                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link active" id="nav-sinkronus-tab" data-toggle="tab" href="#nav-sinkronus" role="tab" aria-controls="nav-sinkronus" aria-selected="true">Jadwal Seminar Proposal</a>
                        <a class="nav-item nav-link" id="nav-asinkronus-tab" data-toggle="tab" href="#nav-asinkronus" role="tab" aria-controls="nav-asinkronus" aria-selected="false">Video Seminar Proposal</a>
                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade active show" id="nav-sinkronus" role="tabpanel" aria-labelledby="nav-sinkronus-tab">
    
                        <div class="table-responsive">
                            <table class="table table-bordered" id="tableSemproSinkronus" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>NPM</th>
                                        <th>Nama</th>
                                        <th>Program Studi</th>
                                        <th>Judul</th>
                                        <th>Bidang</th>
                                        <th>Tanggal Seminar</th>
                                        <th>Link Konferensi / Ruangan</th>
                                        <th>Reviewer 1</th>
                                        <th>Reviewer 2</th>
                                        <th>Status</th>
                                        <th>Komentar</th>
                                        <th>Pembuat Komentar</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
    
                                <tbody>
                                <?php foreach($seminarProposal as $sm): ?>
                                    <?php if ($sm['mode_sempro'] == 'Sinkronus Luring' || $sm['mode_sempro'] == 'Sinkronus Daring') : ?>
                                    <tr>
                                        <td></td>
                                        <td class="npm"><?= $sm['npm'] ?></td>
                                        <td class="nama"><?= $sm['nama_mahasiswa'] ?></td>
                                        <td data-toggle="tooltip" data-placement="top" title="<?= $sm['nama_prodi'] ?>"><?= $sm['inisial_prodi'] ?></td>
                                        <td><?= $sm['judul'] ?></td>
                                        <td data-toggle="tooltip" data-placement="top" title="<?= $sm['nama_bidang'] ?>"><?= $sm['inisial_bidang'] ?></td>
                                        <td class="tanggal" style="min-width: 8vw"><?= date_format(date_create($sm['tanggal']), 'd-m-Y H:i') ?> WIB</td>
    
    
    
                                    <?php if ($sm['mode_sempro'] == 'Sinkronus Daring') : ?>
    
                                        <?php if ($sm['link_konferensi'] == null): ?>
                                            <td>-</td>
                                        <?php else: ?>
                                            <td class="link_konferensi"><a href="<?= $sm['link_konferensi'] ?>" target="_blank">Klik disini!</td>
                                        <?php endif; ?>
    
                                    <?php elseif ($sm['mode_sempro'] == 'Sinkronus Luring') : ?>
    
                                        <?php if ($sm['ruangan'] == null): ?>
                                            <td>-</td>
                                        <?php else: ?>
                                            <td class="ruangan"><?= $sm['ruangan'] ?></td>
                                        <?php endif; ?>
    
                                    <?php endif; ?>
    
    
                                    
                                    <?php if ($sm['dosen_penguji1'] == null) : ?>
    
                                        <td>-</td>
    
                                    <?php else: ?>
    
                                        <?php foreach($dosen as $d) : ?>
                                            <?php if ($d['id'] == $sm['dosen_penguji1']) : ?>
                                                <td data-toggle="tooltip" data-placement="top" title="<?= $d['nama'] ?>" class="dosen_penguji1" data-id="<?= $d['id'] ?>"><?= $d['inisial'] ?></td>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
    
                                    <?php endif; ?>
    
    
    
                                    <?php if ($sm['dosen_penguji2'] == null): ?>
    
                                        <td>-</td>
    
                                    <?php else: ?>
    
                                        <?php foreach($dosen as $d) : ?>
                                            <?php if ($d['id'] == $sm['dosen_penguji2']) : ?>
                                                <td data-toggle="tooltip" data-placement="top" title="<?= $d['nama'] ?>" class="dosen_penguji2" data-id="<?= $d['id'] ?>"><?= $d['inisial'] ?></td>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
    
                                    <?php endif; ?>
    
    
    
                                        <td><?= $sm['status'] != 'TERTUNDA' ? $sm['status'] : "-" ?></td>
                                        <td><?= $sm['komentar'] != null ? $sm['komentar'] : "-" ?></td>
    
    
    
                                    <?php if ($sm['pembuat_komentar'] != null) :?>
    
                                        <?php foreach($dosen as $d) : ?>
                                            <?php if ($d['id'] == $sm['pembuat_komentar']) : ?>
                                                <td data-toggle="tooltip" data-placement="top" title="<?= $d['nama'] ?>" class="pembuat_komentar" data-id="<?= $d['id'] ?>"><?= $d['inisial'] ?></td>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
    
                                    <?php else:?>
    
                                        <td>-</td>
    
                                    <?php endif; ?>
    
                                        <td>
                                            <button type="button" 
                                                    class="btn btn-primary btn-sm btn-cetak-ba"
                                                    data-toggle="modal" 
                                                    data-target="#modalCetakBA"
                                                    data-id="<?= $sm['id']; ?>"
                                                    data-nomor="<?= isset($sm['nomor_berita_acara']) ? $sm['nomor_berita_acara'] : ''; ?>">
                                                <i class="fas fa-print"></i>
                                            </button>
                                        </td>
    
                                    </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
    
                    </div>
    
    
                    <div class="tab-pane fade" id="nav-asinkronus" role="tabpanel" aria-labelledby="nav-asinkronus-tab">
    
                        <div class="table-responsive">
                            <table class="table table-bordered" id="tableSemproAsinkronus" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>NPM</th>
                                        <th>Nama</th>
                                        <th>Program Studi</th>
                                        <th>Judul</th>
                                        <th>Bidang</th>
                                        <th>Tanggal Pengumpulan</th>
                                        <th>Link Video</th>
                                        <th>Status</th>
                                        <th>Komentar</th>
                                        <th>Pembuat Komentar</th>
                                    </tr>
                                </thead>
    
                                <tbody>
                                <?php foreach($seminarProposal as $sm): ?>
                                    <?php if ($sm['mode_sempro'] == 'Asinkronus') : ?>
                                    <tr>
                                        <td></td>
                                        <td class="npm"><?= $sm['npm'] ?></td>
                                        <td class="nama"><?= $sm['nama_mahasiswa'] ?></td>
                                        <td data-toggle="tooltip" data-placement="top" title="<?= $sm['nama_prodi'] ?>"><?= $sm['inisial_prodi'] ?></td>
                                        <td><?= $sm['judul'] ?></td>
                                        <td data-toggle="tooltip" data-placement="top" title="<?= $sm['nama_bidang'] ?>"><?= $sm['inisial_bidang'] ?></td>
                                        <td class="tanggal" style="min-width: 8vw"><?= date_format(date_create($sm['tanggal']), 'd-m-Y H:i') ?> WIB</td>
    
    
    
                                    <?php if ($sm['link_video'] == null): ?>
                                        <td>-</td>
                                    <?php else: ?>
                                        <td class="link_video"><a href="<?= $sm['link_video'] ?>">Klik disini!</td>
                                    <?php endif; ?>
    
    
    
                                        <td><?= $sm['status'] != 'TERTUNDA' ? $sm['status'] : "-" ?></td>
                                        <td><?= $sm['komentar'] != null ? $sm['komentar'] : "-" ?></td>
    
    
    
                                    <?php if ($sm['pembuat_komentar'] != null) :?>
    
                                        <?php foreach($dosen as $d) : ?>
                                            <?php if ($d['id'] == $sm['pembuat_komentar']) : ?>
                                                <td data-toggle="tooltip" data-placement="top" title="<?= $d['nama'] ?>" class="pembuat_komentar" data-id="<?= $d['id'] ?>"><?= $d['inisial'] ?></td>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
    
                                    <?php else:?>
    
                                        <td>-</td>
    
                                    <?php endif; ?>
    
    
                                    </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
    
                    </div>
                </div>

            </div>


        </div>

    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalCetakBA" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Cetak Berita Acara Seminar Proposal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?= base_url('TenagaKependidikan/cetakBeritaAcaraSempro'); ?>" method="post" target="_blank">
                    <div class="modal-body">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="id_seminar" id="id_seminar_input">
                        
                        <div class="form-group">
                            <label for="nomor_ba">Nomor Berita Acara</label>
                            <input type="text" class="form-control" name="nomor_berita_acara" id="nomor_ba_input" required placeholder="Contoh: 001/BA-SEMPRO/FTI/2023">
                            <small class="text-muted">Nomor ini akan dicetak pada surat berita acara.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-print"></i> Simpan & Cetak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?= $this->endSection(); ?>



<?= $this->section("scripts"); ?>
    <script src="<?= base_url("assets/js/tenagakependidikan/seminarProposal.js");?>" defer></script>

    <script>
        $(document).ready(function() {
            // Ketika tombol cetak di klik
            $('.btn-cetak-ba').on('click', function() {
                // Ambil data dari tombol
                const id = $(this).data('id');
                const nomor = $(this).data('nomor');

                // Masukkan value ke dalam inputan modal
                $('#id_seminar_input').val(id);
                $('#nomor_ba_input').val(nomor);
            });
        });
    </script>
<?= $this->endSection(); ?>