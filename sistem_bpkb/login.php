<?php
require_once 'config/config.php';
require_once 'config/database.php';

// Sudah login → langsung ke home
if (!empty($_SESSION['user_id'])) {
    header('Location: pages/home.php');
    exit();
}

$error = '';
$activeTab = $_GET['tab'] ?? 'maf'; // default tab: maf

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = MD5(trim($_POST['password'] ?? ''));
    $loginAs  = $_POST['login_as'] ?? 'maf';
    $activeTab = $loginAs;

    // Map tab ke role yang diharapkan
    $roleMap = [
        'maf'   => 'MAF',
        'mcf'   => 'MCF',
        'kacab' => 'kacab',
    ];
    $expectedRole = $roleMap[$loginAs] ?? '';

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ? AND role = ? AND is_active = 1");
    $stmt->bind_param('sss', $username, $password, $expectedRole);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user) {
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['nama']     = $user['nama'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'];
        header('Location: pages/home.php');
        exit();
    } else {
        $error = 'Username atau password salah, atau tidak sesuai dengan tipe login yang dipilih!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — <?= APP_NAME ?? 'Sistem Monitoring BPKB' ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    
    <link href="assets/css/style.css" rel="stylesheet">

    <style>
        :root {
            --brand-yellow: #FFCA28;
            --brand-orange: #F57C00;
            --brand-grey: #495057;
            --bg-light: #F8F9FA;
            --maf-color: #F57C00;
            --mcf-color: #1565C0;
            --kacab-color: #2E7D32;
        }
        
        body.page-login {
            background: linear-gradient(135deg, #fff8e1 0%, #fff3e0 50%, #fce4ec 100%);
            font-family: 'Plus Jakarta Sans', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }

        .login-wrap {
            width: 100%;
            padding: 15px;
            display: flex;
            justify-content: center;
        }

        .login-card {
            background: #ffffff;
            width: 100%;
            max-width: 440px;
            padding: 35px 30px 30px;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
            border-top: 5px solid var(--brand-orange);
        }

        .login-logo {
            text-align: center;
            margin-bottom: 25px;
        }

        .login-logo img {
            width: 75px; 
            height: 75px;
            object-fit: contain;
            margin-bottom: 10px;
            border-radius: 50%; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.08); 
        }

        .login-title {
            color: var(--brand-grey);
            font-weight: 700;
            font-size: 1.2rem;
            margin-top: 8px;
            margin-bottom: 3px;
        }

        .login-subtitle {
            color: #8c9097;
            font-size: 0.85rem;
        }

        /* Tab Login */
        .login-tabs {
            display: flex;
            gap: 6px;
            margin-bottom: 22px;
            background: #f5f5f5;
            border-radius: 10px;
            padding: 5px;
        }

        .login-tab-btn {
            flex: 1;
            padding: 9px 4px;
            border: none;
            border-radius: 7px;
            font-weight: 600;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.25s ease;
            background: transparent;
            color: #888;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
        }

        .login-tab-btn i {
            font-size: 1rem;
        }

        .login-tab-btn.active-maf {
            background: var(--maf-color);
            color: #fff;
            box-shadow: 0 4px 10px rgba(245, 124, 0, 0.3);
        }

        .login-tab-btn.active-mcf {
            background: var(--mcf-color);
            color: #fff;
            box-shadow: 0 4px 10px rgba(21, 101, 192, 0.3);
        }

        .login-tab-btn.active-kacab {
            background: var(--kacab-color);
            color: #fff;
            box-shadow: 0 4px 10px rgba(46, 125, 50, 0.3);
        }

        /* Role info badge */
        .role-info-badge {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .role-info-badge.maf {
            background: rgba(245, 124, 0, 0.08);
            border: 1px solid rgba(245, 124, 0, 0.25);
            color: var(--maf-color);
        }

        .role-info-badge.mcf {
            background: rgba(21, 101, 192, 0.08);
            border: 1px solid rgba(21, 101, 192, 0.25);
            color: var(--mcf-color);
        }

        .role-info-badge.kacab {
            background: rgba(46, 125, 50, 0.08);
            border: 1px solid rgba(46, 125, 50, 0.25);
            color: var(--kacab-color);
        }

        .form-label {
            font-weight: 600;
            color: var(--brand-grey);
            font-size: 0.88rem;
        }

        .form-control {
            padding: 10px 15px;
            font-size: 0.95rem;
            border-radius: 8px;
            background-color: #FFF8E1 !important;
            color: #495057 !important;
            border: 1px solid #ddd !important;
        }
        
        .form-control::placeholder {
            color: #B0BEC5 !important;
            opacity: 1;
        }

        .form-control:focus {
            background-color: #FFFFFF !important;
            border-color: var(--brand-orange) !important;
            box-shadow: 0 0 0 0.25rem rgba(245, 124, 0, 0.2) !important;
            color: #495057 !important;
        }

        .btn-login {
            font-weight: 700;
            padding: 12px;
            border: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 12px;
            font-size: 0.95rem;
        }

        .btn-login-maf {
            background-color: var(--maf-color);
            color: #ffffff;
        }
        .btn-login-maf:hover {
            background-color: #E65100;
            color: #ffffff;
            transform: translateY(-1px);
        }

        .btn-login-mcf {
            background-color: var(--mcf-color);
            color: #ffffff;
        }
        .btn-login-mcf:hover {
            background-color: #0D47A1;
            color: #ffffff;
            transform: translateY(-1px);
        }

        .btn-login-kacab {
            background-color: var(--kacab-color);
            color: #ffffff;
        }
        .btn-login-kacab:hover {
            background-color: #1B5E20;
            color: #ffffff;
            transform: translateY(-1px);
        }

        .tab-content-panel { display: none; }
        .tab-content-panel.show { display: block; }
    </style>
</head>
<body class="page-login">

<div class="login-wrap">
    <div class="login-card">

        <div class="login-logo">
            <img src="assets/img/icon-m.png" alt="Logo Mega Group">
            <h2 class="login-title">Sistem BPKB</h2>
            <p class="login-subtitle">Mega Auto Central Finance</p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-danger" style="font-size: 0.88rem; border-radius: 8px;">
            <i class="fa-solid fa-circle-exclamation me-2"></i><?= $error ?>
        </div>
        <?php endif; ?>

        <!-- Tab Switcher -->
        <div class="login-tabs">
            <button type="button" class="login-tab-btn <?= $activeTab === 'maf' ? 'active-maf' : '' ?>" onclick="switchTab('maf')">
                <i class="fa-solid fa-building-columns"></i>
                Admin MAF
            </button>
            <button type="button" class="login-tab-btn <?= $activeTab === 'mcf' ? 'active-mcf' : '' ?>" onclick="switchTab('mcf')">
                <i class="fa-solid fa-landmark"></i>
                Admin MCF
            </button>
            <button type="button" class="login-tab-btn <?= $activeTab === 'kacab' ? 'active-kacab' : '' ?>" onclick="switchTab('kacab')">
                <i class="fa-solid fa-user-tie"></i>
                Ketua Cabang
            </button>
        </div>

        <!-- MAF Panel -->
        <div class="tab-content-panel <?= $activeTab === 'maf' ? 'show' : '' ?>" id="panel-maf">
            <div class="role-info-badge maf">
                <i class="fa-solid fa-circle-info"></i>
                Login sebagai Admin MAF — Mega Auto Finance
            </div>
            <form method="POST" autocomplete="off">
                <input type="hidden" name="login_as" value="maf">
                <div class="form-group mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control"
                           placeholder="Username Admin MAF" required
                           value="<?= $activeTab === 'maf' ? sanitize($_POST['username'] ?? '') : '' ?>">
                </div>
                <div class="form-group mb-1">
                    <label class="form-label">Password</label>
                    <div style="position:relative">
                        <input type="password" name="password" id="pw-maf" class="form-control"
                               placeholder="Password" required style="padding-right:42px">
                        <button type="button" onclick="togglePw('pw-maf','icon-maf')"
                                style="position:absolute;top:50%;right:12px;transform:translateY(-50%);background:none;border:none;color:#8c9097;cursor:pointer;font-size:16px;padding:0;z-index:10;">
                            <i class="fa-solid fa-eye" id="icon-maf"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-login btn-login-maf">
                    <i class="fa-solid fa-right-to-bracket me-2"></i> Masuk sebagai Admin MAF
                </button>
            </form>
        </div>

        <!-- MCF Panel -->
        <div class="tab-content-panel <?= $activeTab === 'mcf' ? 'show' : '' ?>" id="panel-mcf">
            <div class="role-info-badge mcf">
                <i class="fa-solid fa-circle-info"></i>
                Login sebagai Admin MCF — Mega Central Finance
            </div>
            <form method="POST" autocomplete="off">
                <input type="hidden" name="login_as" value="mcf">
                <div class="form-group mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control"
                           placeholder="Username Admin MCF" required
                           value="<?= $activeTab === 'mcf' ? sanitize($_POST['username'] ?? '') : '' ?>">
                </div>
                <div class="form-group mb-1">
                    <label class="form-label">Password</label>
                    <div style="position:relative">
                        <input type="password" name="password" id="pw-mcf" class="form-control"
                               placeholder="Password" required style="padding-right:42px">
                        <button type="button" onclick="togglePw('pw-mcf','icon-mcf')"
                                style="position:absolute;top:50%;right:12px;transform:translateY(-50%);background:none;border:none;color:#8c9097;cursor:pointer;font-size:16px;padding:0;z-index:10;">
                            <i class="fa-solid fa-eye" id="icon-mcf"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-login btn-login-mcf">
                    <i class="fa-solid fa-right-to-bracket me-2"></i> Masuk sebagai Admin MCF
                </button>
            </form>
        </div>

        <!-- Kacab Panel -->
        <div class="tab-content-panel <?= $activeTab === 'kacab' ? 'show' : '' ?>" id="panel-kacab">
            <div class="role-info-badge kacab">
                <i class="fa-solid fa-circle-info"></i>
                Login sebagai Ketua Cabang — Approval & Monitoring
            </div>
            <form method="POST" autocomplete="off">
                <input type="hidden" name="login_as" value="kacab">
                <div class="form-group mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control"
                           placeholder="Username Ketua Cabang" required
                           value="<?= $activeTab === 'kacab' ? sanitize($_POST['username'] ?? '') : '' ?>">
                </div>
                <div class="form-group mb-1">
                    <label class="form-label">Password</label>
                    <div style="position:relative">
                        <input type="password" name="password" id="pw-kacab" class="form-control"
                               placeholder="Password" required style="padding-right:42px">
                        <button type="button" onclick="togglePw('pw-kacab','icon-kacab')"
                                style="position:absolute;top:50%;right:12px;transform:translateY(-50%);background:none;border:none;color:#8c9097;cursor:pointer;font-size:16px;padding:0;z-index:10;">
                            <i class="fa-solid fa-eye" id="icon-kacab"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-login btn-login-kacab">
                    <i class="fa-solid fa-right-to-bracket me-2"></i> Masuk sebagai Ketua Cabang
                </button>
            </form>
        </div>

        <p style="text-align:center; font-size:12px; color:#a0a5b1; margin-top:25px; margin-bottom:0;">
            &copy; <?= date('Y') ?> Mega Group. All rights reserved.
        </p>
    </div>
</div>

<script>
function switchTab(tab) {
    // Sembunyikan semua panel
    document.querySelectorAll('.tab-content-panel').forEach(p => p.classList.remove('show'));
    // Hapus semua active dari tombol
    document.querySelectorAll('.login-tab-btn').forEach(b => {
        b.classList.remove('active-maf', 'active-mcf', 'active-kacab');
    });
    // Tampilkan panel yang dipilih
    document.getElementById('panel-' + tab).classList.add('show');
    // Tambahkan kelas aktif ke tombol
    const btns = document.querySelectorAll('.login-tab-btn');
    if (tab === 'maf')   btns[0].classList.add('active-maf');
    if (tab === 'mcf')   btns[1].classList.add('active-mcf');
    if (tab === 'kacab') btns[2].classList.add('active-kacab');
}

function togglePw(inputId, iconId) {
    const pw = document.getElementById(inputId);
    const ic = document.getElementById(iconId);
    if (pw.type === 'password') { 
        pw.type = 'text'; 
        ic.className = 'fa-solid fa-eye-slash'; 
    } else { 
        pw.type = 'password'; 
        ic.className = 'fa-solid fa-eye'; 
    }
}
</script>
</body>
</html>
