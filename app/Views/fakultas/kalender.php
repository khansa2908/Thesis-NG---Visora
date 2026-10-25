<?= $this->extend("layout/template"); ?>



<?= $this->section("content"); ?>

<h1 class="h3 mb-2 text-gray-800"><?= $title ?></h1>

<?php if (session()->getFlashdata("message")) : ?>

    <div id="flashdata" data-open="true">

        <p id="icon" hidden><?= session()->getFlashdata("message")["icon"]; ?></p>

        <p id="title" hidden><?= session()->getFlashdata("message")["title"]; ?></p>

        <p id="text" hidden><?= session()->getFlashdata("message")["text"]; ?></p>

    </div>

<?php endif; ?>

<div class="card shadow mb-4">

        <div class="card-header py-3">

            <?= $title ?>

        </div>

        <div class="card-body">

            <div class="d-flex justify-content-start mb-3">

                <button class="btn btn-primary" data-toggle="modal" data-target="#tambahKegiatan">Tambahkan Kegiatan</button>

            </div>

            <div class="table-responsive">

                <table class="table table-bordered" id="kegiatanSkripsi" width="100%" cellspacing="0">

                    <thead>

                        <tr>

                            <th>No</th>

                            <th>Nama Kegiatan</th>

                            <th>Tanggal Kegiatan</th>

                            <th>Aksi</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php 

                        $counter = 1;

                        foreach($kegiatanSkripsi as $k):
                            // Gabungkan nama prodi jadi string (Contoh: "Teknik Informatika, PdSI")
                            $prodiString = implode(", ", $k['prodi_list']);
                            // Ubah array ID prodi jadi JSON string agar bisa dibaca JS (Contoh: "[1,2]")
                            $prodiIdsJson = json_encode($k['prodi_ids']);
                        ?>

                            <tr>

                                <td><?= $counter ?></td>

                                <td class="nama-kegiatan">
                                    <strong><?= $k["nama_kegiatan"]?></strong><br>
                                    <small class="text-muted">Prodi: <?= $prodiString ?></small>
                                </td>

                                <td class="tanggal-kegiatan">
                                    <?= date_format(date_create($k["tanggal_mulai"]), "d M Y H:i") . " - " . date_format(date_create($k["tanggal_selesai"]), "d M Y H:i")?>
                                </td>

                                <td>

                                    <button class="btn btn-primary ubah-kegiatan" 
                                        data-toggle="modal" 
                                        data-target="#ubahKegiatan"
                                        data-id="<?= $k['id_utama'] ?>" 
                                        data-nama="<?= $k['nama_kegiatan'] ?>"
                                        data-jenis="<?= $k['jenis_kegiatan'] ?>"
                                        data-prodi-ids='<?= $prodiIdsJson ?>' 
                                        data-mulai="<?= $k['tanggal_mulai'] ?>"
                                        data-selesai="<?= $k['tanggal_selesai'] ?>"
                                        title="Ubah">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>

                                    <form action="<?= base_url("fakultas/deleteKalender/" . $k['id_utama']) ?>" method="post" class="d-inline" id="formHapusKegiatan">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Hapus"><i class="fa fa-trash"></i></button>

                                    </form>

                                </td>

                            </tr>    

                        <?php 

                        $counter = $counter + 1;

                        endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>



    <!-- modal tambah kegiatan -->

    <div class="modal fade" id="tambahKegiatan" tabindex="-1" role="dialog" aria-labelledby="tambahKegiatanLabel" aria-hidden="true">

        <div class="modal-dialog" role="document">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title" id="tambahKegiatanLabel">Tambah Kegiatan</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                    <span aria-hidden="true">&times;</span>

                    </button>

                </div>

                <form id="formTambahKegiatan" method="post" action="<?= base_url("fakultas/insertKalender")?>">

                    <?= csrf_field() ?>

                    <div class="modal-body">

                        <div class="form-group">

                            <label for="namaKegiatan">Nama Kegiatan</label>

                            <input type="text" class="form-control" id="namaKegiatan" name="namaKegiatan" required>

                        </div>
                        
                        <!-- Radio button jenis kegiatan -->
                        <div class="form-group card p-3 mb-3 bg-light">
                            <label class="d-block font-weight-bold">Kegiatan:</label>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="js1" name="jenis_kegiatan" value="sempro" class="custom-control-input">
                                <label class="custom-control-label" for="js1">Sempro</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="js2" name="jenis_kegiatan" value="prasidang" class="custom-control-input">
                                <label class="custom-control-label" for="js2">Pra-Sidang</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="js3" name="jenis_kegiatan" value="sidang" class="custom-control-input">
                                <label class="custom-control-label" for="js3">Sidang Akhir</label>
                            </div>
                            <button type="button" class="btn btn-xs btn-danger ml-2" onclick="clearRadio()">Clear</button>
                        </div>

                        <!-- Checkbox Prodi yang dipilih -->
                        <div class="form-group card p-3 mb-3 bg-light">
                            <label class="d-block font-weight-bold">Berlaku Untuk Prodi:</label>
                            <?php foreach($listProdi as $prodi): ?>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="prodi[]" value="<?= $prodi['id'] ?>" id="p_<?= $prodi['id'] ?>" checked>
                                    <label class="form-check-label" for="p_<?= $prodi['id'] ?>"><?= $prodi['nama'] ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- <div class="form-group">

                            <label for="durasiKegiatan">Durasi Kegiatan</label>

                            <input type="text" class="form-control" id="durasiKegiatan" name="durasiKegiatan" required>

                        </div> -->

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mulai</label>
                                    <input type="datetime-local" name="tanggalMulai" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Selesai</label>
                                    <input type="datetime-local" name="tanggalSelesai" class="form-control" required>
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


