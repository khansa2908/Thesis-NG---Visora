<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-Equiv="Cache-Control" Content="no-cache" />
    <meta http-Equiv="Pragma" Content="no-cache" />
    <meta http-Equiv="Expires" Content="0" />

    <title><?= $title ?> | TheSIS</title>

    <link href="<?= base_url("assets/vendor/fontawesome-free/css/all.min.css") ?>" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="<?= base_url("assets/css/sb-admin-2.min.css") ?>" rel="stylesheet">
    
    <style>
        h1, h2, h3, h4, h5, h6 { color: #000; }
        
        /* CSS NOTIFIKASI */
        .notif-header {
            background-color: #ffffff !important;
            border-bottom: 1px solid #e3e6f0 !important;
            padding: 12px 16px !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            color: #3a3b45 !important;
        }
        .notif-item-link {
            display: block;
            padding: 12px 16px;
            border-bottom: 1px solid #e3e6f0;
            text-decoration: none !important;
            transition: all 0.2s;
        }
        .notif-item-link:hover { background-color: #f8f9fc !important; }
        .notif-scroll-area { max-height: 350px; overflow-y: auto; }
        .notif-footer-link {
            display: block; text-align: center; padding: 12px;
            color: #1cc88a !important; font-weight: bold;
            font-size: 0.85rem; background: #ffffff;
            border-top: 1px solid #e3e6f0;
        }
        .notif-unread {
            background-color: #ffffff;
            font-weight: bold;
            border-left: 4px solid #4e73df;
        }
        .notif-read {
            background-color: #f4f6f9;
            opacity: 0.7;
            border-left: 4px solid transparent;
        }

        /* Z-Index Fix untuk Profile Dropdown */
        #userDropdown { position: relative; z-index: 1060; }
        .dropdown-menu { z-index: 1050; }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        <?= $this->include("layout/sidebar"); ?> 
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <span class="badge badge-danger badge-counter" id="navbar-notif-count" style="display:none;">0</span>
                            </a>
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown" style="min-width: 320px; padding: 0 !important; border: none !important;">
                                <div id="navbar-notif-list">
                                    <div class="p-3 text-center small text-gray-500">Memuat...</div>
                                </div>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= session()->get("user_session")['nama'] ?></span>
                                <img class="img-profile rounded-circle" src="<?= base_url("assets/img/undraw_profile.svg") ?>">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>

                <div class="container-fluid">
                    <?= $this->renderSection('content'); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Yakin ingin keluar?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Pilih "Logout" di bawah jika Anda siap untuk mengakhiri sesi saat ini.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <a class="btn btn-primary" href="<?= base_url('Auth/logout') ?>">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= base_url("assets/vendor/jquery/jquery.min.js") ?>"></script>
    <script src="<?= base_url("assets/vendor/bootstrap/js/bootstrap.bundle.min.js") ?>"></script>
    <script src="<?= base_url("assets/js/sb-admin-2.min.js") ?>"></script>

    <script>
    $(document).ready(function() {
        function refreshNotif() {
            $.ajax({
                url: "<?= base_url('TenagaKependidikan/fetchNotif') ?>",
                type: "GET",
                dataType: "JSON",
                success: function(response) {
                    // Update Badge
                    if (response.unread > 0) {
                        $('#navbar-notif-count').text(response.unread).show();
                    } else {
                        $('#navbar-notif-count').hide();
                    }

                    // Susun HTML
                    let htmlOutput = `
                        <div class="notif-header">
                            <span class="font-weight-bold">Notifications</span>
                            <a href="javascript:void(0)" id="btn-mark-all" class="text-success small font-weight-bold">Mark all read</a>
                        </div>
                        <div class="notif-scroll-area">
                    `;

                    if (response.list && response.list.length > 0) {
                        response.list.forEach(function(item) {
                            let statusClass = (item.is_read_admin == 1) ? 'notif-read' : 'notif-unread';
                            htmlOutput += `
                                <a class="notif-item-link ${statusClass}" href="${item.link}">
                                    <div class="text-dark small mb-1">${item.pesan}</div>
                                    <div class="small text-gray-500">Klik untuk detail</div>
                                </a>
                            `;
                        });
                    } else {
                        htmlOutput += '<div class="p-4 text-center small text-gray-500">Belum ada pengajuan baru</div>';
                    }

                    htmlOutput += `</div>`; // Tutup notif-scroll-area
                    htmlOutput += `<a class="notif-footer-link" href="<?= base_url('TenagaKependidikan/semua_notifikasi') ?>">Lihat Semua Notifikasi</a>`;
                    
                    $('#navbar-notif-list').html(htmlOutput);
                },
                error: function() {
                    console.log("Error fetching notifications");
                }
            });
        }

        refreshNotif();
        setInterval(refreshNotif, 10000);

        $(document).on('click', '#btn-mark-all', function() {
            $.get("<?= base_url('TenagaKependidikan/markAsRead') ?>", function() {
                refreshNotif();
            });
        });
    });
    </script>
</body>
</html>