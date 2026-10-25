<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Kelola Pengguna</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Semua Akun</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; foreach($users as $u): ?>
                    
                    <?php 
                        // --- [FIX: TERJEMAHKAN ID_ROLE KE NAMA ROLE] ---
                        // Kita buat variabel $roleName biar tidak error "Undefined array key"
                        $roleName = "User"; // Default
                        
                        // Cek kalau controller sudah mengirim 'role' (tulisan)
                        if (isset($u['role'])) {
                            $roleName = $u['role'];
                        } 
                        // Kalau belum, kita cek 'id_role' (angka) dan terjemahkan manual
                        elseif (isset($u['id_role'])) {
                            switch ($u['id_role']) {
                                case 1: $roleName = "Mahasiswa"; break;
                                case 2: $roleName = "Dosen"; break;
                                case 3: $roleName = "Kaprodi"; break;
                                case 4: $roleName = "Tendik"; break;
                                case 5: $roleName = "Fakultas"; break;
                                case 7: $roleName = "Klien"; break;
                                case 8: $roleName = "Admin"; break;
                                case 99: $roleName = "Tamu"; break;
                                default: $roleName = "Role ID: " . $u['id_role']; break;
                            }
                        }
                    ?>

                    <tr>
                        <td><?= $i++; ?></td>
                        <td><?= $u['username']; ?></td>
                        <td><?= $u['email']; ?></td>
                        <td><span class="badge badge-info"><?= $roleName; ?></span></td>
                        <td>
                            <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#resetModal<?= $u['id']; ?>">
                                <i class="fas fa-key"></i> Reset
                            </button>
                            <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#roleModal<?= $u['id']; ?>">
                                <i class="fas fa-user-tag"></i> Role
                            </button>
                        </td>
                    </tr>

                    <div class="modal fade" id="roleModal<?= $u['id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Ganti Role - <?= $u['username']; ?></h5>
                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                </div>
                                <form action="<?= base_url('admin/changeRole') ?>" method="post">
                                    <div class="modal-body">
                                        <input type="hidden" name="id" value="<?= $u['id']; ?>">
                                        <div class="form-group">
                                            <label for="roleSelect<?= $u['id']; ?>">Pilih Role Baru</label>
                                            <select id="roleSelect<?= $u['id']; ?>" name="role" class="form-control">
                                                <option value="Admin" <?= (isset($u['id_role']) && $u['id_role']==8) ? 'selected' : ''; ?>>Admin</option>
                                                <option value="Dosen" <?= (isset($u['id_role']) && $u['id_role']==2) ? 'selected' : ''; ?>>Dosen</option>
                                                <option value="Mahasiswa" <?= (isset($u['id_role']) && $u['id_role']==1) ? 'selected' : ''; ?>>Mahasiswa</option>
                                                <option value="Tendik" <?= (isset($u['id_role']) && $u['id_role']==4) ? 'selected' : ''; ?>>Tendik</option>
                                                <option value="Kaprodi" <?= (isset($u['id_role']) && $u['id_role']==3) ? 'selected' : ''; ?>>Kaprodi</option>
                                                <option value="Klien" <?= (isset($u['id_role']) && $u['id_role']==7) ? 'selected' : ''; ?>>Klien</option>
                                                <option value="Tamu" <?= (isset($u['id_role']) && $u['id_role']==99) ? 'selected' : ''; ?>>Tamu</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="resetModal<?= $u['id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title">Reset Password Paksa - <?= $u['username']; ?></h5>
                                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                                </div>
                                <form action="<?= base_url('admin/forceReset') ?>" method="post">
                                    <div class="modal-body">
                                        <input type="hidden" name="id" value="<?= $u['id']; ?>">
                                        <p class="text-danger small font-weight-bold">PERINGATAN: Password lama user akan langsung diganti.</p>
                                        <div class="form-group">
                                            <label for="password<?= $u['id']; ?>">Password Baru</label>
                                            <input id="password<?= $u['id']; ?>" type="password" name="password" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="confirm<?= $u['id']; ?>">Konfirmasi Password</label>
                                            <input id="confirm<?= $u['id']; ?>" type="password" name="confirm_password" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-danger">Reset Password</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script src="<?= base_url('assets/vendor/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/datatables/dataTables.bootstrap4.min.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable().destroy();
        }
        $('#dataTable').DataTable();
    });

    <?php if (session()->getFlashdata('message')): ?>
        Swal.fire({
            icon: '<?= session()->getFlashdata('message')['icon'] ?>',
            title: '<?= session()->getFlashdata('message')['title'] ?>',
            text: '<?= session()->getFlashdata('message')['text'] ?>',
        })
    <?php endif; ?>
</script>
<?= $this->endSection(); ?>