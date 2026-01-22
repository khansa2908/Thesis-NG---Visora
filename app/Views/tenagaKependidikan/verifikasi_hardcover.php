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
        
        <div class="row mb-4">
            <div class="col-lg-5"></div>
            <div class="col-lg-7 d-flex justify-content-end align-items-center">
                <form class="form-inline" action="<?= base_url("TenagaKependidikan/verifikasiHardcover") ?>" method="get" id="semesterForm">
                    <div class="form-group mr-2">
                        <select class="form-control" id="semesterOption" name="semester" onchange="document.getElementById('semesterForm').submit();">
                            <?php foreach($semesterList as $semester) : ?>
                                <?php 
                                    $isSelected = '';
                                    if ($currentSemester && $currentSemester['kode'] == $semester['kode']) {
                                        $isSelected = 'selected';
                                    } elseif (!$currentSemester && $semester['status'] == 'AKTIF') {
                                        $isSelected = 'selected';
                                    }
                                ?>
                                <option value="<?= $semester['kode'] ?>" <?= $isSelected ?>>
                                    <?= $semester['nama'] ?> <?= ($semester['status'] == 'AKTIF') ? '(Aktif)' : '' ?>
                                </option>
                            <?php endforeach;?>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered" id="dataTableHardcover" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NPM</th>
                        <th>Nama</th>
                        <th>Program Studi</th>
                        <th>Judul</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                        <th>Aksi</th>
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
                            <td><?= date('d-m-Y', strtotime($p['tanggal_pengajuan'])) ?></td>
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

<?= $this->endSection(); ?>


<?= $this->section("scripts"); ?>
<script>
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('#dataTableHardcover')) {
            $('#dataTableHardcover').DataTable().destroy();
        }

        var table = $('#dataTableHardcover').DataTable({
            dom: "<'row mt-3'<'col-sm-12 col-md-4'B><'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            buttons: [
                { extend: 'excelHtml5', text: 'Excel', className: 'btn btn-dark', exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] } },
                { extend: 'pdfHtml5', text: 'PDF', className: 'btn btn-dark', orientation: 'landscape', pageSize: 'A4', exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] } },
                { extend: 'colvis', text: 'Column Visibility', className: 'btn btn-dark' }
            ],
            lengthMenu: [[10, 25, 50, -1], ['10 rows', '25 rows', '50 rows', 'Show all']],
            language: {
                search: "Search:",
                zeroRecords: "Data tidak ditemukan",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)",
                paginate: { first: "First", last: "Last", next: "Next", previous: "Previous" },
                buttons: { pageLength: { _: "Show %d rows", '-1': "Show all rows" } }
            }
        });
        table.buttons().container().appendTo('#dataTableHardcover_wrapper .col-md-6:eq(0)');
    });
</script>
<?= $this->endSection(); ?>