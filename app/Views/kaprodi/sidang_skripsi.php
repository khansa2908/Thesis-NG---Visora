<?= $this->extend("layout/template"); ?>



<?= $this->section("content"); ?>

<?php if (session()->getFlashdata("message")) : ?>

    <div id="flashdata" data-open="true">

        <p id="icon" hidden><?= session()->getFlashdata("message")["icon"]; ?></p>

        <p id="title" hidden><?= session()->getFlashdata("message")["title"]; ?></p>

        <?php if (isset(session()->getFlashdata("message")["text"])) : ?>
            <p id="text" hidden><?= session()->getFlashdata("message")["text"]; ?></p>
        <?php else: ?>
            <p id="html" hidden><?= session()->getFlashdata("message")["html"]; ?></p>
        <?php endif; ?>

    </div>

<?php endif; ?>

    <h1 class="h3 mb-2 text-gray-800"><?= $title ?></h1>

    

    <div class="row">

        <div class="col-lg-12">

            <div class="alert alert-info" role="alert">

                <small>Silahkan tambahkan <strong>Jadwal Sidang Skripsi</strong> dengan cara mengklik tombol <strong>Tambahkan Jadwal</strong>.</small>

            </div>

        </div>

    </div>

    <div class="card shadow mb-4">

        <div class="card-header py-3">

            Kelola Jadwal Sidang Skripsi

        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-lg-5">
                
                    <?php if ($currentSemester != null && $currentSemester['status'] == 'AKTIF'): ?>
                        <button class="btn btn-primary mr-2 mb-3" data-toggle="modal" data-target="#tambahJadwal">Tambahkan Jadwal</button>
                    <?php else: ?>
                        <button class="btn btn-primary mr-2 mb-3" disabled>Tambahkan Jadwal</button>
                    <?php endif; ?>


                </div>

                <div class="col-lg-7 d-flex justify-content-end align-items-center">

                    <form class="form-inline" action="<?= base_url("Kaprodi/sidangSkripsi") ?>" method="get" id="semesterForm">
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



            <!-- table sidang skripsi -->

            <div class="table-responsive">

                <table class="table table-bordered" id="jadwalSidangSkripsi" width="100%" cellspacing="0">

                    <thead>

                        <tr>

                            <th>No</th>

                            <th>NPM</th>

                            <th>Nama</th>

                            <th>Judul</th>

                            <th>Bidang</th>

                            <th>Tanggal Sidang</th>

                            <th>Ruangan</th>

                            <th>Ketua Sidang</th>

                            <th>Penguji</th>

                            <th>Pemb. Ilmu 1</th>

                            <th>Pemb. Ilmu 2</th>

                            <th>Pemb. Agama</th>

                            <th>Klien</th>

                            <th>Status</th>

                            <th>Aksi</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php foreach ($sidangSkripsi as $ss) :?>

                        <tr>

                            <td></td>

                            <td class="npm"><?= $ss['npm'] ?></td>

                            <td class="nama"><?= $ss['nama_mahasiswa'] ?></td>

                            <td class="judul"><?= $ss['judul'] ?></td>

                            <td data-toggle="tooltip" data-placement="top" title="<?= $ss['nama_bidang'] ?>" class="bidang"><?= $ss['inisial_bidang'] ?></td>                        

                            <td class="tanggal" style="min-width: 8vw"><?= date_format(date_create($ss['tanggal']), 'd-m-Y H:i') ?> WIB</td>



                            <?php if (preg_match('#^https?://#i', $ss['ruangan']) === 1): ?>

                                <td class="ruangan"><a href="<?= $ss['ruangan'] ?>" target="_blank">Klik disini!</a></td>

                            <?php else: ?>

                                <td class="ruangan"><?= $ss['ruangan'] ?></td>

                            <?php endif; ?>

                            <?php if ($ss['nama_ketua'] == null): ?>
                                <td>-</td>
                            <?php else: ?>
                                <td data-toggle="tooltip" data-placement="top" title="<?= $ss['nama_ketua'] ?>">
                                    <?= $ss['inisial_ketua'] ?>
                                </td>
                            <?php endif; ?>

                            <td data-toggle="tooltip" data-placement="top" title="<?= $ss['nama_penguji'] ?>" class="penguji" data-id="<?= $ss['dosen_penguji'] ?>"><?= $ss['inisial_penguji'] ?></td>



                            <td data-toggle="tooltip" data-placement="top" title="<?= $ss['nama_pembimbing1'] ?>" class="pembimbing1"><?= $ss['inisial_pembimbing1'] ?></td>



                            <?php if ($ss['nama_pembimbing2'] == null): ?>

                                <td>-</td>

                            <?php else: ?>

                                <td data-toggle="tooltip" data-placement="top" title="<?= $ss['nama_pembimbing2'] ?>" class="pembimbing2"><?= $ss['inisial_pembimbing2'] ?></td>

                            <?php endif;?>

                            <td data-toggle="tooltip" data-placement="top" title="<?= $ss['nama_pembimbing_agama'] ?>" class="pembimbing_agama"><?= $ss['inisial_pembimbing_agama'] ?></td>

                            <?php if ($ss['inisial_klien'] == null): ?>

                                <td>-</td>

                            <?php else: ?>

                                <td data-toggle="tooltip" data-placement="top" title="<?= $ss['instansi_klien'] ?>" class="klien" data-id="<?= $ss['id_klien'] ?>"><?= $ss['inisial_klien'] ?></td>

                            <?php endif;?>

                            <td><?= $ss['status'] == null ? "TERTUNDA" : $ss['status'] ?></td>

                            <td style="min-width: 7vw">

                                <?php if ($ss['jumlah_nilai_masuk'] == 0) : ?>

                                    <?php 
                                        $timestamp = strtotime($ss['tanggal']);
                                        $dateOnly = date('Y-m-d', $timestamp);
                                        $timeOnly = date('H:i', $timestamp);
                                    ?>

                                    <button class="btn btn-primary btn-edit" data-toggle="tooltip" data-placement="top" title="Ubah" 
                                        data-id="<?= $ss['id'] ?>"
                                        data-npm="<?= $ss['npm'] ?>"
                                        data-nama="<?= $ss['nama_mahasiswa'] ?>"
                                        data-judul="<?= $ss['judul'] ?>"
                                        data-bidang="<?= $ss['nama_bidang'] ?>"
                                        data-pembimbing1="<?= $ss['nama_pembimbing1'] ?>"
                                        data-pembimbing2="<?= $ss['nama_pembimbing2'] == null ? '-' : $ss['nama_pembimbing2'] ?>"
                                        data-pembimbing_agama="<?= $ss['nama_pembimbing_agama'] ?>"
                                        data-tanggal="<?= $dateOnly ?>"
                                        data-jam="<?= $timeOnly ?>"
                                        data-ruangan="<?= $ss['ruangan'] ?>"
                                        data-ketua="<?= $ss['ketua_sidang'] ?>"
                                        data-penguji="<?= $ss['dosen_penguji'] ?>"
                                        data-klien="<?= $ss['klien'] ?>"><i class="fas fa-pencil-alt"></i></button>

                                    <form action="<?= base_url("Kaprodi/deleteJadwalSidangSkripsi/" . $ss['id']) ?>" method="post" class="d-inline" id="formHapusJadwal">

                                        <button type="submit" class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Hapus"><i class="fa fa-trash"></i></button>

                                    </form>

                                <?php else: ?>

                                    <button class="btn btn-primary" disabled><i class="fas fa-pencil-alt"></i></button>

                                    <button type="button" class="btn btn-danger" disabled><i class="fa fa-trash"></i></button>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>



    <!-- modal tambah jadwal sidang -->

    <div class="modal fade" id="tambahJadwal" tabindex="-1" role="dialog" aria-labelledby="tambahJadwalLabel" aria-hidden="true">

        <div class="modal-dialog" role="document">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title" id="tambahJadwalLabel">Tambahkan Jadwal Sidang Skripsi</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                    <span aria-hidden="true">&times;</span>

                    </button>

                </div>

                <nav>

                    <div class="nav nav-tabs" id="nav-tab" role="tablist">

                        <a class="nav-item nav-link active" id="nav-single-tab" data-toggle="tab" href="#nav-single" role="tab" aria-controls="nav-single" aria-selected="true">Single</a>

                        <a class="nav-item nav-link" id="nav-batch-tab" data-toggle="tab" href="#nav-batch" role="tab" aria-controls="nav-batch" aria-selected="false">Batch</a>

                    </div>

                </nav>

                <div class="tab-content" id="nav-tabContent">

                    <div class="tab-pane fade show active" id="nav-single" role="tabpanel" aria-labelledby="nav-single-tab">

                        <form action="<?= base_url("Kaprodi/insertJadwalSidangSkripsi") ?>" method="post" id="formTambahJadwal">

                            <div class="modal-body">

                                <div class="row">

                                    <div class="col-lg-12">

                                        <div class="form-group">

                                            <label for="mahasiswa">Mahasiswa</label>

                                            <select class="form-control" id="mahasiswa" name="mahasiswa">

                                                <option value="none" selected disabled> - Pilih Mahasiswa - </option>

                                                <?php foreach($mahasiswa as $m) : ?>

                                                    <option value="<?= $m['npm'] ?>"><?= $m['npm'] ." | " . $m['nama'] ?></option>

                                                <?php endforeach; ?>

                                            </select>

                                        </div>

                                    </div>



                                    <div class="col-lg-12">

                                        <div class="form-group judul">

                                            <label for="judul">Judul</label>

                                            <textarea rows="2" class="form-control" id="judul" name="judul" disabled></textarea>

                                            <?php foreach($mahasiswa as $m) : ?>

                                                <textarea class="form-control" id="judul" name="judul" row="2" data-npm="<?= $m['npm'] ?>" hidden disabled><?= $m['judul'] ?></textarea>

                                            <?php endforeach; ?>

                                        </div>

                                    </div>



                                    <div class="col-lg-12">

                                        <div class="form-group bidang">

                                            <label for="bidang">Bidang</label>

                                            <input type="text" class="form-control" id="bidang" name="bidang" disabled>

                                            <?php foreach($mahasiswa as $m) : ?>

                                                <input type="text" class="form-control" id="bidang" name="bidang" value="<?= $m['nama_bidang'] ?>" data-npm="<?= $m['npm'] ?>" hidden disabled>

                                            <?php endforeach; ?>

                                        </div>

                                    </div>

                                    <div class="col-lg-12">

                                        <div class="form-group">

                                            <label for="jenis_skripsi">Jenis Skripsi</label>

                                            <input type="text" class="form-control" id="jenis_skripsi" name="jenis_skripsi" disabled>

                                            <?php foreach($mahasiswa as $m) : ?>

                                                <input type="text" class="form-control" id="jenis_skripsi" name="jenis_skripsi" value="<?= $m['jenis_skripsi'] ?>" data-npm="<?= $m['npm'] ?>" hidden disabled>

                                            <?php endforeach; ?>

                                        </div>

                                    </div>


                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="ketua_sidang">Ketua Komisi Sidang</label>
                                            <select name="ketua_sidang" id="ketua_sidang" class="form-control" required>
                                                <option value="" selected disabled> - Pilih Ketua Sidang - </option>
                                                <?php foreach($dosen as $d) : ?>
                                                    <option value="<?= $d['id'] ?>"><?= $d['inisial'] . " | " . $d['nama'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>



                                    <div class="col-lg-6">

                                        <div class="form-group bidang">

                                            <label for="pembimbing1">Pembimbing Ilmu 1</label>

                                            <input type="text" class="form-control" id="pembimbing1" name="pembimbing1" disabled>

                                            <?php foreach($mahasiswa as $m) : ?>

                                                <input type="text" class="form-control" id="pembimbing1" name="pembimbing1" value="<?= $m['nama_pembimbing1'] ?>" data-npm="<?= $m['npm'] ?>" hidden disabled>

                                            <?php endforeach; ?>

                                        </div>

                                    </div>

                                    <div class="col-lg-6">

                                        <div class="form-group bidang">

                                            <label for="pembimbing2">Pembimbing Ilmu 2</label>

                                            <input type="text" class="form-control" id="pembimbing2" name="pembimbing2" disabled>

                                            <?php foreach($mahasiswa as $m) : ?>

                                                <input type="text" class="form-control" id="pembimbing2" name="pembimbing2" value="<?= $m['nama_pembimbing2'] == null ? "-": $m['nama_pembimbing2'] ?>" data-npm="<?= $m['npm'] ?>" hidden disabled>

                                            <?php endforeach; ?>

                                        </div>

                                    </div>

                                    <div class="col-lg-6">

                                        <div class="form-group bidang">

                                            <label for="pembimbing_agama">Pembimbing Agama</label>

                                            <input type="text" class="form-control" id="pembimbing_agama" name="pembimbing_agama" disabled>

                                            <?php foreach($mahasiswa as $m) : ?>

                                                <input type="text" class="form-control" id="pembimbing_agama" name="pembimbing_agama" value="<?= $m['nama_pembimbing_agama'] ?>" data-npm="<?= $m['npm'] ?>" hidden disabled>

                                            <?php endforeach; ?>

                                        </div>

                                    </div>

                                    <div class="col-lg-6">

                                        <div class="form-group">

                                            <label for="dosen_penguji">Dosen Penguji</label>

                                            <select name="dosen_penguji" id="dosen_penguji" class="form-control">

                                                <option value="none" selected disabled> - Pilih Dosen Penguji - </option>

                                                <?php foreach($dosen as $d) : ?>

                                                    <option value="<?= $d['id'] ?>"><?= $d['inisial'] . " | " . $d['nama'] ?></option>

                                                <?php endforeach; ?>

                                            </select>

                                        </div>

                                    </div>

                                    <div class="col-lg-6">

                                        <div class="form-group">

                                            <label for="rekomendasi_klien">Rekomendasi Klien</label>

                                            <input type="text" class="form-control" id="rekomendasi_klien" name="rekomendasi_klien" disabled>

                                            <?php foreach($mahasiswa as $m) : ?>

                                                <input type="text" class="form-control" id="rekomendasi_klien" name="rekomendasi_klien" value="<?= $m['rekomendasi_klien'] ?>" data-npm="<?= $m['npm'] ?>" hidden disabled>

                                            <?php endforeach; ?>

                                        </div>

                                    </div>



                                    <div class="col-lg-6">

                                        <div class="form-group">

                                            <label for="klien">Klien</label>

                                            <select name="klien" id="klien" class="form-control" required>

                                                <option value="none" selected disabled> - Pilih Klien - </option>

                                                <?php foreach($listKlien as $lk) : ?>

                                                    <option value="<?= $lk['id'] ?>"><?= $lk['inisial'] . " | " . $lk['instansi'] ?></option>

                                                <?php endforeach; ?>

                                            </select>

                                        </div>

                                    </div>

                                    <div class="col-lg-12">

                                        <div class="form-group">

                                            <label for="ruangan">Ruangan</label>

                                            <input type="text" class="form-control" id="ruangan" name="ruangan" placeholder="contoh: Ruangan MDI">

                                        </div>

                                    </div>



                                    <div class="col-lg-6">

                                        <div class="form-group">

                                            <label for="tanggal">Tanggal Sidang</label>

                                            <input type="text" class="form-control" id="tanggal" name="tanggal">

                                        </div>

                                    </div>

                                    <div class="col-lg-6">

                                        <div class="form-group">

                                            <label for="jam">Waktu Sidang</label>

                                            <!-- <input type="text" class="form-control" id="jam" name="jam" placeholder="format: (hh:mm)"> -->
                                            <input type="time" name="jam" id="jam" class="form-control" required>
                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="modal-footer">

                                <button type="submit" class="btn btn-primary">Simpan</button>

                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>

                            </div>

                        </form>

                    </div>

                    <div class="tab-pane fade" id="nav-batch" role="tabpanel" aria-labelledby="nav-batch-tab">

                        <form action="<?= base_url("Kaprodi/insertJadwalSidangSkripsiBatch") ?>" method="post" id="formJadwalBatch" enctype="multipart/form-data">

                            <div class="modal-body">

                                <label>File Jadwal Sidang Skripsi</label>

                                <div class="input-group">

                                    <div class="custom-file">

                                        <input type="file" class="custom-file-input" id="fileJadwal" name="fileJadwal">

                                        <label class="custom-file-label" for="fileJadwal">Pilih Jadwal Sidang Skripsi</label>

                                    </div>

                                </div>



                                <a href="<?= base_url("Kaprodi/downloadFormatJadwalSidangSkripsi") ?>"><small>Download Format Jadwal Sidang Skripsi</small></a>

                            </div>

                            <div class="modal-footer">

                                <button type="submit" class="btn btn-primary">Simpan</button>

                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>


<!-- Modal Ubah Jadwal Sidang Skripsi -->
    <div class="modal fade" id="ubahJadwal" tabindex="-1" role="dialog" aria-labelledby="ubahJadwalLabel" aria-hidden="true">

        <div class="modal-dialog" role="document">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title" id="ubahJadwalLabel">Ubah Jadwal Sidang Skripsi</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                    <span aria-hidden="true">&times;</span>

                    </button>

                </div>

                <form action="" method="post" id="formUbahJadwal">

                    <div class="modal-body">

                        <div class="row">

                            <div class="col-lg-12">

                                <div class="form-group">

                                    <label for="mahasiswa">Mahasiswa</label>

                                    <input class="form-control" id="mahasiswa" name="mahasiswa" type="text" disabled>

                                </div>

                            </div>



                            <div class="col-lg-12">

                                <div class="form-group">

                                    <label for="judul">Judul</label>

                                    <textarea rows="2" class="form-control" id="judul" name="judul" disabled></textarea>

                                </div>

                            </div>



                            <div class="col-lg-12">

                                <div class="form-group">

                                    <label for="bidang">Bidang</label>

                                    <input type="text" class="form-control" id="bidang" name="bidang" disabled>

                                </div>

                            </div>


                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="ketua_sidang_edit">Ketua Komisi Sidang</label>
                                    <select name="ketua_sidang" id="ketua_sidang_edit" class="form-control" required>
                                        <option value="" selected disabled> - Pilih Ketua Sidang - </option>
                                        <?php foreach($dosen as $d) : ?>
                                            <option value="<?= $d['id'] ?>"><?= $d['inisial'] . " | " . $d['nama'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>


                            <div class="col-lg-6">

                                <div class="form-group">

                                    <label for="pembimbing1">Pembimbing Ilmu 1</label>

                                    <input type="text" class="form-control" id="pembimbing1" name="pembimbing1" disabled>

                                </div>

                            </div>



                            <div class="col-lg-6">

                                <div class="form-group">

                                    <label for="pembimbing2">Pembimbing Ilmu 2</label>

                                    <input type="text" class="form-control" id="pembimbing2" name="pembimbing2" disabled>

                                </div>

                            </div>



                            <div class="col-lg-6">

                                <div class="form-group">

                                    <label for="pembimbing_agama">Pembimbing Agama</label>

                                    <input type="text" class="form-control" id="pembimbing_agama" name="pembimbing_agama" disabled>

                                </div>

                            </div>



                            <div class="col-lg-6">

                                <div class="form-group">

                                    <label for="dosen_penguji">Dosen Penguji</label>

                                    <select name="dosen_penguji" id="dosen_penguji" class="form-control">

                                        <option value="none" selected disabled> - Pilih Dosen Penguji - </option>

                                        <?php foreach($dosen as $d) : ?>

                                            <option value="<?= $d['id'] ?>"><?= $d['inisial'] . " | " . $d['nama'] ?></option>

                                        <?php endforeach; ?>

                                    </select>

                                </div>

                            </div>

                            
                            <div class="col-lg-12">

                                <div class="form-group">

                                    <label for="klien">Klien</label>

                                    <select name="klien" id="klien" class="form-control">

                                        <option value="none" selected disabled> - Pilih Dosen Penguji - </option>

                                        <?php foreach($listKlien as $lk) : ?>

                                            <option value="<?= $lk['id'] ?>"><?= $lk['inisial'] . " | " . $lk['instansi'] ?></option>

                                        <?php endforeach; ?>

                                    </select>

                                </div>

                            </div>

                            <div class="col-lg-12">

                                <div class="form-group">

                                    <label for="ruangan">Ruangan</label>

                                    <input type="text" class="form-control" id="ruangan" name="ruangan" placeholder="contoh: Ruangan MDI">

                                </div>

                            </div>



                            <div class="col-lg-6">

                                <div class="form-group">

                                    <label for="tanggal">Tanggal Sidang</label>

                                    <input type="text" class="form-control" id="tanggal" name="tanggal">

                                </div>

                            </div>

                            <div class="col-lg-6">

                                <div class="form-group">

                                    <label for="jam">Waktu Sidang</label>

                                    <!-- <input type="text" class="form-control" id="jam" name="jam" placeholder="format: (hh:mm)"> -->
                                    <input type="time" name="jam" id="jam" class="form-control" required>
                                </div>

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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

    <script src="<?= base_url('assets/vendor/daterangepicker/daterangepicker.js') ?>"></script>
    <link rel="stylesheet" href="<?= base_url('assets/vendor/daterangepicker/daterangepicker.css') ?>">

    <script>
        $(document).ready(function() {
            // Hapus value "Invalid date" jika ada
            $('input[name="tanggal"]').val(''); 

            // Inisialisasi Datepicker
            $('input[name="tanggal"]').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                autoUpdateInput: false, // Biar tidak langsung isi otomatis (mencegah salah format)
                locale: {
                    format: 'YYYY-MM-DD',
                    cancelLabel: 'Clear'
                }
            });

            // Event saat tanggal dipilih
            $('input[name="tanggal"]').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD'));
            });

            // Event saat tombol clear diklik
            $('input[name="tanggal"]').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });
        });
    </script>

    <script src="<?= base_url('assets/js/kaprodi/sidangSkripsi.js') ?>"></script>
    <script>
        $(document).ready(function() {
            
            // --- 1. INISIALISASI DATEPICKER UNTUK MODAL EDIT JUGA ---
            // Penting: Input tanggal di modal ubah harus punya ID atau Class yang unik
            // Anggap saja di modal ubah kamu tambahkan id="tanggal_edit" atau kita pakai selector spesifik
            $('#ubahJadwal input[name="tanggal"]').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                autoUpdateInput: false, 
                locale: {
                    format: 'YYYY-MM-DD',
                    cancelLabel: 'Clear'
                }
            });
            
            $('#ubahJadwal input[name="tanggal"]').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD'));
            });

            // --- 2. LOGIC TOMBOL EDIT ---
            $(document).off('click', '.btn-edit'); 

            // Buat event handler baru
            $(document).on('click', '.btn-edit', function(e) {
                e.preventDefault();

                // 1. Ambil data dari tombol (Data Attributes)
                var id = $(this).data('id');
                var npm = $(this).data('npm');
                var nama = $(this).data('nama');
                var judul = $(this).data('judul');
                
                // [BARU] Ambil data pembimbing & bidang
                var bidang = $(this).data('bidang');
                var p1 = $(this).data('pembimbing1');
                var p2 = $(this).data('pembimbing2');
                var pa = $(this).data('pembimbing_agama');

                var tanggal = $(this).data('tanggal'); 
                var jam = $(this).data('jam');
                var ruangan = $(this).data('ruangan');
                var penguji = $(this).data('penguji');
                var ketua = $(this).data('ketua');
                var klien = $(this).data('klien');

                // 2. Tentukan ID Modal Edit
                var modal = $('#ubahJadwal'); 

                // 3. Isi Data Visual (Yang disabled)
                modal.find('input[name="mahasiswa"]').val(npm + ' | ' + nama);
                modal.find('textarea[name="judul"]').val(judul);
                
                // [BARU] Isi Field Bidang & Pembimbing
                modal.find('input[name="bidang"]').val(bidang);
                modal.find('input[name="pembimbing1"]').val(p1);
                modal.find('input[name="pembimbing2"]').val(p2);
                modal.find('input[name="pembimbing_agama"]').val(pa);

                // 4. Isi Data Form yang bisa diedit
                modal.find('input[name="jam"]').val(jam);
                modal.find('input[name="ruangan"]').val(ruangan);
                
                // Isi Dropdown (Select)
                modal.find('select[name="dosen_penguji"]').val(penguji).trigger('change');
                modal.find('select[name="ketua_sidang"]').val(ketua).trigger('change');
                modal.find('select[name="klien"]').val(klien).trigger('change');

                // 5. Handle Datepicker
                var inputTanggalEdit = modal.find('input[name="tanggal"]');
                inputTanggalEdit.val(tanggal); 

                if (inputTanggalEdit.data('daterangepicker')) {
                    inputTanggalEdit.data('daterangepicker').setStartDate(tanggal);
                    inputTanggalEdit.data('daterangepicker').setEndDate(tanggal);
                }

                // 6. Update Action URL
                var form = modal.find('form');
                var actionUrl = "<?= base_url('Kaprodi/updateJadwalSidangSkripsi') ?>/" + id;
                form.attr('action', actionUrl);

                // 7. Tampilkan Modal
                modal.modal('show');
            });
        });
    </script>
<?= $this->endSection(); ?>