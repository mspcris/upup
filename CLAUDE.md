# UPUP — Contexto para Claude Code

## O projeto
Site da ONG **UPUP — Unidos Por Um Propósito**, em Natividade-RJ.
Domínio: **upup.ong.br** (A record apontado para esta VM em março/2026).
Repositório: **https://github.com/mspcris/upup.git** (branch: main)

---

## Stack

- **Front-end**: HTML5 puro + CSS customizado + Vanilla JS (sem frameworks)
- **Admin**: PHP 8+ com Bootstrap 5, sessão PHP, SQLite via PDO
- **Sem banco externo**: tudo local em `admin/data/upup.db` (criado automaticamente)
- **Servidor web**: nginx + PHP-FPM (não Apache — `.htaccess` é ignorado)
- **Deploy**: git pull na VM + rodar seed se necessário

---

## Estrutura de pastas

```
upup/
├── index.html
├── pascoa2026.html               ← Páscoa Solidária 2026 (campanha)
├── pascoa2026_prestacao_de_contas.html  ← Tabela de doações (API local)
├── natal_feliz.html              ← Natal Solidário 2025
├── diadascriancas2025_prestacao_de_contas.html  ← ainda usa Google Sheets
├── css/style.css
├── js/  (header.js, footer.js, menu.js, carrossel.js, overlay.js, preloader.js)
├── images/
│   ├── pascoa2026/               ← fotos gerenciadas pelo admin (slot_1…slot_5)
│   ├── carrossel1/               ← Dia das Crianças
│   ├── carrossel2/               ← homepage
│   └── carrossel5/               ← Natal 2025
└── admin/
    ├── index.php                 ← login
    ├── dashboard.php             ← painel CRUD doações + upload carrossel
    ├── config.php                ← credenciais e configurações
    ├── auth.php                  ← sessão + getDB() SQLite
    ├── seed_pascoa2026.py        ← popula o banco com os 46 itens da Páscoa
    ├── api/
    │   ├── donations.php         ← GET público / POST PUT DELETE autenticado
    │   ├── carousel.php          ← upload de imagens
    │   └── logout.php
    └── data/
        └── upup.db               ← SQLite (não vai pro git; criado pelo seed)
```

---

## Admin

- URL: `https://upup.ong.br/admin/`
- Usuário: `admin`
- Senha padrão: `upup@2026` — **trocar após o deploy**
- Para gerar novo hash: `php -r "echo password_hash('nova_senha', PASSWORD_BCRYPT);"`
- Atualizar hash em `admin/config.php` → `ADMIN_PASS_HASH`

---

## Deploy completo na VM (passo a passo)

### 1. Instalar dependências (se ainda não tiver)

```bash
# PHP + extensões necessárias
sudo apt update
sudo apt install -y php8.2-fpm php8.2-sqlite3 php8.2-mbstring

# Verificar versão
php -v
```

### 2. Configurar nginx para servir o site com PHP

Editar (ou criar) o arquivo de site do nginx, ex: `/etc/nginx/sites-available/upup`:

```nginx
server {
    listen 80;
    server_name upup.ong.br www.upup.ong.br;

    root /CAMINHO/PARA/upup;   # ← ajustar para o caminho real do repositório
    index index.html index.php;

    # Arquivos estáticos (HTML, CSS, JS, imagens)
    location / {
        try_files $uri $uri/ =404;
    }

    # PHP via PHP-FPM
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Bloquear acesso direto ao banco SQLite e config
    location ~* \.(db|sqlite|sqlite3)$ { deny all; }
    location = /admin/config.php { deny all; }
    location = /admin/auth.php   { deny all; }

    # HTTPS — se usar Certbot, ele adiciona automaticamente
}
```

```bash
sudo ln -sf /etc/nginx/sites-available/upup /etc/nginx/sites-enabled/
sudo nginx -t          # testar configuração
sudo systemctl reload nginx
sudo systemctl restart php8.2-fpm
```

### 3. Permissões

```bash
# Dentro do diretório do repositório:
chmod 755 admin/data/
chmod 755 images/pascoa2026/

# O usuário do nginx (www-data) precisa escrever no admin/data/
sudo chown -R www-data:www-data admin/data/
sudo chown -R www-data:www-data images/pascoa2026/
```

### 4. Popular o banco de dados

```bash
# Sem venv — usa apenas sqlite3 da stdlib do Python3
python3 admin/seed_pascoa2026.py
```

Saída esperada: `✅ 46 itens inseridos em .../admin/data/upup.db`

Se o banco precisar ser acessado pelo nginx (www-data):
```bash
sudo chown www-data:www-data admin/data/upup.db
chmod 664 admin/data/upup.db
```

### 5. HTTPS com Certbot (recomendado)

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d upup.ong.br -d www.upup.ong.br
```

---

## Git

```bash
git pull          # atualizar na VM após push do dev
git status
git push          # (credenciais já salvas em ~/.git-credentials)
```

Repositório: `https://github.com/mspcris/upup.git` — branch: `main`

---

## Carrosséis CSS (radio button, sem bibliotecas)

| Classe | Radio name | Pasta de imagens | Usado em |
|---|---|---|---|
| `.carousel1` | `radio-btn-1` | `images/carrossel1/` | Dia das Crianças |
| `.carousel2` | `radio-btn-2` | `images/carrossel2/` | Homepage |
| `.carousel3` | `radio-btn-3` | `images/pascoa2026/` | Páscoa 2026 (admin) |

---

## Paleta de cores

```css
--upup-yellow: #F5C400   /* marca principal */
--upup-blue:   #0A4C9A   /* apoio */
--pascoa-purple:  #6B3FA0
--pascoa-purple2: #9B59B6
--pascoa-card-bg: #FAF5FF
```

---

## API de doações

- **GET público**: `admin/api/donations.php?event=pascoa2026` → JSON (usado pela prestação de contas)
- **POST/PUT/DELETE**: requer sessão autenticada (admin)

---

## Como adicionar novo evento

1. Em `admin/config.php`: adicionar em `EVENTS`, `CAROUSEL_SLOTS`, `EVENT_IMAGE_DIR`
2. Criar `{evento}.html` e `{evento}_prestacao_de_contas.html`
3. A prestação carrega dados de `admin/api/donations.php?event={slug}`
4. Adicionar link no menu de todas as páginas (dentro de `#grp-atividades`)

---

## Contatos / dados fixos no código

| Campo | Valor |
|---|---|
| WhatsApp | (22) 99862-6059 |
| E-mail | upupoficial@gmail.com |
| PIX CNPJ | 48.211.513/0001-78 |
| PIX e-mail | upupoficial@gmail.com |
| Banco | Banco do Brasil |
| Endereço | Rua Dr. Antônio de Carvalho Cavalcanti, 248 — Cantinho Fiorello, Natividade-RJ — CEP 28380-000 |
| CNPJ | 48.211.513/0001-78 |