<!-- JANGAN LUPA PERUBAHAN DISINI -->
    <!-- modal ubah kegiatan -->

    <div class="modal fade" id="ubahKegiatan" tabindex="-1" role="dialog" aria-labelledby="ubahKegiatanLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ubahKegiatanLabel">Ubah Kegiatan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form id="formUbahKegiatan" method="post" action="<?= base_url("fakultas/updateKalender") ?>">
                <?= csrf_field() ?>
                
                <input type="hidden" id="edit_id" name="id">

                <div class="modal-body">
                    
                    <div class="form-group">
                        <label for="edit_namaKegiatan">Nama Kegiatan</label>
                        <input type="text" class="form-control" id="edit_namaKegiatan" name="namaKegiatan" required>
                    </div>

                    <div class="form-group card p-3 mb-3 bg-light">
                        <label class="d-block font-weight-bold">Kegiatan:</label>
                        
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="edit_js1" name="jenis_kegiatan" value="sempro" class="custom-control-input">
                            <label class="custom-control-label" for="edit_js1">Sempro</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="edit_js2" name="jenis_kegiatan" value="prasidang" class="custom-control-input">
                            <label class="custom-control-label" for="edit_js2">Pra-Sidang</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="edit_js3" name="jenis_kegiatan" value="sidang" class="custom-control-input">
                            <label class="custom-control-label" for="edit_js3">Sidang Akhir</label>
                        </div>

                        <button type="button" class="btn btn-xs btn-danger ml-2" onclick="clearEditInput('jenis_kegiatan')">Clear</button>
                    </div>

                    <div class="form-group card p-3 mb-3 bg-light">
                        <label class="d-block font-weight-bold">Berlaku Untuk Prodi:</label>
                        <?php foreach($listProdi as $prodi): ?>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input edit-check-prodi" 
                                       type="checkbox" 
                                       name="prodi[]" 
                                       value="<?= $prodi['id'] ?>" 
                                       id="edit_p_<?= $prodi['id'] ?>">
                                <label class="form-check-label" for="edit_p_<?= $prodi['id'] ?>"><?= $prodi['nama'] ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Mulai</label>
                                <input type="datetime-local" id="edit_tanggalMulai" name="tanggalMulai" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Selesai</label>
                                <input type="datetime-local" id="edit_tanggalSelesai" name="tanggalSelesai" class="form-control" required>
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

    <script src="<?= base_url("assets/js/fakultas/kalender.js");?>"></script>

    <script>
        function clearRadio() {
            let radios = document.getElementsByName('jenis_kegiatan');
            for(let i=0; i<radios.length; i++) radios[i].checked = false;
        }
    </script>

    <script>
        function clearEditInput(inputName) {
            $('#formUbahKegiatan input[name="' + inputName + '"]').prop('checked', false);
        }

        $(document).ready(function() {
            
            $('.ubah-kegiatan').on('click', function() {
                // 1. Ambil Data
                const id = $(this).data('id');
                const nama = $(this).data('nama');
                const jenis = $(this).data('jenis');
                const prodiIds = $(this).data('prodi-ids'); // Ini Array [1, 2] atau [1]
                let mulai = $(this).data('mulai');
                let selesai = $(this).data('selesai');

                // 2. Format Tanggal
                if(mulai) mulai = mulai.replace(" ", "T").substring(0, 16);
                if(selesai) selesai = selesai.replace(" ", "T").substring(0, 16);

                // 3. Isi Input Teks
                $('#edit_id').val(id);
                $('#edit_namaKegiatan').val(nama);
                $('#edit_tanggalMulai').val(mulai);
                $('#edit_tanggalSelesai').val(selesai);

                // 4. Set Radio (Jenis)
                clearEditInput('jenis_kegiatan');
                $('#formUbahKegiatan input[name="jenis_kegiatan"][value="' + jenis + '"]').prop('checked', true);

                // 5. Set Checkbox (Prodi) - LOGIC BARU
                // Reset dulu
                $('.edit-check-prodi').prop('checked', false);
                
                // Loop array prodiIds dan centang yang sesuai
                if (Array.isArray(prodiIds)) {
                    prodiIds.forEach(function(prodiId) {
                        $('#edit_p_' + prodiId).prop('checked', true);
                    });
                } else {
                    // Fallback jika cuma single value (bukan array)
                    $('#edit_p_' + prodiIds).prop('checked', true);
                }
            });
        });
    </script>

<?= $this->endSection(); ?>