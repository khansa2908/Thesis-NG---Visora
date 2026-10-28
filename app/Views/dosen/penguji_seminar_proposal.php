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

                    <form class="form-inline" action="<?= base_url("dosen/pengujiSeminarProposal") ?>" method="get" id="semesterForm">

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

            <div class="table-responsive">

                <table class="table table-bordered" id="jadwalPengujiSempro" width="100%" cellspacing="0">

                    <thead>

                        <tr>

                            <th>No</th>

                            <th>NPM</th>

                            <th>Nama</th>

                            <th>Judul</th>

                            <th>Bidang</th>

                        <?php if ($modeSempro != null && $modeSempro['mode'] == 'Asinkronus') : ?>

                            <th>Tanggal Pengumpulan</th>

                        <?php else : ?>

                            <th>Tanggal Seminar</th>

                        <?php endif; ?>



                        <?php if ($modeSempro != null && $modeSempro['mode'] == 'Sinkronus Daring') : ?>

                            <th>Link Konferensi</th>

                        <?php elseif ($modeSempro != null && $modeSempro['mode'] == 'Sinkronus Luring') : ?>

                            <th>Ruangan</th>

                        <?php elseif ($modeSempro != null && $modeSempro['mode'] == 'Asinkronus') : ?>

                            <th>Video</th>

                        <?php endif; ?>

                        

                        <?php if ($modeSempro != null && $modeSempro['mode'] == 'Asinkronus') : ?>

                            <th hidden>Reviewer 1</th>

                            <th hidden>Reviewer 2</th>

                        <?php else: ?>

                            <th>Reviewer 1</th>

                            <th>Reviewer 2</th>

                        <?php endif; ?>

                            <th>Status</th>

                            <th>Rekomendasi Jenis Skripsi</th>

                            <th>Komentar Reviewer 1</th>

                            <th>Komentar Reviewer 2</th>

                            <th>Aksi</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php 

                    $counter = 1;

                    // Ambil ID Dosen yang sedang login untuk filter komentar
                    $idDosenLogin = session()->get('user_session')['id'];

                    foreach($seminarProposal as $sm):
                    
                        // --- LOGIKA PINTAR: Tentukan Komentar Siapa yang Harus Muncul di Modal ---
                        $komentarSaya = "";
                        if ($idDosenLogin == $sm['dosen_penguji1']) {
                        $komentarSaya = $sm['komentar'] ?? "";
                        } elseif (isset($sm['dosen_penguji2']) && $idDosenLogin == $sm['dosen_penguji2']) {
                         $komentarSaya = $sm['komentar_2'] ?? "";
                            }
                    ?>

                        <tr>

                            <td><?= $counter ?></td>

                            <td class="npm"><?= $sm['npm'] ?></td>

                            <td><?= $sm['nama_mahasiswa'] ?></td>

                            <td style="min-width: 10vw"><?= $sm['judul'] ?></td>

                            <td data-toggle="tooltip" data-placement="top" title="<?= $sm['nama_bidang'] ?>"><?= $sm['inisial_bidang'] ?></td>



                            <td class="tanggal" style="min-width: 8vw"><?= date_format(date_create($sm['tanggal']), 'd-m-Y H:i') ?> WIB</td>



                        <?php if ($modeSempro != null && $modeSempro['mode'] == 'Sinkronus Daring') : ?>

                            <td class="link_konferensi"><a href="<?= $sm['link_konferensi'] ?>" target="_blank">Klik disini!</a></td>

                        <?php elseif ($modeSempro != null && $modeSempro['mode'] == 'Sinkronus Luring') : ?>

                            <td class="ruangan"><?= $sm['ruangan'] ?></td>

                        <?php elseif ($modeSempro != null && $modeSempro['mode'] == 'Asinkronus') : ?>

                            <td class="link_video"><a href="<?= $sm['link_video'] ?>" target="_blank">Klik disini!</a></td>

                        <?php endif; ?>



                        <?php if ($modeSempro != null && $modeSempro['mode'] == 'Asinkronus') : ?>
                            <td hidden>-</td>
                            <td hidden>-</td>
                        <?php else : ?>
                            <?php 
                                $namaRev1 = "-"; $inisialRev1 = "-";
                                foreach($dosen as $d) { if ($d['id'] == $sm['dosen_penguji1']) { $namaRev1 = $d['nama']; $inisialRev1 = $d['inisial']; break; } }
                            ?>
                            <td data-toggle="tooltip" title="<?= $namaRev1 ?>"><?= $inisialRev1 ?></td>

                            <?php 
                                $namaRev2 = "-"; $inisialRev2 = "-";
                                if ($sm['dosen_penguji2']) {
                                    foreach($dosen as $d) { if ($d['id'] == $sm['dosen_penguji2']) { $namaRev2 = $d['nama']; $inisialRev2 = $d['inisial']; break; } }
                                }
                            ?>
                            <td data-toggle="tooltip" title="<?= $namaRev2 ?>"><?= $inisialRev2 ?></td>
                        <?php endif; ?>



                            <td class="status"><?= $sm['status'] ?></td>

                            <td class="jenis_skripsi"><?= $sm['jenis_skripsi'] == null ? "-" : $sm['jenis_skripsi'] ?></td>

                            <td class="komentar_1">
                                <?= $sm['komentar'] != null ? $sm['komentar'] : "-" ?>
                            </td>

                            <td class="komentar_2">
                                <?= (isset($sm['komentar_2']) && $sm['komentar_2'] != null) ? $sm['komentar_2'] : "-" ?>
                            </td>

                            

                            <td style="min-width: 7vw">

                                <?php if($sm['file_proposal'] == "" || $sm['file_proposal'] == null): ?>

                                    <button class="btn btn-primary disabled" data-toggle="tooltip" data-placement="top" title="Unduh Proposal"><i class="fa fa-download" aria-hidden="true"></i></button>
                                    
                                <?php else: ?>
                                
                                    <form action="<?= base_url("mahasiswa/downloadProposal/".$sm['id_proposal']) ?>" method="post" class="d-inline">

                                        <button class="btn btn-primary" type="submit" data-toggle="tooltip" data-placement="top" title="Unduh Proposal"><i class="fa fa-download" aria-hidden="true"></i></button>

                                    </form>

                                <?php endif; ?>

                                <button class="btn btn-primary buat-nilai" 
                                    data-toggle="tooltip" 
                                    data-placement="top" 
                                    title="Beri Penilaian" 
                                    data-id="<?= $sm['id'] ?>"
                                    data-komentar="<?= htmlspecialchars($komentarSaya) ?>"
                                    data-status="<?= $sm['status'] ?>"
                                    data-jenis="<?= $sm['jenis_skripsi'] ?>">
                                    <i class="fa fa-comment"></i>
                                </button>

                            </td>

                        </tr>

                        <?php $counter++; ?>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>



    <!-- modal untuk menilai seminar proposal -->

    <div class="modal fade" id="penilaianSempro" tabindex="-1" role="dialog" aria-labelledby="penilaianSemproLabel" aria-hidden="true">

        <div class="modal-dialog" role="document">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title" id="penilaianSemproLabel">Penilaian Seminar Proposal</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                    <span aria-hidden="true">&times;</span>

                    </button>

                </div>

                <form action="" method="post" id="formPenilaianSempro">

                    <div class="modal-body">

                        <div class="row">

                            <div class="col-lg-12">

                                <div class="form-group">

                                    <label for="komentar">Komentar</label>

                                    <textarea class="form-control" id="komentar" name="komentar" rows="8"></textarea>

                                </div>

                            </div>

                            <div class="col-lg-6">
                                <label for="status">Status Proposal</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="none" selected disabled> - Pilih Status Proposal - </option>
                                    <option value="DITERIMA">Diterima</option>
                                    <option value="REVISI">Revisi</option>
                                    <option value="DITOLAK">Ditolak</option>
                                </select>
                            </div>

                            <div class="col-lg-6">
                                <label for="jenis_skripsi">Jenis Skripsi</label>
                                <select name="jenis_skripsi" id="jenis_skripsi" class="form-control">
                                    <option value="none" selected disabled> - Pilih Jenis Skripsi - </option>
                                    <option value="SKRIPSI REGULER">Skripsi Reguler</option>
                                    <option value="PROYEK TIM">Proyek Tim</option>
                                    <option value="PROYEK MANDIRI">Proyek Mandiri</option>
                                </select>
                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="submit" class="btn btn-primary">Simpan</button>

                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>

                    </div>

                </form>

            </div>

        </div>

    </div>

