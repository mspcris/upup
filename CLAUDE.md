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

## Deploy na VM — via Docker (recomendado)

Docker isola tudo e não instala nada diretamente no host.

### 1. Instalar Docker na VM (uma vez só)

```bash
curl -fsSL https://get.docker.com | sh
sudo usermod -aG docker $USER
# Fazer logout e login novamente para o grupo surtir efeito
```

### 2. Clonar e subir o site

```bash
git clone https://github.com/mspcris/upup.git
cd upup

# Subir o container (build + start)
docker compose up -d --build
```

O entrypoint já:
- Cria as pastas necessárias com permissões corretas
- Roda o seed automaticamente se o banco não existir
- Inicia PHP-FPM + nginx

### 3. Verificar se está no ar

```bash
docker compose ps          # deve mostrar upup-site running
docker compose logs -f     # acompanhar logs em tempo real
curl -I http://localhost    # deve retornar 200
```

### 4. Atualizar o site após git push

```bash
cd upup
git pull
docker compose up -d --build   # reconstrói e reinicia
```

### 5. HTTPS com Certbot (no host, não no container)

O Certbot roda no host e faz proxy reverso para o container na porta 80.

```bash
# Instalar nginx no host só como proxy reverso
sudo apt install -y nginx certbot python3-certbot-nginx

# Criar config mínima em /etc/nginx/sites-available/upup:
# server { listen 80; server_name upup.ong.br; location / { proxy_pass http://localhost:80; } }
sudo ln -sf /etc/nginx/sites-available/upup /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx

# Gerar certificado SSL
sudo certbot --nginx -d upup.ong.br -d www.upup.ong.br
```

> Alternativa mais simples: usar Caddy ou Traefik como proxy com SSL automático.

### Volumes persistidos fora do container

```
admin/data/        ← banco SQLite (não some ao rebuildar)
images/pascoa2026/ ← fotos do carrossel
```

### Comandos úteis

```bash
docker compose down            # parar
docker compose restart upup    # reiniciar sem rebuild
docker compose exec upup sh    # entrar no container
docker compose exec upup python3 admin/seed_pascoa2026.py  # rodar seed manualmente
```

### Seed manual (se precisar repovoar o banco)

```bash
# Sem venv — usa apenas sqlite3 da stdlib
python3 admin/seed_pascoa2026.py
# ou dentro do container:
docker compose exec upup python3 admin/seed_pascoa2026.py
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
