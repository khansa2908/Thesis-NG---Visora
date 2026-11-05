<?= $this->extend("layout/template"); ?>

<?= $this->section("content"); ?>

<?php
$disabled = ($mahasiswa['status_persetujuan_skripsi'] ?? '') === 'Disetujui';

$mataKuliah = $mataKuliah ?? [];

// Mata kuliah wajib untuk daftar yang belum lulus.
$mkWajib = array_filter($mataKuliah, function ($mk) {
    return ($mk['kategori'] ?? '') === 'Wajib';
});

// Pilihan yang sebelumnya tersimpan.
$mkSedangDipilih = array_filter(
    array_map('trim', explode(',', $mahasiswa['mk_sedang_diambil'] ?? ''))
);

$nilaiWajibTersimpan = trim($mahasiswa['mk_akan_diambil'] ?? '');

$sudahLulusSemua = $nilaiWajibTersimpan === 'Sudah lulus semua';

$mkWajibDipilih = $sudahLulusSemua
    ? []
    : array_filter(array_map('trim', explode(',', $nilaiWajibTersimpan)));
?>

<!-- NOTIFIKASI -->
<?php if (session()->getFlashdata("message")) : ?>
    <div id="flashdata" data-open="true">
        <p id="icon" hidden><?= session()->getFlashdata("message")["icon"]; ?></p>
        <p id="title" hidden><?= session()->getFlashdata("message")["title"]; ?></p>
        <p id="text" hidden><?= session()->getFlashdata("message")["text"]; ?></p>
    </div>
<?php endif; ?>


<!-- JUDUL HALAMAN -->
<h1 class="h3 mb-2 text-gray-800">
    <?= esc($title) ?>
</h1>


<!-- STATUS PENGAJUAN -->
<div class="row">
    <div class="col-lg-12">

        <?php if (
            $mahasiswa['file_khs'] != null &&
            $mahasiswa['file_krs'] != null &&
            $mahasiswa['file_persetujuan_skripsi'] != null &&
            $mahasiswa['sks_lulus'] != null &&
            $mahasiswa['pembimbing_akademik'] != null &&
            $mahasiswa['mk_sedang_diambil'] != null &&
            $mahasiswa['mk_akan_diambil'] != null &&
            $mahasiswa['status_persetujuan_skripsi'] == 'Disetujui'
        ): ?>

            <div class="alert alert-success" role="alert">
                <small>
                    <strong>Pengajuan penulisan skripsi</strong> Anda
                    <strong>disetujui</strong>. Silakan kumpulkan
                    <strong>proposal</strong> Anda pada
                    <a href="<?= base_url("mahasiswa/proposal") ?>">menu berikut</a>.
                </small>
            </div>

        <?php elseif (
            $mahasiswa['file_khs'] != null &&
            $mahasiswa['file_krs'] != null &&
            $mahasiswa['file_persetujuan_skripsi'] != null &&
            $mahasiswa['sks_lulus'] != null &&
            $mahasiswa['pembimbing_akademik'] != null &&
            $mahasiswa['mk_sedang_diambil'] != null &&
            $mahasiswa['mk_akan_diambil'] != null &&
            $mahasiswa['status_persetujuan_skripsi'] == 'Ditolak'
        ): ?>

            <div class="alert alert-danger" role="alert">
                <small>
                    <strong>Pengajuan penulisan skripsi</strong> Anda
                    <strong>ditolak</strong>. Silakan perbaiki dan kirim ulang pengajuan Anda.
                </small>
            </div>

        <?php elseif (
            $mahasiswa['file_khs'] != null &&
            $mahasiswa['file_krs'] != null &&
            $mahasiswa['file_persetujuan_skripsi'] != null &&
            $mahasiswa['sks_lulus'] != null &&
            $mahasiswa['pembimbing_akademik'] != null &&
            $mahasiswa['mk_sedang_diambil'] != null &&
            $mahasiswa['mk_akan_diambil'] != null &&
            $mahasiswa['status_persetujuan_skripsi'] == null
        ): ?>

            <div class="alert alert-info" role="alert">
                <small>
                    <strong>Pengajuan Penulisan Skripsi</strong> Anda sedang
                    <strong>diproses</strong>.
                </small>
            </div>

        <?php else: ?>

            <div class="alert alert-danger" role="alert">
                <small>
                    Lengkapi data berikut sebagai syarat
                    <strong>wajib</strong> untuk dapat mengikuti kegiatan skripsi.
                </small>
            </div>

        <?php endif; ?>

    </div>
