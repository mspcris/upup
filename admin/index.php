<?php
require_once __DIR__ . '/auth.php';

// Se já está logado, vai direto pro dashboard
if (!empty($_SESSION['upup_admin_logged'])) {
    header('Location: dashboard.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['usuario'] ?? '');
    $pass = $_POST['senha'] ?? '';
    if (tryLogin($user, $pass)) {
        header('Location: dashboard.php');
        exit;
    }
    $erro = 'Usuário ou senha incorretos.';
    // Atraso para dificultar brute-force
    sleep(1);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPUP Admin — Login</title>
    <link rel="shortcut icon" href="../images/upup_logomarca_somente_nome_up_favicon.png" type="image/x-icon">
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --upup-purple: #6B3FA0;
            --upup-yellow: #F5C400;
        }
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #4A148C 0%, #7B1FA2 50%, #AB47BC 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .login-card {
            background: #fff;
            border-radius: 20px;
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.35);
        }
        .login-logo {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .login-logo img {
            width: 72px;
            height: auto;
        }
        .login-logo h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--upup-purple);
            margin: .5rem 0 .15rem;
        }
        .login-logo small {
            color: #888;
            font-size: .85rem;
        }
        .btn-login {
            background: var(--upup-purple);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: .85rem;
            font-weight: 700;
            width: 100%;
            font-size: 1rem;
            transition: filter .2s;
        }
        .btn-login:hover { filter: brightness(1.1); }
        .form-control:focus {
            border-color: var(--upup-purple);
            box-shadow: 0 0 0 3px rgba(107,63,160,.18);
        }
        .input-group-text {
            background: #f4eeff;
            border-color: #dee2e6;
            color: var(--upup-purple);
        }
        .alert-danger {
            border-radius: 12px;
        }
        .hint-box {
            background: #f4eeff;
            border: 1px solid rgba(107,63,160,.2);
            border-radius: 10px;
            padding: .75rem 1rem;
            font-size: .82rem;
            color: #555;
            margin-top: 1.25rem;
        }
        .hint-box code {
            background: rgba(107,63,160,.1);
            color: var(--upup-purple);
            border-radius: 4px;
            padding: 1px 5px;
        }
        footer-text {
            text-align: center;
            color: #aaa;
            font-size: .78rem;
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-logo">
            <img src="../images/upup_logomarca_fundo_transparente_400px_30res.png"
                 alt="UPUP Logo"
                 onerror="this.style.display='none'">
            <h1>UPUP Admin</h1>
            <small>Painel de Administração</small>
        </div>

        <?php if ($erro): ?>
        <div class="alert alert-danger d-flex align-items-center gap-2" role="alert">
            <i class="fa-solid fa-circle-xmark"></i>
            <?= h($erro) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="" novalidate>
            <div class="mb-3">
                <label for="usuario" class="form-label fw-semibold">Usuário</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                    <input type="text" class="form-control" id="usuario" name="usuario"
                           value="<?= h($_POST['usuario'] ?? '') ?>"
                           placeholder="admin" autocomplete="username" required autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label for="senha" class="form-label fw-semibold">Senha</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" class="form-control" id="senha" name="senha"
                           placeholder="••••••••" autocomplete="current-password" required>
                    <button class="btn btn-outline-secondary" type="button" id="toggleSenha"
                            aria-label="Mostrar/ocultar senha">
                        <i class="fa-solid fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="fa-solid fa-right-to-bracket"></i> Entrar
            </button>
        </form>

        <div class="hint-box">
            <strong>Acesso padrão:</strong><br>
            Usuário: <code>admin</code> &nbsp; Senha: <code>upup@2026</code><br>
            <span style="color:#c0392b">Troque a senha em <code>admin/config.php</code> após o deploy.</span>
        </div>

        <p style="text-align:center;color:#bbb;font-size:.78rem;margin-top:1.5rem;">
            &copy; <?= date('Y') ?> UPUP — Unidos Por Um Propósito
        </p>
    </div>

    <script>
        document.getElementById('toggleSenha').addEventListener('click', function () {
            const input = document.getElementById('senha');
            const icon  = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    </script>
</body>
</html>
