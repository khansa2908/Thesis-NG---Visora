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
        Jadwal Sidang Skripsi
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-5"></div>
            <div class="col-lg-7 d-flex justify-content-end align-items-center">
                <form class="form-inline" action="<?= base_url("TenagaKependidikan/sidangSkripsi") ?>" method="get" id="semesterForm">
                    <div class="form-group">
                        <select class="form-control" id="semesterOption" name="semester">
                            <?php foreach ($semesterList as $semester) : ?>
                                <?php if ($semester['status'] == 'AKTIF'): ?>
                                    <option value="<?= $semester['kode'] ?>" selected><?= $semester['nama'] ?> (Aktif)</option>
                                <?php else: ?>
                                    <option value="<?= $semester['kode'] ?>"><?= $semester['nama'] ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive mt-3">
            <table class="table table-bordered" id="jadwalSidangSkripsi" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NPM</th>
                        <th>Nama</th>
                        <th>Program Studi</th>
                        <th>Judul</th>
                        <th>Bidang</th>
                        <th>Tanggal Sidang</th>
                        <th>Ruangan</th>
                        <th>Penguji</th>
                        <th>Pemb. Ilmu 1</th>
                        <th>Pemb. Ilmu 2</th>
                        <th>Pemb. Agama</th>
                        <th>Status</th>
                        <th class="text-center" style="min-width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $counter = 1; ?>
                    <?php foreach ($sidangSkripsi as $ss) : ?>
                        <tr>
                            <td><?= $counter ?></td>
                            <td class="npm"><?= $ss['npm'] ?></td>
                            <td class="nama"><?= $ss['nama_mahasiswa'] ?></td>
                            <td data-toggle="tooltip" title="<?= $ss['nama_prodi'] ?>"><?= $ss['inisial_prodi'] ?></td>
                            <td class="judul"><?= $ss['judul'] ?></td>
                            <td data-toggle="tooltip" title="<?= $ss['nama_bidang'] ?>" class="bidang"><?= $ss['inisial_bidang'] ?></td>
                            <td class="tanggal" style="min-width: 8vw"><?= date_format(date_create($ss['tanggal']), 'd-m-Y H:i') ?> WIB</td>

                            <?php if (preg_match('#^https?://#i', $ss['ruangan']) === 1): ?>
                                <td class="ruangan"><a href="<?= $ss['ruangan'] ?>" target="_blank">Klik disini!</a></td>
                            <?php else: ?>
                                <td class="ruangan"><?= $ss['ruangan'] ?></td>
                            <?php endif; ?>

                            <td data-toggle="tooltip" title="<?= $ss['nama_penguji'] ?>" class="penguji" data-id="<?= $ss['dosen_penguji'] ?>"><?= $ss['inisial_penguji'] ?></td>
                            <td data-toggle="tooltip" title="<?= $ss['nama_pembimbing1'] ?>" class="pembimbing1"><?= $ss['inisial_pembimbing1'] ?></td>

                            <?php if ($ss['nama_pembimbing2'] == null): ?>
                                <td>-</td>
                            <?php else: ?>
                                <td data-toggle="tooltip" title="<?= $ss['nama_pembimbing2'] ?>" class="pembimbing2"><?= $ss['inisial_pembimbing2'] ?></td>
                            <?php endif; ?>

                            <td data-toggle="tooltip" title="<?= $ss['nama_pembimbing_agama'] ?>" class="pembimbing_agama"><?= $ss['inisial_pembimbing_agama'] ?></td>
                            <td><?= $ss['status'] == null ? "TERTUNDA" : $ss['status'] ?></td>

                            <td class="text-center">

                                <button type="button" class="btn btn-secondary btn-sm btn-print-undangan"
                                    data-toggle="modal"
                                    data-target="#modalCetakUndangan"
                                    data-id="<?= $ss['id']; ?>"
                                    title="Cetak Undangan">
                                    <i class="fas fa-print"></i>
                                </button>

                                <button type="button" class="btn btn-primary btn-sm btn-ubah-jadwal"
                                    data-id="<?= $ss['id']; ?>"
                                    title="Ubah Jadwal">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>

                                <form action="<?= base_url("TenagaKependidikan/deleteJadwalSidang/" . $ss['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus jadwal ini?');">
                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus Jadwal">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>

                            </td>
                        </tr>
                        <?php $counter++; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCetakUndangan" tabindex="-1" role="dialog" aria-labelledby="modalLabelUndangan" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabelUndangan">Cetak Undangan Sidang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('TenagaKependidikan/cetakUndanganSidang'); ?>" method="post" target="_blank">
                <div class="modal-body">
                    <input type="hidden" name="id_jadwal" id="input_id_jadwal_undangan">

                    <div class="form-group">
                        <label>Nomor Surat</label>
                        <input type="text" name="no_surat" class="form-control" placeholder="Contoh: 013/Prodi TI/PP.20.10/VII/2025" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Surat</label>
                        <input type="date" name="tanggal_surat" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-print"></i> Cetak PDF</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalUbahJadwal" tabindex="-1" role="dialog" aria-labelledby="modalLabelUbah" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabelUbah">Ubah Jadwal Sidang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('TenagaKependidikan/updateJadwalSidang'); ?>" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id_jadwal" id="edit_id_jadwal">

                    <div class="form-group">
                        <label>Tanggal Sidang</label>
                        <input type="date" name="tanggal" id="edit_tanggal" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Jam Mulai</label>
                        <input type="time" name="jam" id="edit_jam" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Ruangan</label>
                        <input type="text" name="ruangan" id="edit_ruangan" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>



<?= $this->endSection(); ?>


<?= $this->section("scripts"); ?>

<script src="<?= base_url("assets/js/tenagakependidikan/sidangSkripsi.js"); ?>" defer></script>

<script>
    $(document).ready(function() {


        $('body').on('click', '.btn-print-undangan', function() {
            var id = $(this).data('id');
            $('#input_id_jadwal_undangan').val(id);
        });


        $('body').on('click', '.btn-cetak-ba-sidang', function() {
            const id = $(this).data('id');
            const nomor = $(this).data('nomor');

            $('#id_sidang_input').val(id);
            $('#nomor_ba_sidang_input').val(nomor);
        });


        $('body').on('click', '.btn-ubah-jadwal', function() {
            var id = $(this).data('id');

            // Ambil data dari database via AJAX untuk mengisi form modal
            $.ajax({
                url: "<?= base_url('TenagaKependidikan/getJsonJadwalSidang') ?>/" + id,
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    if (data) {
                        $('#edit_id_jadwal').val(data.id);

                        if (data.tanggal) {
                            var splitData = data.tanggal.split(' ');
                            $('#edit_tanggal').val(splitData[0]); // Ambil YYYY-MM-DD
                            $('#edit_jam').val(splitData[1].substring(0, 5)); // Ambil HH:MM
                        }

                        // Isi Ruangan
                        $('#edit_ruangan').val(data.ruangan);

                        // Munculkan Modal Ubah
                        $('#modalUbahJadwal').modal('show');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("Error fetching data: ", textStatus, errorThrown);
                    alert('Gagal mengambil data jadwal. Pastikan function getJsonJadwalSidang ada di Controller.');
                }
            });
        });

    });
</script>

<?= $this->endSection(); ?>