<?php
/**
 * UPUP Admin — Autenticação via sessão
 */

require_once __DIR__ . '/config.php';

session_start();

/**
 * Verifica se o usuário está autenticado.
 * Se não estiver, redireciona para a tela de login.
 */
function requireLogin(): void {
    if (empty($_SESSION['upup_admin_logged'])) {
        header('Location: ' . adminUrl('index.php'));
        exit;
    }
}

/**
 * Tenta autenticar com usuário e senha.
 * Retorna true em caso de sucesso.
 */
function tryLogin(string $user, string $pass): bool {
    if ($user === ADMIN_USER && password_verify($pass, ADMIN_PASS_HASH)) {
        $_SESSION['upup_admin_logged'] = true;
        $_SESSION['upup_admin_user']   = $user;
        session_regenerate_id(true);
        return true;
    }
    return false;
}

/**
 * Encerra a sessão do admin.
 */
function doLogout(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();
}

/**
 * Retorna a URL base do admin.
 */
function adminUrl(string $file = ''): string {
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    // Se estamos em /admin/api/, subir um nível
    if (str_contains($base, '/api')) {
        $base = dirname($base);
    }
    return $base . '/' . ltrim($file, '/');
}

/**
 * Inicializa (ou migra) o banco de dados SQLite.
 */
function getDB(): PDO {
    $dir = dirname(DB_PATH);
    if (!is_dir($dir)) {
        mkdir($dir, 0750, true);
    }

    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $pdo->exec("PRAGMA journal_mode=WAL;");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS donations (
            id           INTEGER PRIMARY KEY AUTOINCREMENT,
            event        TEXT    NOT NULL DEFAULT 'pascoa2026',
            item         TEXT    NOT NULL,
            valor        TEXT,
            responsavel  TEXT,
            pagou        TEXT    NOT NULL DEFAULT 'nao',
            tipo_doacao  TEXT    NOT NULL DEFAULT 'produto',
            ordem        INTEGER NOT NULL DEFAULT 0,
            created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at   DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // Migração: adiciona coluna se banco já existia sem ela
    try {
        $pdo->exec("ALTER TABLE donations ADD COLUMN tipo_doacao TEXT NOT NULL DEFAULT 'produto'");
    } catch (PDOException) {
        // Coluna já existe — ignorar
    }

    return $pdo;
}

/**
 * Sanitiza saída HTML para evitar XSS.
 */
function h(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