</div>


<!-- FORM PENGAJUAN -->
<div class="card shadow mb-4">

    <div class="card-header py-3">
        Persyaratan Pengajuan Penulisan Skripsi
    </div>

    <div class="card-body">

        <form
            action="<?= base_url("mahasiswa/insertPengajuanPenulisanSkripsi/" . $mahasiswa['npm']) ?>"
            method="post"
            id="formDataPersetujuanSkripsi"
            enctype="multipart/form-data"
        >

            <?= csrf_field() ?>


            <!-- BARIS PERTAMA: TIGA KOLOM -->
            <div class="row">

                <div class="col-lg-4">
                    <div class="form-group">
                        <label for="sks_lulus">SKS yang Sudah Lulus</label>

                        <input
                            type="text"
                            class="form-control"
                            id="sks_lulus"
                            name="sks_lulus"
                            value="<?= $mahasiswa['sks_lulus'] ?>"
                            <?= ($mahasiswa['status_persetujuan_skripsi'] == 'Disetujui') ? "disabled" : "" ?>
                            >
                        </div>
                </div>


                <!-- DOSEN PEMBIMBING -->
                <div class="col-lg-4">
                    <div class="form-group">

                        <label for="pembimbing_akademik">
                            Dosen Pembimbing Akademik
                        </label>

                        <select
                            class="form-control"
                            name="pembimbing_akademik"
                            id="pembimbing_akademik"
                            <?= $disabled ? 'disabled' : '' ?>
                        >

                            <option
                                value=""
                                disabled
                                <?= empty($mahasiswa['pembimbing_akademik']) ? 'selected' : '' ?>
                            >
                                Pilih Dosen
                            </option>

                            <?php foreach ($dosen as $d): ?>

                                <option
                                    value="<?= esc($d['id']) ?>"
                                    <?= ($mahasiswa['pembimbing_akademik'] ?? '') == $d['id'] ? 'selected' : '' ?>
                                >
                                    <?= esc($d['inisial'] . ' | ' . $d['nama']) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>
                </div>


                <!-- JENIS TUGAS AKHIR -->
                <div class="col-lg-4">
                    <div class="form-group">

                        <label for="jenis_ta">
                            Jenis Tugas Akhir
                        </label>

                        <select
                            class="form-control"
                            name="jenis_ta"
                            id="jenis_ta"
                            <?= $disabled ? 'disabled' : '' ?>
                        >

                            <option value="" selected disabled>
                             Pilih TA
                            </option>

                            <?php foreach ($jenisTugasAkhir as $jenis): ?>
    <option
        value="<?= esc($jenis['id_jenis_tugas_akhir']) ?>"
        <?= ($mahasiswa['id_jenis_tugas_akhir'] ?? '') == $jenis['id_jenis_tugas_akhir'] ? 'selected' : '' ?>
    >
        <?= esc($jenis['nama_jenis_tugas_akhir']) ?>
    </option>
