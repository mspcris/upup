<?php
require_once __DIR__ . '/auth.php';
requireLogin();

$eventos    = EVENTS;
$eventoAtual = $_GET['evento'] ?? array_key_first($eventos);
if (!array_key_exists($eventoAtual, $eventos)) {
    $eventoAtual = array_key_first($eventos);
}
$eventoNome = $eventos[$eventoAtual];
$slots      = CAROUSEL_SLOTS[$eventoAtual] ?? [];
$imgDir     = EVENT_IMAGE_DIR[$eventoAtual] ?? $eventoAtual;
$imgPath    = UPLOAD_DIR . $imgDir . '/';

// Lê os slots do carrossel (verifica se a imagem existe)
$carouselInfo = [];
foreach ($slots as $slot) {
    foreach (['jpg','jpeg','png','webp','gif'] as $ext) {
        $file = $imgPath . $slot . '.' . $ext;
        if (file_exists($file)) {
            $carouselInfo[$slot] = [
                'exists'  => true,
                'url'     => '../images/' . $imgDir . '/' . $slot . '.' . $ext . '?t=' . filemtime($file),
                'file'    => $file,
                'ext'     => $ext,
            ];
            break;
        }
    }
    if (!isset($carouselInfo[$slot])) {
        $carouselInfo[$slot] = ['exists' => false, 'url' => ''];
    }
}

