
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login | TheSIS</title>

    <!-- Font dan ikon -->
    <link href="<?= base_url('assets/vendor/fontawesome-free/css/all.min.css') ?>"
          rel="stylesheet">

    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700,800"
          rel="stylesheet">

    <!-- Bootstrap dan CSS bawaan project -->
    <link href="<?= base_url('assets/css/sb-admin-2.min.css') ?>"
          rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #5b5d69, #343540);
            color: #111827;
        }

        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .login-card {
            display: grid;
            grid-template-columns: 1fr 1fr;
            width: 100%;
            max-width: 1100px;
            min-height: 620px;
            background: #fff;
            overflow: hidden;
            border-radius: 5px;
            box-shadow: 0 8px 35px rgba(0, 0, 0, 0.15);
        }

        /* Bagian ilustrasi kiri */
        .login-illustration {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 0;
            background: #dce7fc;
            overflow: hidden;
        }

        .login-illustration img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        /* Bagian form kanan */
        .login-content {
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 55px 42px;
            background: #fffdfd;
        }

        .login-title {
            margin: 0 0 36px;
            text-align: center;
            color: #111;
            font-size: 27px;
            font-weight: 800;
        }

        .login-form .form-group {
            margin-bottom: 28px;
        }

        .login-form label {
            display: block;
            margin-bottom: 7px;
            font-size: 12px;
            font-weight: 700;
            color: #202020;
        }
        
        .input-wrapper {
            position: relative;
            width: 100%;
            display: flex;
            align-items: center;
        }

        
.input-wrapper .input-icon {
    position: absolute;
    left: 14px;
    top: 23px;
    transform: translateY(-50%);
    color: #b4b6ba;
    font-size: 15px;
    line-height: 1;
    z-index: 2;
    pointer-events: none;
}


        .login-input {
            width: 100%;
            height: 46px;
            padding: 10px 42px;
            border: 1px solid #a9a9a9;
            border-radius: 8px;
            background: #fff;
            color: #333;
            font-family: inherit;
            font-size: 12px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .login-input::placeholder {
            color: #aaa;
        }

        .login-input:focus {
            border-color: #6595ff;
            box-shadow: 0 0 0 3px rgba(101, 149, 255, 0.14);
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            padding: 4px;
            border: 0;
            background: transparent;
            color: #aaa;
            cursor: pointer;
        }

        .login-button {
            width: 100%;
            height: 46px;
            margin-top: 6px;
            border: 0;
            border-radius: 7px;
            background: #6794ff;
            color: white;
            font-family: inherit;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
        }

        .login-button:hover {
            background: #4f7fe9;
        }

        .login-links {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 20px;
            font-size: 10px;
        }

        .login-links a {
            color: #0755ff;
            text-decoration: none;
        }

        .login-links a:hover {
            text-decoration: underline;
        }

        .login-links .link-separator {
            color: #0755ff;
        }

        /* Tampilan tablet dan HP */
        @media (max-width: 767.98px) {
            .login-page {
                padding: 16px;
            }

            .login-card {
                grid-template-columns: 1fr;
                max-width: 440px;
                min-height: auto;
            }

            .login-illustration {
                height: 220px;
            }

            .login-content {
                padding: 32px 28px 36px;
            }

            .login-title {
                margin-bottom: 28px;
            }

            .login-form .form-group {
                margin-bottom: 22px;
            }
        }

        @media (max-width: 380px) {
            .login-illustration {
                height: 170px;
            }

            .login-content {
                padding: 28px 20px;
            }

            .login-links {
                gap: 8px;
                font-size: 9px;
            }
        }
        
/* Pesan validasi tampil di bawah kolom input */
            .login-form .input-wrapper label.error {
                display: block !important;
                width: 100% !important;
                margin-top: 5px !important;
                margin-bottom: 0 !important;
                color: #ff3333 !important;
                font-size: 11px !important;
                line-height: 1.4 !important;
                text-align: left !important;
        }

/* Wrapper input dan pesan validasi */
            .login-form .input-wrapper {
                display: flex;
                flex-wrap: wrap;
        }

/* Input tetap memenuhi baris */
            .login-form .input-wrapper .login-input {
                flex: 0 0 100%;
                width: 100%;
        }
        /* Menghilangkan tombol reveal password bawaan browser */
            input::-ms-reveal,
            input::-ms-clear {
            display: none;
        }
        
    </style>
</head>

<body>

    <?php if (session()->getFlashdata("message")) : ?>
        <div id="flashdata" data-open="true">
            <p id="icon" hidden>
                <?= session()->getFlashdata("message")["icon"]; ?>
            </p>
            <p id="title" hidden>
                <?= session()->getFlashdata("message")["title"]; ?>
            </p>
            <p id="text" hidden>
                <?= session()->getFlashdata("message")["text"]; ?>
            </p>
        </div>
    <?php endif; ?>

    <main class="login-page">
        <div class="login-card">

            <!-- Ilustrasi sebelah kiri -->
            <div class="login-illustration">
                <img
                    src="<?= base_url('assets/img/hero_login_baru.png') ?>"
                    alt="Ilustrasi sistem informasi akademik">
            </div>

            <!-- Form login sebelah kanan -->
            <section class="login-content">

                <h1 class="login-title">Login</h1>

                <form
                    id="formLogin"
                    class="login-form"
                    method="post"
                    action="<?= base_url('auth/signInAccount') ?>">

                    <div class="form-group">
                        <label for="username">Username</label>

                        <div class="input-wrapper">
                            <i class="fas fa-user input-icon"
                               aria-hidden="true"></i>

                            <input
                                type="text"
                                class="login-input"
                                id="username"
                                name="username"
                                placeholder="Masukkan username"
                                autocomplete="username"
                                required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Kata Sandi</label>

                        <div class="input-wrapper">
                            <i class="fas fa-lock input-icon"
                               aria-hidden="true"></i>

                            <input
                                type="password"
                                class="login-input"
                                id="password"
                                name="password"
                                placeholder="Masukkan kata sandi"
                                autocomplete="current-password"
                                required>

                            <button
                                type="button"
                                class="password-toggle"
                                id="togglePassword"
                                aria-label="Tampilkan kata sandi">
                                <i class="fas fa-eye"
                                   id="passwordIcon"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="login-button">
                        Login
                    </button>

                </form>

                <div class="login-links">
                    <a href="<?= base_url('auth/pendaftaranTamu') ?>">
                        Daftar sebagai tamu
                    </a>

                    <span class="link-separator">|</span>

                    <a href="<?= base_url('auth/forgotPassword') ?>">
                        Lupa kata sandi?
                    </a>
                </div>

            </section>
        </div>
    </main>

    <!-- JavaScript bawaan project -->
    <script src="<?= base_url('assets/vendor/jquery/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/jquery-easing/jquery.easing.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/sb-admin-2.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/constant.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/sweetalert2/sweetalert2.all.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/jquery-validate/jquery.validate.min.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/jquery-validate/localization/messages_id.js') ?>"></script>
    <script src="<?= base_url('assets/js/auth/login.js') ?>"></script>

    <!-- Fungsi tampil/sembunyikan kata sandi -->
    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            const password = document.getElementById('password');
            const icon = document.getElementById('passwordIcon');
            const isHidden = password.type === 'password';

            password.type = isHidden ? 'text' : 'password';
            icon.classList.toggle('fa-eye', !isHidden);
            icon.classList.toggle('fa-eye-slash', isHidden);

            this.setAttribute(
                'aria-label',
                isHidden ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'
            );
        });
    </script>

</body>
</html>
