<?= $this->extend("layout/template"); ?>

<?= $this->section("content"); ?>

<?php if (session()->getFlashdata("message")) : ?>
    <div id="flashdata" data-open="true">
        <p id="icon" hidden><?= session()->getFlashdata("message")["icon"]; ?></p>
        <p id="title" hidden><?= session()->getFlashdata("message")["title"]; ?></p>
        <p id="text" hidden><?= session()->getFlashdata("message")["text"]; ?></p>
    </div>
<?php endif; ?>

<h1 class="h3 mb-4 text-gray-800"><?= $title ?></h1>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <?= $title ?>
    </div>
    
    <div class="card-body">
        
        <div class="row mb-4 align-items-center">
            
            <div class="col-md-6">
                <button class="btn btn-primary" data-toggle="modal" data-target="#modalUnggah">
                    <i class="fas fa-upload fa-sm text-white-50"></i> Unggah Hard Cover
                </button>
            </div>

            <div class="col-md-6 d-flex justify-content-end">
                <form class="form-inline" action="<?= base_url("TenagaKependidikan/unggahHardcover") ?>" method="get" id="semesterForm">
                    <div class="form-group">
                        <select class="form-control form-control" id="semesterOption" name="semester" onchange="document.getElementById('semesterForm').submit();">
                            <?php foreach($semesterList as $semester) : ?>
                                <option value="<?= $semester['kode'] ?>" <?= ($currentSemester['kode'] == $semester['kode']) ? 'selected' : '' ?>>
                                    <?= $semester['nama'] ?> <?= ($semester['status'] == 'AKTIF') ? '(Aktif)' : '' ?>
                                </option>
                            <?php endforeach;?>
                        </select>
                    </div>
                </form>
            </div>
            
        </div>

        <div class="table-responsive">
            <table class="table table-bordered" id="dataTableUnggah" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NPM</th>
                        <th>Nama</th>
                        <th>Prodi</th>
                        <th>Judul Skripsi</th>
                        <th>Status</th>
                        <th>File</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($pengajuan as $p) : ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $p['npm'] ?></td>
                            <td><?= $p['nama_mhs'] ?></td>
                            <td><?= $p['nama_prodi'] ?></td>
                            <td><?= $p['judul'] ?></td>
                            <td>
                                <?php if($p['status'] == 'DISETUJUI'): ?>
                                    <span class="badge badge-success">DISETUJUI</span>
                                <?php elseif($p['status'] == 'DITOLAK'): ?>
                                    <span class="badge badge-danger">DITOLAK</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">TERTUNDA</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= base_url('TenagaKependidikan/detailVerifikasiHardcover/' . $p['id']) ?>" class="btn btn-primary btn-sm" title="Detail">
                                    <i class="fas fa-info-circle"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalUnggah" tabindex="-1" role="dialog" aria-labelledby="modalUnggahLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalUnggahLabel">Unggah Hard Cover Mahasiswa</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('TenagaKependidikan/simpanHardcoverManual') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    
                    <div class="form-group">
                        <label class="font-weight-bold">Program Studi Mahasiswa:</label><br>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="prodiTI" name="filter_prodi" class="custom-control-input" value="1" checked onchange="filterMahasiswa()">
                            <label class="custom-control-label" for="prodiTI">Teknik Informatika (TI)</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="prodiPdSI" name="filter_prodi" class="custom-control-input" value="2" onchange="filterMahasiswa()">
                            <label class="custom-control-label" for="prodiPdSI">Perpustakaan & Sains Informasi (PdSI)</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="pilih_mahasiswa">Nama Mahasiswa <small>(Hanya yang sudah lulus skripsi)</small></label>
                        <select class="form-control" id="pilih_mahasiswa" required onchange="isiOtomatis()">
                            <option value="">-- Pilih Mahasiswa --</option>
                            <?php foreach($listMahasiswa as $mhs): ?>
                                <option value="<?= $mhs['id_skripsi'] ?>" 
                                        data-npm="<?= $mhs['npm'] ?>" 
                                        data-judul="<?= htmlspecialchars($mhs['judul']) ?>"
                                        data-prodi="<?= $mhs['id_prodi'] ?>">
                                    <?= $mhs['nama'] ?> (<?= $mhs['npm'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="id_skripsi" id="input_id_skripsi">
                    </div>

                    <div class="form-group">
                        <label>NPM</label>
                        <input type="text" class="form-control" id="input_npm" readonly placeholder="Otomatis terisi...">
                    </div>

                    <div class="form-group">
                        <label>Judul Skripsi</label>
                        <textarea class="form-control" id="input_judul" rows="2" readonly placeholder="Otomatis terisi..."></textarea>
                    </div>

                    <hr>

                    <div class="form-group">
                        <label>File Hard Cover (PDF)</label>
                        <input type="file" class="form-control-file" name="file_hardcover" accept=".pdf" required>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan & Setujui</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section("scripts"); ?>
<script>
    $(document).ready(function() {
        // Hapus inisialisasi lama biar bersih
        if ($.fn.DataTable.isDataTable('#dataTableUnggah')) {
            $('#dataTableUnggah').DataTable().destroy();
        }

        // Konfigurasi DataTables LENGKAP (Export & Show Entries)
        var table = $('#dataTableUnggah').DataTable({
            dom: "<'row mt-3'<'col-sm-12 col-md-4'B><'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Excel',
                    className: 'btn btn-dark', // Warna Hijau
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6] // Kolom yang diexport
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: 'PDF',
                    className: 'btn btn-dark',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                },
                {
                    extend: 'colvis',
                    text: 'Column Visibility',
                    className: 'btn btn-dark'
                }
            ],
            
            lengthMenu: [
                [10, 25, 50, -1],
                ['10 rows', '25 rows', '50 rows', 'Show all']
            ],

            language: {
                search: "Search:",
                zeroRecords: "Data tidak ditemukan",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                },
                buttons: {
                    pageLength: {
                        _: "Show %d rows",
                        '-1': "Show all rows"
                    }
                }
            }
        });
        
        // Perbaiki styling tombol agar rapi
        table.buttons().container().appendTo('#dataTableUnggah_wrapper .col-md-6:eq(0)');

        // Jalankan filter modal pertama kali
        filterMahasiswa();
    });

    // --- LOGIC MODAL ---

    function isiOtomatis() {
        var selectedOption = $('#pilih_mahasiswa').find(':selected');
        var idSkripsi = selectedOption.val();
        var npm = selectedOption.data('npm');
        var judul = selectedOption.data('judul');

        $('#input_id_skripsi').val(idSkripsi);
        $('#input_npm').val(npm);
        $('#input_judul').val(judul);
    }

    function filterMahasiswa() {
        var prodiID = $('input[name="filter_prodi"]:checked').val();
        
        $('#pilih_mahasiswa').val('');
        $('#input_npm').val('');
        $('#input_judul').val('');

        $('#pilih_mahasiswa option').each(function() {
            var dataProdi = $(this).data('prodi');
            if ($(this).val() == "") return; 

            if (dataProdi == prodiID) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }
</script>
<?= $this->endSection(); ?>