<?php endforeach; ?>
                        </select>
                        <small class="text-danger d-none" id="errorJenisTA">
                            Kolom ini harus diisi.
                        </small>

                    </div>
                </div>

            </div>


            <!-- BARIS KEDUA: DUA DROPDOWN MATA KULIAH -->
            <div class="row mt-3">

                <!-- KIRI: MATA KULIAH WAJIB BELUM LULUS -->
                <div class="col-lg-6">

                    <div class="form-group">

                        <label>
                            Mata Kuliah Wajib yang Belum Lulus
                        </label>

                        <div class="dropdown-mk" id="dropdownWajib">

                            <button
                                type="button"
                                class="form-control dropdown-mk-button"
                                <?= $disabled || $sudahLulusSemua ? 'disabled' : '' ?>
                            >

                                <span class="dropdown-mk-text">
                                    <?= $sudahLulusSemua ? 'Sudah lulus semua' : 'Pilih Mata Kuliah' ?>
                                </span>

                                <i class="fas fa-chevron-down"></i>

                            </button>


                            <div class="dropdown-mk-content">

                                <input
                                    type="text"
                                    class="form-control dropdown-mk-search"
                                    placeholder="Cari mata kuliah..."
                                >

                                <div class="dropdown-mk-list">

                                    <?php if (!empty($mkWajib)): ?>

                                        <?php foreach ($mkWajib as $mk): ?>

                                            <label class="dropdown-mk-option">

                                                <input
                                                    type="checkbox"
                                                    name="mk_akan_diambil[]"
                                                    value="<?= esc($mk['nama_matkul']) ?>"
                                                    <?= in_array($mk['nama_matkul'], $mkWajibDipilih) ? 'checked' : '' ?>
                                                    <?= $disabled || $sudahLulusSemua ? 'disabled' : '' ?>
                                                >

                                                <span>
                                                    <?= esc($mk['nama_matkul']) ?>
                                                </span>

                                            </label>

                                        <?php endforeach; ?>

                                    <?php else: ?>

                                        <p class="text-muted small">
                                            Belum ada mata kuliah wajib.
                                        </p>

                                    <?php endif; ?>

                                </div>


                                <div class="dropdown-mk-footer">

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-light dropdown-mk-close"
                                    >
                                        Selesai
                                    </button>

                                </div>

                            </div>

                        </div>
                        <small class="text-danger d-none" id="errorMKWajib">
                            Kolom ini harus diisi.
                        </small>


                        <!-- CHECKBOX SUDAH LULUS SEMUA -->
                        <div class="custom-control custom-checkbox mt-3">

                            <input
                                type="checkbox"
                                class="custom-control-input"
                                id="sudahLulusSemua"
                                <?= $sudahLulusSemua ? 'checked' : '' ?>
                                <?= $disabled ? 'disabled' : '' ?>
                            >

                            <label
                                class="custom-control-label"
                                for="sudahLulusSemua"
                            >
                                Sudah lulus semua
                            </label>

                        </div>

                        <small class="text-muted">
                            Pilih mata kuliah wajib yang belum Anda selesaikan.
                        </small>

                    </div>

                </div>


                <!-- KANAN: SEMUA MATA KULIAH YANG SEDANG DIAMBIL -->
                <div class="col-lg-6">

                    <div class="form-group">

                        <label>
                            Mata Kuliah yang Sedang Berjalan
                        </label>

                        <div class="dropdown-mk" id="dropdownSedang">

                            <button
                                type="button"
                                class="form-control dropdown-mk-button"
                                <?= $disabled ? 'disabled' : '' ?>
                            >

                                <span class="dropdown-mk-text">
                                    Pilih Mata Kuliah
                                </span>

                                <i class="fas fa-chevron-down"></i>

                            </button>


                            <div class="dropdown-mk-content">

                                <input
                                    type="text"
                                    class="form-control dropdown-mk-search"
                                    placeholder="Cari mata kuliah..."
                                >

                                <div class="dropdown-mk-list">

                                    <?php if (!empty($mataKuliah)): ?>

                                        <?php foreach ($mataKuliah as $mk): ?>

                                            <label class="dropdown-mk-option">

                                                <input
                                                    type="checkbox"
                                                    name="mk_sedang_diambil[]"
                                                    value="<?= esc($mk['nama_matkul']) ?>"
                                                    <?= in_array($mk['nama_matkul'], $mkSedangDipilih) ? 'checked' : '' ?>
                                                    <?= $disabled ? 'disabled' : '' ?>
                                                >

                                                <span>
                                                   <?= esc($mk['nama_matkul']) ?>
                                            </span>

                                            </label>

                                        <?php endforeach; ?>

                                    <?php else: ?>

                                        <p class="text-muted small">
                                            Belum ada mata kuliah untuk program studi Anda.
                                        </p>

                                    <?php endif; ?>

                                </div>


                                <div class="dropdown-mk-footer">

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-light dropdown-mk-close"
                                    >
                                        Selesai
                                    </button>

                                </div>

                            </div>

                        </div>

                        <small class="text-danger d-none" id="errorMKSedang">
                            Kolom ini harus diisi.
                        </small>

                        <small class="text-muted">
                            Anda dapat memilih lebih dari satu mata kuliah.
                        </small>

                    </div>

                </div>

            </div>


            <!-- DOKUMEN PERSYARATAN -->
            <div class="row mt-4">

                <div class="col-lg-12">

                    <h6 class="font-weight-bold mb-3">
                        Dokumen Persyaratan
                    </h6>

                </div>


                <!-- FILE KHS -->
                <div class="col-lg-12 my-2">

                    <label>
                        File Kartu Hasil Studi (KHS)
                    </label>

                    <?php if ($mahasiswa['file_khs'] == null): ?>

                        <small class="text-muted d-block">
                            Unggah KHS dalam format PDF dengan ukuran maksimal 10 MB.
                        </small>

                        <div class="input-group">

                            <div class="custom-file">

                                <input type="file" class="custom-file-input"
                                id="inputKhs" name="inputKhs">

                                <label
                                    class="custom-file-label"
                                    for="inputKhs"
                                >
                                    Pilih File Kartu Hasil Studi
                                </label>

                            </div>

                        </div>

                    <?php else: ?>

                        <div class="input-group">

                            <input
                                type="text"
                                class="form-control"
                                value="<?= esc($mahasiswa['file_khs']) ?>"
                                readonly
                            >

                            <div class="input-group-append">

                                <a
                                    role="button"
                                    class="btn btn-outline-primary"
                                    id="tinjauKhs"
                                    data-npm="<?= esc($mahasiswa['npm']) ?>"
                                >
                                    Tinjau
                                </a>

                            </div>

                        </div>

                    <?php endif; ?>

                </div>


                <!-- FILE KRS -->
                <div class="col-lg-12 my-2">

                    <label>
                        File Kartu Rencana Studi (KRS)
                    </label>

                    <?php if ($mahasiswa['file_krs'] == null): ?>

                        <small class="text-muted d-block">
                            Unggah KRS dalam format PDF dengan ukuran maksimal 10 MB.
                        </small>

                        <div class="input-group">

                            <div class="custom-file">

                                <input type="file" class="custom-file-input"
                                id="inputKrs" name="inputKrs">
                                <label
                                    class="custom-file-label"
                                    for="inputKrs"
                                >
                                    Pilih File Kartu Rencana Studi
                                </label>

                            </div>

                        </div>

                    <?php else: ?>

                        <div class="input-group">

                            <input
                                type="text"
                                class="form-control"
                                value="<?= esc($mahasiswa['file_krs']) ?>"
                                readonly
                            >

                            <div class="input-group-append">

                                <a
                                    role="button"
                                    class="btn btn-outline-primary"
                                    id="tinjauKrs"
                                    data-npm="<?= esc($mahasiswa['npm']) ?>"
                                >
                                    Tinjau
                                </a>

                            </div>

                        </div>

                    <?php endif; ?>

                </div>


                <!-- FILE PERSETUJUAN -->
                <div class="col-lg-12 my-2">

                    <label>
                        File Persetujuan Tugas Akhir
                    </label>

                    <?php if ($mahasiswa['file_persetujuan_skripsi'] == null): ?>

                        <small class="text-muted d-block">
                            Unggah persetujuan penyusunan tugas akhir dalam format PDF
                            dengan ukuran maksimal 10 MB.
                        </small>

                        <div class="input-group">

                            <div class="custom-file">

                                <input type="file" class="custom-file-input"
                                id="inputPersetujuanSkripsi" name="inputPersetujuanSkripsi">

                                <label
                                    class="custom-file-label"
                                    for="inputPersetujuanSkripsi"
                                >
                                    Pilih File Persetujuan Tugas Akhir
                                </label>

                            </div>

                        </div>

                    <?php else: ?>

                        <div class="input-group">

                            <input
                                type="text"
                                class="form-control"
                                value="<?= esc($mahasiswa['file_persetujuan_skripsi']) ?>"
                                readonly
                            >

                            <div class="input-group-append">

                                <a
                                    role="button"
                                    class="btn btn-outline-primary"
                                    id="tinjauPersetujuan"
                                    data-npm="<?= esc($mahasiswa['npm']) ?>"
                                >
                                    Tinjau
                                </a>

                            </div>

                        </div>

                    <?php endif; ?>

                </div>


                <!-- INFORMASI DOKUMEN -->
                <div class="col-lg-12 my-2">

                    <small class="text-primary">
                        <i class="fas fa-info-circle"></i>
                        Pastikan dokumen dipindai jelas menggunakan CamScanner
                        dengan posisi berdiri (portrait).
                    </small>

                </div>


                <!-- TOMBOL SIMPAN -->
                <?php if (!$disabled): ?>

                    <div class="col-lg-12 d-flex justify-content-end my-2">

                        <button
                            class="btn btn-primary"
                            type="submit"
                        >
                            Kirim Pengajuan
                        </button>

                    </div>

                <?php endif; ?>

            </div>

        </form>

    </div>

