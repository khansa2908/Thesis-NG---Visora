<?php //dd(session()->get("user_session")) 
?>

<?php
// Safe roles helper to avoid errors when session is not present
$session = session()->get('user_session');
$roles = [];
if ($session) {
    if (isset($session['roles']) && is_array($session['roles'])) {
        $roles = array_map('strtolower', $session['roles']);
    } elseif (isset($session['role'])) {
        $roles = [strtolower($session['role'])];
    }
}
?>

<?php
$currentPath = strtolower(trim(service('uri')->getPath(), '/'));

// Menentukan apakah sebuah URL sedang aktif
$isActive = function ($path) use ($currentPath) {
    return $currentPath === strtolower(trim($path, '/'));
};
?>

<!-- Sidebar -->

<ul class="navbar-nav bg-gradient-dark sidebar sidebar-dark accordion" id="accordionSidebar">



    <!-- Sidebar - Brand -->

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">

        <div class="sidebar-brand-icon">

            <img src="<?= base_url("assets/img/skripsi.png") ?>" alt="Logo" style="height: 3.5rem">

        </div>

        <div class="sidebar-brand-text mx-3">THESIS</div>

    </a>



    <!-- Divider -->

    <hr class="sidebar-divider">



    <div class="sidebar-heading">
        Beranda
    </div>

    <?php
    // 1. ADMIN (Biarkan Sendirian ke Admin Dashboard)
    // Hapus 'tendik' dari sini
    if (in_array('admin', $roles)) :
    ?>
        <li class="nav-item">
            <a class="nav-link py-2" href="<?= base_url("admin/dashboard") ?>">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dasbor Admin</span>
            </a>
        </li>
    <?php endif; ?>

    <?php
    // 2. FAKULTAS & TENDIK (Digabung ke Fakultas Dashboard)
    // Tambahkan 'OR in_array tendik' di sini
    if (in_array('fakultas', $roles) || in_array('tendik', $roles)) :
    ?>
        <li class="nav-item">
            <a class="nav-link py-2" href="<?= base_url("fakultas/dashboard") ?>">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dasbor</span>
            </a>
        </li>
    <?php endif; ?>

    <?php
    // 3. KAPRODI (Untuk Akun TI dan PdSI)
    // Cek apakah user punya role 'kaprodi'
    if (in_array('kaprodi', $roles)) :
    ?>
        <li class="nav-item">
            <a class="nav-link py-2" href="<?= base_url("kaprodi/dashboard") ?>">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dasbor Prodi</span>
            </a>
        </li>
    <?php endif; ?>

    <?php
    // DOSEN
    if (in_array('dosen', $roles) || in_array('dospem eksternal', $roles)) :
    ?>
        <li class="nav-item">
            <a class="nav-link py-2" href="<?= base_url("dosen/index") ?>">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard Dosen</span>
            </a>
        </li>
    <?php endif; ?>


    <li class="nav-item">
        <a class="nav-link py-2" href="<?= base_url("home/index") ?>">

            <i class="fas fa-book"></i>

            <span>Repositori</span></a>

    </li>

    <li class="nav-item ">

        <a class="nav-link py-2" href="<?= base_url("home/kalender") ?>">

            <i class="fas fa-calendar-alt"></i>

            <span>Kalender Skripsi</span></a>

    </li>

    <li class="nav-item ">

        <a class="nav-link py-2" href="<?= base_url("home/penelitian") ?>">

            <i class="fas fa-flask"></i>

            <span>Penelitian Dosen</span></a>

    </li>


    <li class="nav-item ">

        <a class="nav-link py-2" href="<?= base_url("home/seminarPrasidang") ?>">

            <i class="fas fa-calendar-alt"></i>

            <span>Seminar Prasidang FTI</span></a>

    </li>

    <!-- ... (Kode menu Seminar Prasidang FTI di atas sini) ... -->
    </li>

    <!-- ============================================================ -->
    <!--  MENU KHUSUS ADMIN: KELOLA PENGGUNA                          -->
    <!-- ============================================================ -->
    <?php
    // Cek Session Role: Menu ini hanya muncul untuk ADMIN
    $session = session()->get('user_session');
    $isAdminRole = false;
    if ($session) {
        if (isset($session['role']) && $session['role'] === 'Admin') {
            $isAdminRole = true;
        }
        if (!$isAdminRole && isset($session['roles']) && is_array($session['roles'])) {
            $lowerRoles = array_map('strtolower', $session['roles']);
            if (in_array('admin', $lowerRoles)) {
                $isAdminRole = true;
            }
        }
    }
    if ($isAdminRole) :
    ?>

        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAdminKelola" aria-expanded="true" aria-controls="collapseAdminKelola">
                <i class="fas fa-fw fa-users-cog"></i>
                <span>Kelola Pengguna</span>
            </a>

            <div id="collapseAdminKelola" class="collapse" aria-labelledby="headingAdmin" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <!-- Admin: manage users (khusus Admin) -->
                    <a class="collapse-item" href="<?= base_url('admin/users'); ?>">Kelola Pengguna</a>

                </div>
            </div>

        </li>

    <?php endif; ?>


    <!-- ⬇️ Menu Sumber Daya ada di bawah sini -->


    <!-- Divider -->



    <!-- Heading -->

    <?php if (in_array("mahasiswa", $roles)) : ?>

        <hr class="sidebar-divider">



        <div class="sidebar-heading">

            Mahasiswa

        </div>



        <li class="nav-item">

            <a class="nav-link collapsed py-2" href="#" data-toggle="collapse" data-target="#collapsePengajuan"

                aria-expanded="true" aria-controls="collapsePengajuan">

                <i class="fas fa-fw fa-folder"></i>

                <span>Pengajuan</span>

            </a>

            <div id="collapsePengajuan" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">

                <div class="bg-white py-2 collapse-inner rounded">

                    <a class="collapse-item" href="<?= base_url("Mahasiswa/pengajuanPenulisanSkripsi") ?>">Penulisan Skripsi</a>
                    <a class="collapse-item" href="<?= base_url("Mahasiswa/pengajuanPraSidang") ?>">Seminar Prasidang</a>
                    <a class="collapse-item" href="<?= base_url("Mahasiswa/pengajuanSidangSkripsi") ?>">Sidang Skripsi</a>

                </div>

            </div>

        </li>



        <li class="nav-item">

            <a class="nav-link collapsed py-2" href="#" data-toggle="collapse" data-target="#collapseBerkas"

                aria-expanded="true" aria-controls="collapseBerkas">

                <i class="fas fa-fw fa-folder"></i>

                <span>Berkas</span>

            </a>

            <div id="collapseBerkas" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">

                <div class="bg-white py-2 collapse-inner rounded">

                    <a class="collapse-item" href="<?= base_url("Mahasiswa/proposal") ?>">Proposal</a>

                    <a class="collapse-item" href="<?= base_url("Mahasiswa/skripsi") ?>">Skripsi</a>

                    <a class="collapse-item" href="<?= base_url("Mahasiswa/makalah") ?>">Makalah</a>

                    <a class="collapse-item" href="<?= base_url("Mahasiswa/pengajuanHardcover") ?>">Hard Cover</a>
                </div>

            </div>

        </li>



        <li class="nav-item">

            <a class="nav-link collapsed py-2" href="#" data-toggle="collapse" data-target="#collapseJadwal"

                aria-expanded="true" aria-controls="collapseJadwal">

                <i class="fas fa-fw fa-calendar-alt"></i>

                <span>Jadwal</span>

            </a>

            <div id="collapseJadwal" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">

                <div class="bg-white py-2 collapse-inner rounded">

                    <a class="collapse-item" href="<?= base_url("Mahasiswa/seminarProposal") ?>">Seminar Proposal</a>
                    <a class="collapse-item" href="<?= base_url("Mahasiswa/seminarPrasidang") ?>">Seminar Prasidang</a>
                    <a class="collapse-item" href="<?= base_url("Mahasiswa/sidangSkripsi") ?>">Sidang Skripsi</a>

                </div>

            </div>

        </li>



        <li class="nav-item ">

            <a class="nav-link py-2" href="<?= base_url("Mahasiswa/pembimbing") ?>">

                <i class="fas fa-file-medical"></i>

                <span>Catatan Bimbingan</span></a>

        </li>

    <?php endif; ?>





    <?php if (in_array("fakultas", $roles)) : ?>

        <hr class="sidebar-divider">



        <div class="sidebar-heading">

            Fakultas

        </div>

        <li class="nav-item">
            <a class="nav-link collapsed py-2" href="#" data-toggle="collapse" data-target="#collapseKelola"
                aria-expanded="true" aria-controls="collapseKelola">
                <i class="fas fa-fw fa-cog"></i>
                <span>Kelola</span>
            </a>

            <div id="collapseKelola" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="<?= base_url("Fakultas/semester") ?>">Semester</a>
                    <a class="collapse-item" href="<?= base_url("Fakultas/dosen") ?>">Dosen</a>
                    <a class="collapse-item" href="<?= base_url("Fakultas/kalender") ?>">Kalender</a>
                </div>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed py-2" href="#" data-toggle="collapse" data-target="#collapseReport"
                aria-expanded="true" aria-controls="collapseReport">
                <i class="fas fa-fw fa-chart-bar"></i>
                <span>Laporan</span>
            </a>

            <div id="collapseReport" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="<?= base_url("Fakultas/proposal") ?>">Proposal</a>
                    <a class="collapse-item" href="<?= base_url("Fakultas/skripsi") ?>">Skripsi</a>
                    <a class="collapse-item" href="<?= base_url("Fakultas/makalah") ?>">Makalah</a>
                    <a class="collapse-item" href="<?= base_url("Fakultas/pembimbing") ?>">Bimbingan Mahasiswa</a>
                    <a class="collapse-item" href="<?= base_url("Fakultas/rekapDosenPembimbing") ?>">Dosen Pembimbing</a>
                </div>
            </div>
        </li>
    <?php endif; ?>



    <?php if (in_array("kaprodi", $roles)) : ?>

        <hr class="sidebar-divider">

        <div class="sidebar-heading">
            Kaprodi
        </div>

        <li class="nav-item">
            <a class="nav-link collapsed py-2" href="#" data-toggle="collapse" data-target="#collapseJadwalKaprodi"
                aria-expanded="true" aria-controls="collapseJadwalKaprodi">
                <i class="fas fa-fw fa-cog"></i>
                <span>Kelola</span>
            </a>

            <div id="collapseJadwalKaprodi" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="<?= base_url("Kaprodi/seminarProposal") ?>">Seminar Proposal</a>
                    <a class="collapse-item" href="<?= base_url("Kaprodi/seminarPrasidang") ?>">Seminar Prasidang</a>
                    <a class="collapse-item" href="<?= base_url("Kaprodi/sidangSkripsi") ?>">Sidang Skripsi</a>
                    <a class="collapse-item" href="<?= base_url("Kaprodi/Dosen") ?>">Dosen</a>
                    <a class="collapse-item" href="<?= base_url("Kaprodi/Dosen") ?>">Rubik Penilaian</a>
                    <a class="collapse-item" href="<?= base_url('Kaprodi/mataKuliah') ?>">Mata Kuliah</a>

                </div>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed py-2" href="#" data-toggle="collapse" data-target="#collapseReport"
                aria-expanded="true" aria-controls="collapseReport">
                <i class="fas fa-fw fa-chart-bar"></i>
                <span>Laporan</span>
            </a>

            <div id="collapseReport" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="<?= base_url("Kaprodi/proposal") ?>">Proposal</a>
                    <a class="collapse-item" href="<?= base_url("Kaprodi/skripsi") ?>">Skripsi</a>
                    <a class="collapse-item" href="<?= base_url("Kaprodi/makalah") ?>">Makalah</a>
                    <a class="collapse-item" href="<?= base_url("Kaprodi/pembimbing") ?>">Bimbingan Mahasiswa</a>
                    <a class="collapse-item" href="<?= base_url("Kaprodi/rekapDosenPembimbing") ?>">Dosen Pembimbing</a>
                    <a class="collapse-item" href="<?= base_url("Kaprodi/penilaianSidang") ?>">Penilaian Sidang</a>
                </div>
            </div>
        </li>
    <?php endif; ?>



    <?php if (in_array("dosen", $roles)) : ?>

        <hr class="sidebar-divider">



        <div class="sidebar-heading">

            Dosen

        </div>

        <!-- Nav Item - Pages Collapse Menu -->

        <li class="nav-item">

            <a class="nav-link py-2" href="<?= base_url("dosen/penelitian") ?>">

                <i class="fas fa-fw fa-cog"></i>

                <span>Kelola Penelitian</span>

            </a>

        </li>

        <li class="nav-item">

            <a class="nav-link py-2" href="<?= base_url("dosen/bimbingan") ?>">

                <i class="fas fa-archive"></i>

                <span>Bimbingan</span>

            </a>

        </li>

        <li class="nav-item">

            <a class="nav-link collapsed py-2" href="#" data-toggle="collapse" data-target="#collapsePenguji"

                aria-expanded="true" aria-controls="collapsePenguji">

                <i class="fas fa-fw fa-calendar-alt"></i>

                <span>Jadwal</span>

            </a>

            <div id="collapsePenguji" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">

                <div class="bg-white py-2 collapse-inner rounded">

                    <a class="collapse-item" href="<?= base_url("dosen/pengujiSeminarProposal") ?>">Seminar Proposal</a>
                    <a class="collapse-item" href="<?= base_url("dosen/pengujiSeminarPrasidang") ?>">Seminar Prasidang</a>
                    <a class="collapse-item" href="<?= base_url("dosen/pengujiSidangSkripsi") ?>">Sidang Skripsi</a>

                </div>

            </div>

        </li>

    <?php endif; ?>



    <?php if (in_array("dospem eksternal", $roles)) : ?>

        <hr class="sidebar-divider">



        <div class="sidebar-heading">

            Dosen

        </div>

        <!-- Nav Item - Pages Collapse Menu -->

        <li class="nav-item">

            <a class="nav-link py-2" href="<?= base_url("dosen/bimbingan") ?>">

                <i class="fas fa-archive"></i>

                <span>Bimbingan</span>

            </a>

        </li>

        <li class="nav-item">

            <a class="nav-link collapsed py-2" href="#" data-toggle="collapse" data-target="#collapsePenguji"

                aria-expanded="true" aria-controls="collapsePenguji">

                <i class="fas fa-fw fa-calendar-alt"></i>

                <span>Jadwal</span>

            </a>

            <div id="collapsePenguji" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">

                <div class="bg-white py-2 collapse-inner rounded">

                    <a class="collapse-item" href="<?= base_url("dosen/pengujiSeminarPrasidang") ?>">Seminar Prasidang</a>
                    <a class="collapse-item" href="<?= base_url("dosen/pengujiSidangSkripsi") ?>">Sidang Skripsi</a>

                </div>

            </div>

        </li>

    <?php endif; ?>



    <?php if (in_array("klien", $roles)) : ?>

        <hr class="sidebar-divider">



        <div class="sidebar-heading">

            Klien

        </div>

        <!-- Nav Item - Pages Collapse Menu -->

        <li class="nav-item">

            <a class="nav-link collapsed py-2" href="#" data-toggle="collapse" data-target="#collapsePenguji"

                aria-expanded="true" aria-controls="collapsePenguji">

                <i class="fas fa-fw ffa-calendar-alt"></i>

                <span>Jadwal</span>

            </a>

            <div id="collapsePenguji" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">

                <div class="bg-white py-2 collapse-inner rounded">

                    <a class="collapse-item" href="<?= base_url("dosen/pengujiSidangSkripsi") ?>">Sidang Skripsi</a>

                </div>

            </div>

        </li>

    <?php endif; ?>



    <?php if (in_array("tendik", $roles) || in_array('admin', $roles)) : ?>

        <hr class="sidebar-divider">



        <div class="sidebar-heading">

            Tenaga Kependidikan

        </div>

        <!-- Nav Item - Pages Collapse Menu -->

        <li class="nav-item">

            <a class="nav-link collapsed py-2" href="#" data-toggle="collapse" data-target="#collapsePenguji"

                aria-expanded="true" aria-controls="collapsePenguji">

                <i class="fas fa-fw fa-folder"></i>

                <span>Verifikasi Pengajuan</span>

            </a>

            <div id="collapsePenguji" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">

                <div class="bg-white py-2 collapse-inner rounded">

                    <a class="collapse-item" href="<?= base_url("TenagaKependidikan/pengajuanSkripsi") ?>">Penulisan Skripsi</a>

                    <a class="collapse-item" href="<?= base_url("TenagaKependidikan/pengajuanPrasidang") ?>">Seminar Prasidang</a>

                    <a class="collapse-item" href="<?= base_url("TenagaKependidikan/pengajuanSidangSkripsi") ?>">Sidang Skripsi</a>

                    <a class="collapse-item" href="<?= base_url("TenagaKependidikan/verifikasiHardcover") ?>">Hard Cover</a>
                </div>

            </div>

        </li>

        <li class="nav-item">
            <a class="nav-link collapsed py-2" href="#" data-toggle="collapse" data-target="#collapseJadwalTendik"
                aria-expanded="true" aria-controls="collapseJadwalTendik">
                <i class="fas fa-fw fa-calendar-alt"></i>
                <span>Jadwal</span>
            </a>

            <div id="collapseJadwalTendik" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="<?= base_url("TenagaKependidikan/seminarProposal") ?>">Seminar Proposal</a>
                    <a class="collapse-item" href="<?= base_url("TenagaKependidikan/seminarPrasidang") ?>">Seminar Prasidang</a>
                    <a class="collapse-item" href="<?= base_url("TenagaKependidikan/sidangSkripsi") ?>">Sidang Skripsi</a>
                </div>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed py-2" href="#" data-toggle="collapse" data-target="#collapseKelolaTendik"
                aria-expanded="true" aria-controls="collapseKelolaTendik">
                <i class="fas fa-fw fa-cog"></i>
                <span>Kelola</span>
            </a>

            <div id="collapseKelolaTendik" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="<?= base_url("TenagaKependidikan/dosen") ?>">Dosen</a>
                    <a class="collapse-item" href="<?= base_url("TenagaKependidikan/pembimbing") ?>">Pembimbing</a>
                    <a class="collapse-item" href="<?= base_url("TenagaKependidikan/klien") ?>">Klien</a>
                    <a class="collapse-item" href="<?= base_url("TenagaKependidikan/pembimbingEksternal") ?>">Pembimbing Eksternal</a>
                </div>
            </div>
        </li>

        <li class="nav-item">

            <a class="nav-link py-2" href="<?= base_url("TenagaKependidikan/penilaianSidang") ?>">

                <i class="fas fa-fw fa-cog"></i>

                <span>Penilaian Sidang</span>

            </a>

        </li>

        <li class="nav-item">
            <a class="nav-link py-2" href="<?= base_url("TenagaKependidikan/unggahHardcover") ?>" >
                <i class="fas fa-fw fa-file-upload"></i>
                <span>Unggah Hard Cover</span>
            </a>
        </li>

        <li class="nav-item">

            <a class="nav-link py-2" href="<?= base_url("TenagaKependidikan/persetujuanAkunTamu") ?>">

                <i class="fas fa-fw fa-user-check"></i>

                <span>Persetujuan Akun Tamu</span>

            </a>

        </li>

    <?php endif; ?>



    <!-- Divider -->

    <hr class="sidebar-divider d-none d-md-block my-0">



    <li class="nav-item">

        <a class="nav-link" href="<?= base_url("home/resource") ?>">

            <i class="fas fa-fw fa-cog"></i>

            <span>Sumber Daya</span>

        </a>

    </li>



    <!-- Divider -->

    <hr class="sidebar-divider d-none d-md-block">



    <!-- Sidebar Toggler (Sidebar) -->

    <div class="text-center d-none d-md-inline">

        <button class="rounded-circle border-0" id="sidebarToggle"></button>

    </div>



