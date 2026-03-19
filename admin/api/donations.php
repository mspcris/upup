<?php
/**
 * UPUP Admin — API de Doações
 * ----------------------------
 * GET    ?event=slug         → lista pública (sem auth)
 * POST   JSON body           → cria (requer auth)
 * PUT    JSON body           → atualiza (requer auth)
 * DELETE JSON body {id}      → remove (requer auth)
 */

require_once dirname(__DIR__) . '/auth.php';

header('Content-Type: application/json; charset=utf-8');

// CORS simples (mesmo domínio; ajuste se necessário)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204); exit;
}

$method = $_SERVER['REQUEST_METHOD'];

// ===== GET — leitura pública =====
if ($method === 'GET') {
    $event = trim($_GET['event'] ?? 'pascoa2026');
    if (!preg_match('/^[a-z0-9_]+$/', $event)) {
        jsonError('Evento inválido.', 400);
    }

    try {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            "SELECT id, event, item, valor, responsavel, pagou, tipo_doacao, ordem
             FROM donations WHERE event = ?
             ORDER BY ordem ASC, id ASC"
        );
        $stmt->execute([$event]);
        $rows = $stmt->fetchAll();
        echo json_encode($rows, JSON_UNESCAPED_UNICODE);
    } catch (Throwable $e) {
        jsonError('Erro interno.', 500);
    }
    exit;
}

// Para os demais métodos exige autenticação
if (empty($_SESSION['upup_admin_logged'])) {
    jsonError('Não autorizado.', 401);
}

// Lê body JSON
$body = json_decode(file_get_contents('php://input'), true) ?? [];

// ===== POST — criar =====
if ($method === 'POST') {
    $item        = trim($body['item'] ?? '');
    $evento      = sanitizeSlug($body['evento'] ?? '');
    $valor       = trim($body['valor'] ?? '');
    $responsavel = trim($body['responsavel'] ?? '');
    $pagou       = in_array($body['pagou'] ?? '', ['sim','nao']) ? $body['pagou'] : 'nao';
    $tipo        = in_array($body['tipo_doacao'] ?? '', ['produto','dinheiro']) ? $body['tipo_doacao'] : 'produto';
    $ordem       = (int)($body['ordem'] ?? 0);

    if (!$item) jsonError('O campo "item" é obrigatório.', 422);
    if (!$evento) jsonError('Evento inválido.', 422);

    try {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            "INSERT INTO donations (event, item, valor, responsavel, pagou, tipo_doacao, ordem)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$evento, $item, $valor, $responsavel, $pagou, $tipo, $ordem]);
        $id = $pdo->lastInsertId();
        http_response_code(201);
        echo json_encode(['ok' => true, 'id' => $id]);
    } catch (Throwable $e) {
        jsonError('Erro ao inserir.', 500);
    }
    exit;
}

// ===== PUT — atualizar =====
if ($method === 'PUT') {
    $id          = (int)($body['id'] ?? 0);
    $item        = trim($body['item'] ?? '');
    $valor       = trim($body['valor'] ?? '');
    $responsavel = trim($body['responsavel'] ?? '');
    $pagou       = in_array($body['pagou'] ?? '', ['sim','nao']) ? $body['pagou'] : 'nao';
    $tipo        = in_array($body['tipo_doacao'] ?? '', ['produto','dinheiro']) ? $body['tipo_doacao'] : 'produto';
    $ordem       = (int)($body['ordem'] ?? 0);

    if (!$id)   jsonError('ID inválido.', 422);
    if (!$item) jsonError('O campo "item" é obrigatório.', 422);

    try {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            "UPDATE donations
             SET item = ?, valor = ?, responsavel = ?, pagou = ?, tipo_doacao = ?, ordem = ?,
                 updated_at = CURRENT_TIMESTAMP
             WHERE id = ?"
        );
        $stmt->execute([$item, $valor, $responsavel, $pagou, $tipo, $ordem, $id]);
        echo json_encode(['ok' => true]);
    } catch (Throwable $e) {
        jsonError('Erro ao atualizar.', 500);
    }
    exit;
}

// ===== DELETE — remover =====
if ($method === 'DELETE') {
    $id = (int)($body['id'] ?? 0);
    if (!$id) jsonError('ID inválido.', 422);

    try {
        $pdo  = getDB();
        $stmt = $pdo->prepare("DELETE FROM donations WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['ok' => true]);
    } catch (Throwable $e) {
        jsonError('Erro ao excluir.', 500);
    }
    exit;
}

jsonError('Método não permitido.', 405);

// ===== Helpers =====
function jsonError(string $msg, int $code = 400): void {
    http_response_code($code);
    echo json_encode(['error' => $msg]);
    exit;
}

function sanitizeSlug(string $s): string {
    $s = preg_replace('/[^a-z0-9_]/', '', strtolower(trim($s)));
    return $s;
}
