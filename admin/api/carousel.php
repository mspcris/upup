<?php
/**
 * UPUP Admin — API de Carrossel (Upload de Imagens)
 * ---------------------------------------------------
 * POST  multipart/form-data: evento, slot, imagem (file)
 * DELETE JSON body: { evento, slot }
 */

require_once dirname(__DIR__) . '/auth.php';

header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['upup_admin_logged'])) {
    jsonErr('Não autorizado.', 401);
}

$method = $_SERVER['REQUEST_METHOD'];

// ===== POST — Upload =====
if ($method === 'POST') {
    $evento = sanitizeSlug($_POST['evento'] ?? '');
    $slot   = sanitizeSlug($_POST['slot']   ?? '');

    if (!$evento || !array_key_exists($evento, EVENTS)) {
        jsonErr('Evento inválido.', 422);
    }

    $slotsValidos = CAROUSEL_SLOTS[$evento] ?? [];
    if (!in_array($slot, $slotsValidos, true)) {
        jsonErr('Slot inválido para este evento.', 422);
    }

    if (empty($_FILES['imagem']) || $_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
        $errMsg = uploadErrMsg($_FILES['imagem']['error'] ?? UPLOAD_ERR_NO_FILE);
        jsonErr('Erro no upload: ' . $errMsg, 422);
    }

    $file = $_FILES['imagem'];

    // Valida tamanho
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        jsonErr('Arquivo muito grande. Máximo: 8 MB.', 422);
    }

    // Valida MIME via finfo
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, ALLOWED_MIME, true)) {
        jsonErr('Tipo de arquivo não permitido. Use JPG, PNG, WebP ou GIF.', 422);
    }

    // Determina extensão pelo MIME
    $extMap = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];
    $ext = $extMap[$mime] ?? 'jpg';

    // Pasta de destino
    $imgDir   = EVENT_IMAGE_DIR[$evento] ?? $evento;
    $destDir  = UPLOAD_DIR . $imgDir . '/';

    if (!is_dir($destDir)) {
        if (!mkdir($destDir, 0755, true)) {
            jsonErr('Não foi possível criar o diretório de imagens.', 500);
        }
    }

    // Remove arquivos antigos do mesmo slot (outras extensões)
    foreach (ALLOWED_EXTENSIONS as $oldExt) {
        $old = $destDir . $slot . '.' . $oldExt;
        if (file_exists($old)) @unlink($old);
    }

    $destFile = $destDir . $slot . '.' . $ext;

    if (!move_uploaded_file($file['tmp_name'], $destFile)) {
        jsonErr('Falha ao salvar o arquivo no servidor.', 500);
    }

    // Protege acesso direto a PHP dentro de uploads (cria .htaccess se não existir)
    $htaccess = $destDir . '.htaccess';
    if (!file_exists($htaccess)) {
        file_put_contents($htaccess, "php_flag engine off\n");
    }

    echo json_encode([
        'ok'  => true,
        'url' => '../images/' . $imgDir . '/' . $slot . '.' . $ext,
    ]);
    exit;
}

// ===== DELETE — Remover imagem =====
if ($method === 'DELETE') {
    $body   = json_decode(file_get_contents('php://input'), true) ?? [];
    $evento = sanitizeSlug($body['evento'] ?? '');
    $slot   = sanitizeSlug($body['slot']   ?? '');

    if (!$evento || !$slot) jsonErr('Parâmetros inválidos.', 422);

    $imgDir  = EVENT_IMAGE_DIR[$evento] ?? $evento;
    $destDir = UPLOAD_DIR . $imgDir . '/';

    $removido = false;
    foreach (ALLOWED_EXTENSIONS as $ext) {
        $file = $destDir . $slot . '.' . $ext;
        if (file_exists($file)) {
            @unlink($file);
            $removido = true;
        }
    }

    echo json_encode(['ok' => true, 'removido' => $removido]);
    exit;
}

jsonErr('Método não permitido.', 405);

// ===== Helpers =====
function jsonErr(string $msg, int $code = 400): void {
    http_response_code($code);
    echo json_encode(['error' => $msg]);
    exit;
}

function sanitizeSlug(string $s): string {
    return preg_replace('/[^a-z0-9_]/', '', strtolower(trim($s)));
}

function uploadErrMsg(int $code): string {
    return match ($code) {
        UPLOAD_ERR_INI_SIZE,
        UPLOAD_ERR_FORM_SIZE => 'Arquivo excede o tamanho máximo permitido.',
        UPLOAD_ERR_PARTIAL   => 'O arquivo foi enviado apenas parcialmente.',
        UPLOAD_ERR_NO_FILE   => 'Nenhum arquivo foi selecionado.',
        UPLOAD_ERR_NO_TMP_DIR => 'Pasta temporária não encontrada.',
        UPLOAD_ERR_CANT_WRITE => 'Falha ao escrever no disco.',
        default              => "Código de erro: $code",
    };
}