</ul>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('accordionSidebar');
    if (!sidebar) return;

    function normalizePath(path) {
        return decodeURIComponent(path)
            .replace(/\/+$/, '')
            .toLowerCase();
    }

    const currentPath = normalizePath(window.location.pathname);

    // Tandai tautan yang URL-nya sama dengan halaman saat ini.
    sidebar.querySelectorAll('a[href]').forEach(function (link) {
        const href = link.getAttribute('href');
        if (!href || href === '#') return;

        try {
            const url = new URL(href, window.location.origin);
            if (url.origin !== window.location.origin) return;

            if (normalizePath(url.pathname) === currentPath) {
                if (link.classList.contains('collapse-item')) {
                    link.classList.add('active');

                    const collapse = link.closest('.collapse');
                    if (collapse) {
                        collapse.classList.add('show');

                        const parentToggle = sidebar.querySelector(
                            '[data-target="#' + collapse.id + '"]'
                        );
                        if (parentToggle) {
                            parentToggle.classList.remove('collapsed');
                            parentToggle.setAttribute('aria-expanded', 'true');
                        }
                    }
                } else if (link.classList.contains('nav-link')) {
                    link.classList.add('active');
                }
            }
        } catch (error) {
            // Abaikan tautan yang tidak valid.
        }
    });

    // Klik menu induk: pindahkan shape ke menu yang diklik.
    sidebar.querySelectorAll('.nav-item > a.nav-link[data-toggle="collapse"]')
        .forEach(function (toggle) {
            toggle.addEventListener('click', function () {
                sidebar.querySelectorAll('.nav-item > a.nav-link')
                    .forEach(function (item) {
                        item.classList.remove('active');
                    });

                this.classList.add('active');
            });
        });
});
</script>
</ul>



<!-- End of Sidebar -->