
<?= $this->extend("layout/template"); ?>

<?= $this->section("content"); ?>

<?php $message = session()->getFlashdata("message"); ?>

<?php if ($message) : ?>
    <div id="flashdata" data-open="true">
        <p id="icon" hidden><?= esc($message["icon"] ?? "info") ?></p>
        <p id="title" hidden><?= esc($message["title"] ?? "") ?></p>
        <p id="text" hidden><?= esc($message["text"] ?? "") ?></p>
    </div>
<?php endif; ?>

<h1 class="h3 mb-2 text-gray-800"><?= esc($title) ?></h1>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <?= esc($title) ?>
    </div>

    <div class="card-body">

        <div class="d-flex justify-content-start mb-3">
            <button type="button"
                    class="btn btn-primary"
                    data-toggle="modal"
                    data-target="#tambahMataKuliah">
                Tambahkan Mata Kuliah
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered"
                   id="tableMataKuliah"
                   width="100%"
                   cellspacing="0">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Mata Kuliah</th>
                        <th>Nama Mata Kuliah</th>
                        <th>Sifat Mata Kuliah</th>
                        <th>Kurikulum</th>
                        <th>Semester</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($mataKuliah)) : ?>

                        <?php foreach ($mataKuliah as $index => $mk) : ?>

                            <tr>
                                <td><?= $index + 1 ?></td>

                                <td>
                                    <?= esc($mk['kode_matkul'] ?? '-') ?>
                                </td>

                                <td>
                                    <?= esc($mk['nama_matkul'] ?? '-') ?>
                                </td>

                                <td>
                                    <?= esc($mk['kategori'] ?? '-') ?>
                                </td>

                                <td>
                                    <?php if (!empty($mk['nama_kurikulum'])) : ?>
                                        <?= esc($mk['nama_kurikulum']) ?>
                                        (<?= esc($mk['tahun_berlaku'] ?? '-') ?>)
                                    <?php else : ?>
                                        -
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?= esc($mk['semester_kurikulum'] ?? '-') ?>
                                </td>

                                <td class="text-nowrap">

                                    <!-- Tombol Edit -->
                                    <button type="button"
                                            class="btn btn-primary btn-sm"
                                            title="Edit"
                                            data-toggle="modal"
                                            data-target="#editMataKuliah<?= $index ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <!-- Tombol Hapus -->
                                    <form action="<?= base_url('Kaprodi/hapusMataKuliah') ?>"
                                          method="post"
                                          class="d-inline form-hapus-matkul">

                                        <?= csrf_field() ?>

                                        <input type="hidden"
                                               name="kode_matkul"
                                               value="<?= esc($mk['kode_matkul'] ?? '') ?>">

                                        <button type="submit"
                                                class="btn btn-danger btn-sm"
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>

                                </td>
                            </tr>

                        <?php endforeach; ?>

                    <?php else : ?>

                        <tr>
                            <td colspan="7" class="text-center">
                                Belum ada data mata kuliah.
                            </td>
                        </tr>

                    <?php endif; ?>
                </tbody>

            </table>
        </div>

    </div>
</div>


<!-- ========================================= -->
<!-- MODAL TAMBAH MATA KULIAH                  -->
<!-- ========================================= -->

<div class="modal fade"
     id="tambahMataKuliah"
     tabindex="-1"
     role="dialog"
     aria-labelledby="tambahMataKuliahLabel"
     aria-hidden="true">

    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="tambahMataKuliahLabel">
                    Tambah Mata Kuliah
                </h5>

                <button type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="formTambahMataKuliah"
                  method="post"
                  action="<?= base_url('Kaprodi/simpanMataKuliah') ?>">

                <?= csrf_field() ?>

                <div class="modal-body">

                    <div class="form-group">
                        <label for="kodeMataKuliah">
                            Kode Mata Kuliah
                        </label>

                        <input type="text"
                               class="form-control"
                               id="kodeMataKuliah"
                               name="kode_matkul"
                               maxlength="20"
                               placeholder="Contoh: IT201"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="namaMataKuliah">
                            Nama Mata Kuliah
                        </label>

                        <input type="text"
                               class="form-control"
                               id="namaMataKuliah"
                               name="nama_matkul"
                               maxlength="150"
                               placeholder="Masukkan nama mata kuliah"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="kurikulumTambah">
                            Kurikulum
                        </label>

                        <input type="text"
                               class="form-control"
                               id="kurikulumTambah"
                               name="nama_kurikulum"
                               maxlength="120"
                               placeholder="Contoh: Kurikulum 2024"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="kategoriTambah">
                            Sifat Mata Kuliah
                        </label>

                        <select class="form-control"
                                id="kategoriTambah"
                                name="kategori"
                                required>

                            <option value="">
                                - Pilih Sifat Mata Kuliah -
                            </option>

                            <option value="Wajib">Wajib</option>
                            <option value="Pilihan">Pilihan</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="semesterTambah">
                            Semester
                        </label>

                        <input type="number"
                               class="form-control"
                               id="semesterTambah"
                               name="semester_kurikulum"
                               min="1"
                               max="8"
                               step="1"
                               placeholder="Masukkan semester (1–8)"
                               required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit" class="btn btn-primary">
                        Simpan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>