</div>


<!-- MODAL PREVIEW FILE -->
<div
    class="modal fade"
    id="filePreview"
    tabindex="-1"
    role="dialog"
    aria-labelledby="filePreviewLabel"
    aria-hidden="true"
>

    <div class="modal-dialog modal-lg" role="document">

        <div class="modal-content">

            <div class="modal-header">

                <h5
                    class="modal-title"
                    id="filePreviewLabel"
                ></h5>

                <button
                    type="button"
                    class="close"
                    data-dismiss="modal"
                    aria-label="Close"
                >
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>


            <div class="modal-body">

                <div class="row">

                    <div class="col-lg-12">

                        <div class="form-group">

                            <div
                                class="embed-responsive embed-responsive-16by9"
                                id="previewContainer"
                            >

                                <iframe
                                    class="embed-responsive-item"
                                    src=""
                                ></iframe>

                            </div>

                        </div>

                    </div>

                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal"
                    >
                        Kembali
                    </button>


                    <form
                        action=""
                        id="formUnduh"
                        method="post"
                    >

                        <button
                            class="btn btn-primary"
                            type="submit"
                        >
                            Unduh
                        </button>

                    </form>


                    <?php if (!$disabled): ?>

                        <form
                            action=""
                            id="formHapus"
                            method="post"
                        >

                            <button
                                class="btn btn-danger"
                                type="submit"
                            >
                                Hapus
                            </button>

                        </form>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

