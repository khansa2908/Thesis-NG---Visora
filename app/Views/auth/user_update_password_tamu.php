<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Ubah Kata Sandi Tamu</title>
    <!-- Asumsi menggunakan Bootstrap/SB Admin 2 style -->
    <link href="<?= base_url("assets/css/sb-admin-2.min.css") ?>" rel="stylesheet">
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
</head>
<body>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Ubah Kata Sandi Akun Tamu</h6>
    </div>
    <div class="card-body">

        <!-- SweetAlert Notifikasi -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>    
        <script>
            <?php if (session()->getFlashdata('message')): ?>
                Swal.fire({
                    icon: '<?= session()->getFlashdata('message')['icon'] ?>',
                    title: '<?= session()->getFlashdata('message')['title'] ?>',
                    text: '<?= session()->getFlashdata('message')['text'] ?>',
                })
            <?php endif; ?>
        </script>

        <!-- Form Update Password -->
        <form id="passwordForm" action="<?= base_url('user/updatePasswordSubmit') ?>" method="POST">
            
            <?= csrf_field() ?>
            
            <div class="form-group">
                <label for="old_password">Kata Sandi Lama</label>
                <input type="password" class="form-control" id="old_password" name="old_password" required>
            </div>
            
            <div class="form-group">
                <label for="new_password">Kata Sandi Baru</label>
                <input type="password" class="form-control" id="new_password" name="new_password" required>
                
                <!-- Indikator Kekuatan Password -->
                <div id="password-strength-bar" class="password-strength-bar"></div>
                <div id="strength-text" class="strength-text text-muted">Ketik password untuk melihat kekuatan</div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Kata Sandi Baru</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            
            <button type="submit" class="btn btn-success mt-3">Simpan Kata Sandi Baru</button>
        </form>
    </div>
</div>

<!-- Library Zxcvbn untuk cek kekuatan password -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.4.2/zxcvbn.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const newPassInput = document.getElementById('new_password');
    const strengthBar = document.getElementById('password-strength-bar');
    const strengthText = document.getElementById('strength-text');

    // Array untuk label dan warna berdasarkan skor zxcvbn (0-4)
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
            strengthBar.className = 'password-strength-bar'; // Reset kelas
            strengthText.textContent = 'Ketik password untuk melihat kekuatan';
            strengthText.className = 'strength-text text-muted';
            return;
        }

        // Cek kekuatan password menggunakan zxcvbn
        const result = zxcvbn(password);
        const score = result.score; // Skor antara 0 (terlemah) sampai 4 (terkuat)
        
        const level = strengthLevels[score];

        // Update bar dan teks
        strengthBar.style.width = level.width;
        
        // Atur warna bar (hapus semua kelas warna lama, tambahkan yang baru)
        strengthBar.className = 'password-strength-bar ' + level.color;

        // Atur teks kekuatan
        strengthText.textContent = `Kekuatan: ${level.label}`;
        strengthText.className = 'strength-text font-weight-bold text-' + (score < 2 ? 'danger' : (score < 4 ? 'info' : 'success'));
    });
});
</script>
</body>
</html>