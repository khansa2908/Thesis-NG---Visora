<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link href="<?= base_url('assets/vendor/fontawesome-free/css/all.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/sb-admin-2.min.css') ?>" rel="stylesheet">
</head>

<body class="bg-gradient-dark">

<div class="container mt-5">

    <div class="row justify-content-center">
        <div class="col-lg-6">

            <div class="card shadow-lg">
                <div class="card-body">

                    <h3 class="text-center mb-4">Atur Ulang Kata Sandi</h3>

                    <form action="<?= base_url('auth/resetSubmit') ?>" method="POST">

                        <input type="hidden" name="token" value="<?= $token ?>">

                        <div class="form-group">
                            <label>Password Baru</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>

                        <div class="form-group">
                            <label>Konfirmasi Password</label>
                            <input type="password" class="form-control" name="password_confirm" required>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block mt-3">
                            Reset Password
                        </button>

                    </form>

                </div>
            </div>

        </div>
    </div>

</div>

</body>
</html>