</div>


<?= $this->endSection(); ?>


<!-- JAVASCRIPT -->
<?= $this->section("scripts"); ?>

<script src="<?= base_url("assets/vendor/daterangepicker/daterangepicker.js") ?>"></script>

<script src="<?= base_url("assets/js/mahasiswa/pengajuanPenulisanSkripsi.js") ?>"></script>


<script>
document.addEventListener('DOMContentLoaded', function () {

    // =========================================
    // DROPDOWN MATA KULIAH
    // =========================================

    document.querySelectorAll('.dropdown-mk').forEach(function (dropdown) {

        const button = dropdown.querySelector('.dropdown-mk-button');
        const search = dropdown.querySelector('.dropdown-mk-search');
        const closeButton = dropdown.querySelector('.dropdown-mk-close');

        if (!button) return;


        // Membuka dropdown.
        button.addEventListener('click', function (e) {

            e.stopPropagation();

            document.querySelectorAll('.dropdown-mk').forEach(function (other) {

                if (other !== dropdown) {
                    other.classList.remove('active');
                }

            });

            dropdown.classList.toggle('active');

            if (search && dropdown.classList.contains('active')) {
                search.focus();
            }

        });


        // Menutup dropdown.
        if (closeButton) {

            closeButton.addEventListener('click', function () {
                dropdown.classList.remove('active');
            });

        }


        // Pencarian mata kuliah.
        if (search) {

            search.addEventListener('input', function () {

                const keyword = this.value.toLowerCase();

                dropdown.querySelectorAll('.dropdown-mk-option').forEach(function (option) {

                    const nama = option.textContent.toLowerCase();

                    option.style.display = nama.includes(keyword) ? 'flex' : 'none';

                });

            });

        }


        // Perubahan pilihan.
        dropdown.querySelectorAll('input[type="checkbox"]').forEach(function (checkbox) {

            checkbox.addEventListener('change', function () {
                updateDropdownText(dropdown);
            });

        });

        updateDropdownText(dropdown);

    });


    // =========================================
    // UPDATE TEKS DROPDOWN
    // =========================================

    function updateDropdownText(dropdown) {

        const selected = Array.from(
            dropdown.querySelectorAll('.dropdown-mk-option input:checked')
        ).map(function (checkbox) {
            return checkbox.value;
        });

        const text = dropdown.querySelector('.dropdown-mk-text');

        if (!text) return;

        // Pertahankan teks jika mahasiswa memilih opsi sudah lulus semua.
        const checkboxLulusSemua = document.getElementById('sudahLulusSemua');

        if (dropdown.id === 'dropdownWajib' && checkboxLulusSemua && checkboxLulusSemua.checked) {
            text.textContent = 'Sudah lulus semua';
        } else if (selected.length === 0) {
            text.textContent = 'Pilih Mata Kuliah';
        } else {
            // Tampilkan nama mata kuliah yang dipilih, bukan hanya jumlahnya.
            text.textContent = selected.join(', ');
            text.title = selected.join(', ');
        }

    }
    // BINDING STATE JENIS TUGAS AKHIR

    const jenisTA = document.getElementById('jenis_ta');

    let selectedJenisTA = jenisTA ? jenisTA.value : '';

    if (jenisTA) {
        jenisTA.addEventListener('change', function () {
        selectedJenisTA = this.value;
        console.log('Jenis TA dipilih:', selectedJenisTA);
        });
    }
    


    // =========================================
    // TUTUP DROPDOWN KETIKA KLIK DI LUAR
    // =========================================

    document.addEventListener('click', function (e) {

        if (!e.target.closest('.dropdown-mk')) {

            document.querySelectorAll('.dropdown-mk').forEach(function (dropdown) {
                dropdown.classList.remove('active');
            });

        }

    });


    // =========================================
    // CHECKBOX SUDAH LULUS SEMUA
    // =========================================

    const checkboxLulus = document.getElementById('sudahLulusSemua');

    const dropdownWajib = document.getElementById('dropdownWajib');

    if (checkboxLulus && dropdownWajib) {

        checkboxLulus.addEventListener('change', function () {

            const daftarCheckbox = dropdownWajib.querySelectorAll(
                '.dropdown-mk-option input[type="checkbox"]'
            );

            daftarCheckbox.forEach(function (checkbox) {

                checkbox.checked = false;
                checkbox.disabled = checkboxLulus.checked;

            });


            const button = dropdownWajib.querySelector('.dropdown-mk-button');

            const text = dropdownWajib.querySelector('.dropdown-mk-text');

            button.disabled = checkboxLulus.checked;

            if (checkboxLulus.checked) {

                text.textContent = 'Sudah lulus semua';

                dropdownWajib.classList.remove('active');

            } else {

                text.textContent = 'Pilih Mata Kuliah';

            }

        });

    }


    // =========================================
    // LABEL FILE UPLOAD
    // =========================================

    document.querySelectorAll('.custom-file-input').forEach(function (input) {

        input.addEventListener('change', function () {

            const fileName = this.files[0]
                ? this.files[0].name
                : 'Pilih File';

            const label = this.nextElementSibling;

            if (label) {
                label.textContent = fileName;
            }

        });

    });

    // Hilangkan pesan Jenis TA setelah pengguna memilih.
    if (jenisTA && errorJenisTA) {
        jenisTA.addEventListener('change', function () {
            if (this.value) errorJenisTA.classList.add('d-none');
        });
    }

    // Hilangkan pesan Mata Kuliah Wajib setelah ada pilihan.
    document.querySelectorAll('#dropdownWajib .dropdown-mk-option input').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            if (getWajibDipilih().length > 0 || (checkboxLulus && checkboxLulus.checked)) {
                errorMKWajib.classList.add('d-none');
            }
        });
    });

    // Pilihan "Sudah lulus semua" juga memenuhi Mata Kuliah Wajib.
    if (checkboxLulus) {
        checkboxLulus.addEventListener('change', function () {
            if (this.checked) errorMKWajib.classList.add('d-none');
        });
    }

    // Hilangkan pesan Mata Kuliah Sedang Berjalan setelah ada pilihan.
    document.querySelectorAll('#dropdownSedang .dropdown-mk-option input').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            if (getSedangDipilih().length > 0) errorMKSedang.classList.add('d-none');
        });
    });

});
</script>


<?= $this->endSection(); ?>