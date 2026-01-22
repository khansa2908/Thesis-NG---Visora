<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Reset Kata Sandi | TheSIS</title>

    <!-- Custom fonts for this template-->
    <link href="<?= base_url("assets/vendor/fontawesome-free/css/all.min.css") ?>" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="<?= base_url("assets/css/sb-admin-2.min.css") ?>" rel="stylesheet">

    <style>
        .bg-login-image {
            background: url(<?= base_url("assets/img/hero_login.png"); ?>);
            background-position: center;
            background-size: unset;
            background-repeat: no-repeat;
        }
    </style>

</head>

<body>

    <div class="container">

        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Reset Kata Sandi Akun Tamu</h6>
                    </div>
                    <div class="card-body p-4">

                        <!-- Notifikasi SweetAlert (jika ada) -->
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

                        <!-- Form Reset (username + email) -->
                        <form id="formReset" method="post" action="<?= base_url('auth/forgotSubmit') ?>">

                            <div class="form-group">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" placeholder="username anda" required>
                            </div>

                            <div class="form-group">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="nama@domain.com" required>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-success">Reset</button>
                            </div>

                            <div class="text-center mt-3">
                                <a href="<?= base_url('auth/login') ?>" class="small text-primary">Kembali ke login</a>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="<?= base_url("assets/vendor/jquery/jquery.min.js") ?>"></script>
    <script src="<?= base_url("assets/vendor/bootstrap/js/bootstrap.bundle.min.js") ?>"></script>

    <!-- Core plugin JavaScript-->
    <script src="<?= base_url("assets/vendor/jquery-easing/jquery.easing.min.js") ?>"></script>

    <!-- Custom scripts for all pages-->
    <script src="<?= base_url("assets/js/sb-admin-2.min.js") ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>    

    <script>
        // 2. Logika PHP/JS untuk menampilkan notifikasi
        <?php if (session()->getFlashdata('message')): ?>
            Swal.fire({
                icon: '<?= session()->getFlashdata('message')['icon'] ?>', // success/error
                title: '<?= session()->getFlashdata('message')['title'] ?>',
                text: '<?= session()->getFlashdata('message')['text'] ?>',
            })
        <?php endif; ?>
    </script>
    
</body>
</html>
    </body>

</html>
