<?= $this->extend('layout/template'); ?>

<?= $this->section('styles'); ?>
<style>
    .password-strength-bar {
        height: 10px;
        margin-top: 5px;
        border-radius: 5px;
        transition: width 0.3s ease, background-color 0.3s ease;
    }
    .strength-text {
        font-size: 0.8rem;
        margin-top: 5px;
    }
</style>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>

<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Ubah Kata Sandi</h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Ganti Kata Sandi Akun</h6>
                </div>
                <div class="card-body">

                    <?php if (session()->getFlashdata('message')): ?>
                        <div class="alert alert-<?= (session()->getFlashdata('message')['icon'] == 'success') ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                            <strong><?= session()->getFlashdata('message')['title']; ?></strong> 
                            <?= session()->getFlashdata('message')['text']; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>
    
                    <form id="passwordForm" action="<?= base_url('auth/updatePasswordSubmit') ?>" method="POST"> 
                        <?= csrf_field() ?>
                        
                        <div class="form-group">
                            <label for="old_password">Kata Sandi Lama</label>
                            <input type="password" class="form-control" id="old_password" name="old_password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password">Kata Sandi Baru</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                            
                            <div class="progress mt-2" style="height: 10px;">
                                <div id="password-strength-bar" class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div id="strength-text" class="strength-text text-muted">Ketik password untuk melihat kekuatan</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        
                        <hr>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-save"></i> Simpan Kata Sandi Baru
                        </button> 
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.4.2/zxcvbn.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const newPassInput = document.getElementById('new_password');
    const strengthBar = document.getElementById('password-strength-bar');
    const strengthText = document.getElementById('strength-text');

    const strengthLevels = [
        { label: 'Sangat Lemah', color: 'bg-danger', width: '20%' },
        { label: 'Lemah', color: 'bg-warning', width: '40%' },
        { label: 'Sedang', color: 'bg-info', width: '60%' },
        { label: 'Kuat', color: 'bg-success', width: '80%' },
        { label: 'Sangat Kuat', color: 'bg-primary', width: '100%' }
    ];

    newPassInput.addEventListener('input', function() {
        const password = newPassInput.value;
        
        if (password.length === 0) {
            strengthBar.style.width = '0%';
            strengthBar.className = 'progress-bar'; 
            strengthText.textContent = 'Ketik password untuk melihat kekuatan';
            strengthText.className = 'strength-text text-muted';
            return;
        }

        const result = zxcvbn(password);
        const score = result.score; // 0-4
        
        const level = strengthLevels[score];

        strengthBar.style.width = level.width;
        strengthBar.className = 'progress-bar ' + level.color;

        strengthText.textContent = `Kekuatan: ${level.label}`;
        strengthText.className = 'strength-text font-weight-bold text-' + (score < 2 ? 'danger' : (score < 4 ? 'info' : 'success'));
    });
});
</script>

<script>
    <?php if (session()->getFlashdata('message')): ?>
        Swal.fire({
            icon: '<?= session()->getFlashdata('message')['icon'] ?>',
            title: '<?= session()->getFlashdata('message')['title'] ?>',
            text: '<?= session()->getFlashdata('message')['text'] ?>',
        })
    <?php endif; ?>
</script>
<?= $this->endSection(); ?>