<?= $this->endSection(); ?>



<?= $this->section("scripts"); ?>

    <script src="<?= base_url("assets/js/dosen/pengujiSeminarProposal.js");?>" defer></script>

    <script>
    $(document).ready(function() {
        // Paksa hapus event lama dulu biar tidak bentrok/dobel
        $(document).off('click', '.buat-nilai').on('click', '.buat-nilai', function() {
            
            const id = $(this).data('id');
            const komentar = $(this).data('komentar');
            const status = $(this).data('status');
            const jenis = $(this).data('jenis');

            // PERHATIKAN BARIS INI:
            // Kita taruh garis miring '/' secara manual di Javascript agar pasti terbaca
            // url dasar + "/" + id
            const baseUrl = '<?= base_url("dosen/insertPenilaianSempro") ?>';
            const finalUrl = baseUrl + '/' + id; 

            // Debugging: Cek di Console browser (F12) apakah URL sudah benar
            console.log("URL Action:", finalUrl);

            $('#formPenilaianSempro').attr('action', finalUrl);
            $('#komentar').val(komentar);
            
            if(status && status !== 'TERTUNDA') {
                $('#status').val(status);
            } else {
                $('#status').val('none');
            }

            if(jenis) {
                $('#jenis_skripsi').val(jenis);
            } else {
                $('#jenis_skripsi').val('none');
            }

            $('#penilaianSempro').modal('show');
        });
    });
    </script>
<?= $this->endSection(); ?>