<!-- ========================================= -->
<!-- MODAL EDIT MATA KULIAH                    -->
<!-- ========================================= -->

<?php foreach (($mataKuliah ?? []) as $index => $mk) : ?>

    <div class="modal fade"
         id="editMataKuliah<?= $index ?>"
         tabindex="-1"
         role="dialog"
         aria-labelledby="editMataKuliahLabel<?= $index ?>"
         aria-hidden="true">

        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title"
                        id="editMataKuliahLabel<?= $index ?>">
                        Edit Mata Kuliah
                    </h5>

                    <button type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form method="post"
                      action="<?= base_url('Kaprodi/updateMataKuliah') ?>">

                    <?= csrf_field() ?>

                    <div class="modal-body">

                        <div class="form-group">
                            <label for="kodeEdit<?= $index ?>">
                                Kode Mata Kuliah
                            </label>

                            <input type="text"
                                   class="form-control"
                                   id="kodeEdit<?= $index ?>"
                                   name="kode_matkul"
                                   value="<?= esc($mk['kode_matkul'] ?? '') ?>"
                                   readonly>
                        </div>

                        <div class="form-group">
                            <label for="namaEdit<?= $index ?>">
                                Nama Mata Kuliah
                            </label>

                            <input type="text"
                                   class="form-control"
                                   id="namaEdit<?= $index ?>"
                                   name="nama_matkul"
                                   maxlength="150"
                                   value="<?= esc($mk['nama_matkul'] ?? '') ?>"
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="kurikulumEdit<?= $index ?>">
                                Kurikulum
                            </label>

                            <input type="text"
                                   class="form-control"
                                   id="kurikulumEdit<?= $index ?>"
                                   name="nama_kurikulum"
                                   maxlength="120"
                                   value="<?= esc($mk['nama_kurikulum'] ?? '') ?>"
                                   placeholder="Contoh: Kurikulum 2024"
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="kategoriEdit<?= $index ?>">
                                Sifat Mata Kuliah
                            </label>

                            <select class="form-control"
                                    id="kategoriEdit<?= $index ?>"
                                    name="kategori"
                                    required>

                                <option value="Wajib"
                                    <?= ($mk['kategori'] ?? '') === 'Wajib' ? 'selected' : '' ?>>
                                    Wajib
                                </option>

                                <option value="Pilihan"
                                    <?= ($mk['kategori'] ?? '') === 'Pilihan' ? 'selected' : '' ?>>
                                    Pilihan
                                </option>

                            </select>
                        </div>

                        <div class="form-group">
                            <label for="semesterEdit<?= $index ?>">
                                Semester
                            </label>

                            <input type="number"
                                   class="form-control"
                                   id="semesterEdit<?= $index ?>"
                                   name="semester_kurikulum"
                                   min="1"
                                   max="8"
                                   step="1"
                                   value="<?= esc($mk['semester_kurikulum'] ?? '') ?>"
                                   placeholder="Masukkan semester (1–8)"
                                   required>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-secondary"
                                data-dismiss="modal">
                            Batal
                        </button>

                        <button type="submit" class="btn btn-primary">
                            Simpan Perubahan
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

<?php endforeach; ?>


<?= $this->endSection(); ?>


<!-- ========================================= -->
<!-- JAVASCRIPT KONFIRMASI HAPUS               -->
<!-- ========================================= -->

<?= $this->section("scripts"); ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).on('submit', '.form-hapus-matkul', function (e) {
    e.preventDefault();

    const form = this;

    Swal.fire({
        icon: 'warning',
        title: 'Apakah anda ingin menghapus mata kuliah ini?',
        text: 'Mata kuliah yang sudah dihapus tidak dapat dikembalikan.',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
});
</script>

<?= $this->endSection(); ?>