$pdo    = getDB();
$stmt   = $pdo->prepare("SELECT * FROM donations WHERE event = ? ORDER BY ordem ASC, id ASC");
$stmt->execute([$eventoAtual]);
$donations = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPUP Admin — Dashboard</title>
    <link rel="shortcut icon" href="../images/upup_logomarca_somente_nome_up_favicon.png" type="image/x-icon">
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --upup-purple:  #6B3FA0;
            --upup-purple2: #9B59B6;
            --upup-yellow:  #F5C400;
            --sidebar-w:    240px;
        }

        body { background: #f0ebf8; }

        /* ===== TOPBAR ===== */
        .topbar {
            position: sticky; top: 0; z-index: 1040;
            background: var(--upup-purple);
            color: #fff;
            height: 56px;
            display: flex; align-items: center;
            padding: 0 1.25rem;
            box-shadow: 0 2px 12px rgba(0,0,0,.25);
        }
        .topbar-brand {
            font-weight: 800; font-size: 1.1rem;
            text-decoration: none; color: #fff;
            display: flex; align-items: center; gap: .6rem;
        }
        .topbar-brand img { width: 32px; }
        .topbar-brand span { color: var(--upup-yellow); }
        .topbar-user { margin-left: auto; font-size: .88rem; opacity: .85; }
        .btn-logout {
            background: rgba(255,255,255,.15);
            border: 1px solid rgba(255,255,255,.3);
            color: #fff;
            border-radius: 8px;
            padding: .35rem .75rem;
            font-size: .85rem;
            text-decoration: none;
            margin-left: .75rem;
            transition: background .2s;
        }
        .btn-logout:hover { background: rgba(255,255,255,.25); color: #fff; }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: var(--sidebar-w);
            min-height: calc(100vh - 56px);
            background: #fff;
            border-right: 1px solid #e5d9f7;
            padding: 1.25rem .75rem;
            position: sticky; top: 56px;
        }
        .sidebar-label {
            font-size: .72rem; font-weight: 700;
            color: #999; text-transform: uppercase;
            letter-spacing: .06em; padding: .25rem .75rem;
            margin-bottom: .25rem;
        }
        .sidebar-link {
            display: flex; align-items: center; gap: .6rem;
            padding: .65rem .85rem; border-radius: 10px;
            text-decoration: none; color: #444;
            font-weight: 500; font-size: .92rem;
            transition: all .18s;
        }
        .sidebar-link:hover { background: #f0ebf8; color: var(--upup-purple); }
        .sidebar-link.active { background: var(--upup-purple); color: #fff; }
        .sidebar-link .fa { width: 18px; text-align: center; }
        .sidebar-event-badge {
            display: inline-block; background: #f0ebf8;
            color: var(--upup-purple); border-radius: 6px;
            font-size: .72rem; font-weight: 700; padding: 2px 7px;
            margin-left: auto;
        }

        /* ===== CONTEÚDO ===== */
        .content { flex: 1; padding: 1.5rem; min-width: 0; }

        /* ===== CARDS ===== */
        .stat-card {
            background: #fff; border-radius: 16px;
            padding: 1.25rem; box-shadow: 0 2px 12px rgba(107,63,160,.08);
            border: 1px solid #e5d9f7;
        }
        .stat-number {
            font-size: 2rem; font-weight: 800;
            color: var(--upup-purple); line-height: 1;
        }
        .stat-label { font-size: .82rem; color: #888; margin-top: .25rem; }

        .section-card {
            background: #fff; border-radius: 20px;
            box-shadow: 0 2px 16px rgba(107,63,160,.08);
            border: 1px solid #e5d9f7;
            overflow: hidden;
        }
        .section-header {
            padding: 1.1rem 1.5rem;
            border-bottom: 1px solid #e5d9f7;
            display: flex; align-items: center;
            justify-content: space-between; gap: .75rem;
            flex-wrap: wrap;
        }
        .section-header h5 { margin: 0; font-weight: 700; color: var(--upup-purple); }

        /* ===== TABELA ===== */
        .table th { font-size: .82rem; text-transform: uppercase;
                    letter-spacing: .04em; color: #888; font-weight: 600;
                    border-bottom: 2px solid #e5d9f7; }
        .table td { vertical-align: middle; }
        .table tbody tr:hover { background: #f8f5ff; }
        .badge-pendente { background: rgba(245,158,11,.15); color: #92400e; }
        .badge-assumido { background: rgba(59,130,246,.15); color: #1e3a8a; }
        .badge-quitado  { background: rgba(34,197,94,.15);  color: #14532d; }

        /* ===== CARROSSEL ADMIN ===== */
        .carousel-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            padding: 1.25rem;
        }
        .slot-card {
            border: 2px dashed #d1bfef;
            border-radius: 14px;
            overflow: hidden;
            background: #f8f4ff;
            position: relative;
            aspect-ratio: 16/9;
            display: flex; align-items: center; justify-content: center;
        }
        .slot-card.has-image { border-style: solid; border-color: #9B59B6; }
        .slot-card img {
            width: 100%; height: 100%; object-fit: cover;
            display: block;
        }
        .slot-overlay {
            position: absolute; inset: 0;
            background: rgba(0,0,0,.55);
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: .5rem;
            opacity: 0; transition: opacity .2s;
        }
        .slot-card:hover .slot-overlay { opacity: 1; }
        .slot-placeholder {
            display: flex; flex-direction: column; align-items: center;
            gap: .5rem; color: #b39ddb; font-size: .85rem;
        }
        .slot-label {
            position: absolute; top: 6px; left: 8px;
            background: rgba(107,63,160,.85); color: #fff;
            font-size: .72rem; font-weight: 700; padding: 2px 8px;
            border-radius: 999px;
        }

        /* Botões de ação */
        .btn-purple  { background: var(--upup-purple); color: #fff; border: none; }
        .btn-purple:hover { background: #5a3490; color: #fff; }
        .btn-purple-sm { background: var(--upup-purple); color: #fff; border: none; font-size: .8rem; padding: .3rem .7rem; }
        .btn-purple-sm:hover { background: #5a3490; color: #fff; }

        /* ===== MODAL ===== */
        .modal-header { background: var(--upup-purple); color: #fff; border-radius: 20px 20px 0 0; }
        .modal-header .btn-close { filter: invert(1); }
        .modal-content { border-radius: 20px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,.25); }

        /* ===== RESPONSIVO ===== */
        @media (max-width: 768px) {
            .sidebar { display: none; }
            .content { padding: 1rem .75rem; }
        }
    </style>
</head>
<body>

<!-- TOPBAR -->
<header class="topbar">
    <a class="topbar-brand" href="dashboard.php">
        <img src="../images/upup_logomarca_somente_nome_up_favicon.png" alt="UPUP" onerror="this.style.display='none'">
        UPUP <span>Admin</span>
    </a>
    <div class="topbar-user">
        <i class="fa-solid fa-user-circle"></i>
        <?= h($_SESSION['upup_admin_user'] ?? 'admin') ?>
        <a href="api/logout.php" class="btn-logout">
            <i class="fa-solid fa-right-from-bracket"></i> Sair
        </a>
    </div>
</header>

<div class="d-flex">
    <!-- SIDEBAR -->
    <nav class="sidebar">
        <p class="sidebar-label">Eventos</p>
        <?php foreach ($eventos as $slug => $nome): ?>
        <a href="dashboard.php?evento=<?= urlencode($slug) ?>"
           class="sidebar-link <?= $eventoAtual === $slug ? 'active' : '' ?>">
            <i class="fa-solid fa-egg fa"></i>
            <?= h($nome) ?>
            <span class="sidebar-event-badge"><?= date('Y') ?></span>
        </a>
        <?php endforeach; ?>

        <hr class="my-3" style="border-color:#e5d9f7">
        <p class="sidebar-label">Site</p>
        <a href="../<?= h($eventoAtual) ?>.html" target="_blank" class="sidebar-link">
            <i class="fa-solid fa-arrow-up-right-from-square fa"></i> Ver evento
        </a>
        <a href="../<?= h($eventoAtual) ?>_prestacao_de_contas.html" target="_blank" class="sidebar-link">
            <i class="fa-solid fa-table-list fa"></i> Ver prestação de contas
        </a>
        <a href="../index.html" target="_blank" class="sidebar-link">
            <i class="fa-solid fa-house fa"></i> Site principal
        </a>
    </nav>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="content">

        <!-- Título do evento -->
        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
            <div>
                <h4 class="mb-0 fw-bold" style="color:var(--upup-purple)">
                    🐣 <?= h($eventoNome) ?>
                </h4>
                <small class="text-muted">Gerenciar doações e carrossel de fotos</small>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="../<?= h($eventoAtual) ?>_prestacao_de_contas.html" target="_blank"
                   class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-eye"></i> Visualizar tabela
                </a>
            </div>
        </div>

        <!-- STATS -->
        <div class="row g-3 mb-4">
            <?php
            $total   = count($donations);
            $assumido = count(array_filter($donations, fn($d) => !empty($d['responsavel']) && $d['pagou'] !== 'sim'));
            $quitado = count(array_filter($donations, fn($d) => $d['pagou'] === 'sim'));
            $pendente = $total - $assumido - $quitado;
            ?>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-number"><?= $total ?></div>
                    <div class="stat-label">Total de itens</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-number" style="color:#92400e"><?= $pendente ?></div>
                    <div class="stat-label">Pendentes</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-number" style="color:#1e3a8a"><?= $assumido ?></div>
                    <div class="stat-label">Assumidos</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-number" style="color:#14532d"><?= $quitado ?></div>
                    <div class="stat-label">Quitados</div>
                </div>
            </div>
        </div>

        <!-- SEÇÃO: DOAÇÕES -->
        <div class="section-card mb-4">
            <div class="section-header">
                <h5><i class="fa-solid fa-hand-holding-heart me-2"></i>Doações</h5>
                <button class="btn btn-sm btn-purple" data-bs-toggle="modal" data-bs-target="#modalDoacao"
                        onclick="abrirModalNovo()">
                    <i class="fa-solid fa-plus"></i> Adicionar item
                </button>
            </div>

            <?php if (empty($donations)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fa-solid fa-inbox fa-3x mb-3" style="opacity:.3"></i>
                <p>Nenhum item cadastrado ainda.<br>Clique em "Adicionar item" para começar.</p>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Ord.</th>
                            <th>Item / Descrição</th>
                            <th>Valor / Qtd</th>
                            <th>Responsável</th>
                            <th>Status</th>
                            <th style="width:1%;white-space:nowrap">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-doacoes">
                        <?php foreach ($donations as $d): ?>
                        <?php
                        if ($d['pagou'] === 'sim') {
                            $badgeClass = 'badge-quitado'; $badgeLabel = 'Quitado';
                        } elseif (!empty($d['responsavel'])) {
                            $badgeClass = 'badge-assumido'; $badgeLabel = 'Assumido';
                        } else {
                            $badgeClass = 'badge-pendente'; $badgeLabel = 'Pendente';
                        }
                        ?>
                        <tr id="row-<?= (int)$d['id'] ?>">
                            <td><small class="text-muted"><?= (int)$d['ordem'] ?></small></td>
                            <td class="fw-semibold"><?= h($d['item']) ?></td>
                            <td><?= h($d['valor'] ?: '—') ?></td>
                            <td><?= h($d['responsavel'] ?: '—') ?></td>
                            <td><span class="badge <?= $badgeClass ?>"><?= $badgeLabel ?></span></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-purple-sm"
                                            onclick="abrirModalEdicao(<?= htmlspecialchars(json_encode($d), ENT_QUOTES) ?>)"
                                            title="Editar">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger"
                                            onclick="excluirDoacao(<?= (int)$d['id'] ?>, '<?= h(addslashes($d['item'])) ?>')"
                                            title="Excluir">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <!-- SEÇÃO: CARROSSEL -->
        <div class="section-card mb-4">
            <div class="section-header">
                <h5><i class="fa-solid fa-images me-2"></i>Carrossel de Fotos</h5>
                <small class="text-muted">Clique em qualquer foto para substituir. Proporção recomendada: 16:9.</small>
            </div>

            <div class="carousel-grid">
                <?php foreach ($carouselInfo as $slot => $info): ?>
                <div class="slot-card <?= $info['exists'] ? 'has-image' : '' ?>"
                     onclick="document.getElementById('upload-<?= h($slot) ?>').click()"
                     title="Clique para enviar uma imagem">
                    <span class="slot-label"><?= h(str_replace('_', ' ', $slot)) ?></span>

                    <?php if ($info['exists']): ?>
                    <img src="<?= h($info['url']) ?>" alt="Foto <?= h($slot) ?>">
                    <div class="slot-overlay">
                        <i class="fa-solid fa-camera fa-2x text-white"></i>
                        <span class="text-white" style="font-size:.8rem;font-weight:600">Substituir foto</span>
                    </div>
                    <?php else: ?>
                    <div class="slot-placeholder">
                        <i class="fa-solid fa-image fa-2x"></i>
                        <span>Sem imagem<br>Clique para enviar</span>
                    </div>
                    <?php endif; ?>

                    <!-- Input oculto para upload -->
                    <form id="form-<?= h($slot) ?>" style="display:none">
                        <input type="file" id="upload-<?= h($slot) ?>"
                               accept="image/jpeg,image/png,image/webp,image/gif"
                               onchange="uploadImagem('<?= h($eventoAtual) ?>', '<?= h($slot) ?>', this)">
                        <input type="hidden" name="evento" value="<?= h($eventoAtual) ?>">
                        <input type="hidden" name="slot"   value="<?= h($slot) ?>">
                    </form>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="px-4 pb-3">
                <small class="text-muted">
                    <i class="fa-solid fa-circle-info me-1"></i>
                    As fotos enviadas aparecem automaticamente no site, no carrossel da página de prestação de contas.
                    Tamanho máximo: 8 MB. Formatos: JPG, PNG, WebP, GIF.
                </small>
            </div>
        </div>

        <!-- TOAST de feedback -->
        <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:9999">
            <div id="toastMsg" class="toast align-items-center text-white border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body" id="toastBody"></div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>

    </main>
</div>

<!-- ===== MODAL DE DOAÇÃO (Criar / Editar) ===== -->
<div class="modal fade" id="modalDoacao" tabindex="-1" aria-labelledby="modalDoacaoLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDoacaoLabel">
                    <i class="fa-solid fa-hand-holding-heart me-2"></i>
                    <span id="modalDoacaoTitulo">Adicionar Item</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formDoacao" novalidate>
                    <input type="hidden" id="doacaoId" name="id" value="">
                    <input type="hidden" name="evento" value="<?= h($eventoAtual) ?>">

                    <div class="mb-3">
                        <label for="doacaoItem" class="form-label fw-semibold">Item / Descrição <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="doacaoItem" name="item"
                               placeholder="Ex: Ovos de Páscoa 500g" required maxlength="255">
                        <div class="invalid-feedback">Informe o item.</div>
                    </div>

                    <div class="mb-3">
                        <label for="doacaoValor" class="form-label fw-semibold">Valor / Quantidade</label>
                        <input type="text" class="form-control" id="doacaoValor" name="valor"
                               placeholder="Ex: R$ 25,00  ou  10 unidades" maxlength="100">
                    </div>

                    <div class="mb-3">
                        <label for="doacaoResponsavel" class="form-label fw-semibold">Responsável / Doador</label>
                        <input type="text" class="form-control" id="doacaoResponsavel" name="responsavel"
                               placeholder="Nome de quem assumiu a doação" maxlength="150">
                        <small class="text-muted">Deixe em branco se ainda não há responsável (status: Pendente).</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status do pagamento</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="pagou" id="pagouNao" value="nao" checked>
                                <label class="form-check-label" for="pagouNao">Não pago</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="pagou" id="pagouSim" value="sim">
                                <label class="form-check-label" for="pagouSim">Quitado / Recebido</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="doacaoOrdem" class="form-label fw-semibold">Ordem de exibição</label>
                        <input type="number" class="form-control" id="doacaoOrdem" name="ordem"
                               value="0" min="0" max="9999" style="width:120px">
                        <small class="text-muted">Menor número aparece primeiro na tabela.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-purple" id="btnSalvarDoacao" onclick="salvarDoacao()">
                    <i class="fa-solid fa-floppy-disk"></i> Salvar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
const EVENTO = '<?= h($eventoAtual) ?>';

// ===== TOAST =====
function showToast(msg, tipo = 'success') {
    const el   = document.getElementById('toastMsg');
    const body = document.getElementById('toastBody');
    body.textContent = msg;
    el.className = 'toast align-items-center text-white border-0 bg-' +
                   (tipo === 'success' ? 'success' : 'danger');
    bootstrap.Toast.getOrCreateInstance(el, { delay: 3500 }).show();
}

// ===== MODAL DOAÇÃO =====
function abrirModalNovo() {
    document.getElementById('modalDoacaoTitulo').textContent = 'Adicionar Item';
    document.getElementById('formDoacao').reset();
    document.getElementById('doacaoId').value = '';
    document.getElementById('pagouNao').checked = true;
}

function abrirModalEdicao(d) {
    document.getElementById('modalDoacaoTitulo').textContent = 'Editar Item';
    document.getElementById('doacaoId').value          = d.id;
    document.getElementById('doacaoItem').value        = d.item;
    document.getElementById('doacaoValor').value       = d.valor || '';
    document.getElementById('doacaoResponsavel').value = d.responsavel || '';
    document.getElementById('doacaoOrdem').value       = d.ordem || 0;
    if (d.pagou === 'sim') {
        document.getElementById('pagouSim').checked = true;
    } else {
        document.getElementById('pagouNao').checked = true;
    }
    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalDoacao')).show();
}

async function salvarDoacao() {
    const form = document.getElementById('formDoacao');
    if (!form.checkValidity()) { form.reportValidity(); return; }

    const data = Object.fromEntries(new FormData(form));
    const isEdit = !!data.id;

    const btn = document.getElementById('btnSalvarDoacao');
    btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Salvando...';

    try {
        const res = await fetch('api/donations.php', {
            method: isEdit ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data),
        });
        const json = await res.json();
        if (!res.ok || json.error) throw new Error(json.error || 'Erro');

        showToast(isEdit ? 'Item atualizado com sucesso!' : 'Item adicionado com sucesso!');
        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalDoacao')).hide();
        setTimeout(() => location.reload(), 900);
    } catch (e) {
        showToast('Erro: ' + e.message, 'danger');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Salvar';
    }
}

async function excluirDoacao(id, nome) {
    if (!confirm(`Excluir o item "${nome}"?\n\nEsta ação não pode ser desfeita.`)) return;

    try {
        const res  = await fetch('api/donations.php', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id }),
        });
        const json = await res.json();
        if (!res.ok || json.error) throw new Error(json.error || 'Erro');

        showToast('Item excluído.');
        setTimeout(() => location.reload(), 800);
    } catch (e) {
        showToast('Erro ao excluir: ' + e.message, 'danger');
    }
}

// ===== UPLOAD DE IMAGEM =====
async function uploadImagem(evento, slot, input) {
    if (!input.files || !input.files[0]) return;

    const fd = new FormData();
    fd.append('evento', evento);
    fd.append('slot',   slot);
    fd.append('imagem', input.files[0]);

    showToast('Enviando imagem...', 'success');

    try {
        const res  = await fetch('api/carousel.php', { method: 'POST', body: fd });
        const json = await res.json();
        if (!res.ok || json.error) throw new Error(json.error || 'Erro no upload');

        showToast('Imagem atualizada com sucesso!');
        setTimeout(() => location.reload(), 1000);
    } catch (e) {
        showToast('Erro no upload: ' + e.message, 'danger');
    }

    input.value = '';
}
</script>
</body>
</html>
