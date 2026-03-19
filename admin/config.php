<?php
/**
 * UPUP Admin — Configuração Central
 * -----------------------------------
 * IMPORTANTE: Não versionar este arquivo com senhas reais em produção.
 * Altere ADMIN_PASS_HASH usando: password_hash('sua_senha', PASSWORD_BCRYPT)
 */

define('ADMIN_USER',      'admin');

// Senha padrão: upup@2026  — troque em produção!
// Para gerar novo hash: php -r "echo password_hash('sua_nova_senha', PASSWORD_BCRYPT);"
define('ADMIN_PASS_HASH', '$2y$10$wDJDQRDiWP/MdrRWdhnVCubbavdvTmbPgDXLVfIy8525B5yYClhZ.');

define('DB_PATH',         __DIR__ . '/data/upup.db');
define('UPLOAD_DIR',      __DIR__ . '/../images/');
define('MAX_UPLOAD_SIZE', 8 * 1024 * 1024); // 8 MB

define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp', 'gif']);
define('ALLOWED_MIME',       ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);

// Eventos disponíveis no admin
define('EVENTS', [
    'pascoa2026' => 'Páscoa Solidária 2026',
]);

// Slots do carrossel por evento
define('CAROUSEL_SLOTS', [
    'pascoa2026' => ['slot_1','slot_2','slot_3','slot_4','slot_5'],
]);

// Mapeamento evento → pasta de imagens
define('EVENT_IMAGE_DIR', [
    'pascoa2026' => 'pascoa2026',
]);
