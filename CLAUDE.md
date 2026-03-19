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
- **Deploy**: esta VM serve diretamente os arquivos (fazer `git pull` após push)

---

## Estrutura de pastas

```
upup/
├── index.html                    ← Homepage
├── pascoa2026.html               ← Páscoa Solidária 2026 (campanha)
├── pascoa2026_prestacao_de_contas.html  ← Tabela de doações (API local)
├── natal_feliz.html              ← Natal Solidário 2025
├── diadascriancas2025_prestacao_de_contas.html  ← ainda usa Google Sheets
├── css/style.css                 ← CSS global (carrosséis, menu, cards, tabelas)
├── js/
│   ├── header.js / footer.js     ← injeta header.html e footer.html nas páginas
│   ├── menu.js                   ← drawer lateral
│   ├── carrossel.js              ← auto-advance (carousel1/2/3)
│   ├── overlay.js                ← anúncios/popup
│   └── preloader.js              ← splash screen
├── images/
│   ├── pascoa2026/               ← fotos do carrossel gerenciadas pelo admin
│   │   └── slot_1.jpg … slot_5.jpg
│   ├── carrossel1/               ← fotos Dia das Crianças
│   ├── carrossel2/               ← fotos homepage
│   └── carrossel5/               ← fotos Natal 2025
└── admin/
    ├── index.php                 ← login (Bootstrap 5)
    ├── dashboard.php             ← painel: doações CRUD + upload carrossel
    ├── config.php                ← credenciais e configurações
    ├── auth.php                  ← sessão + getDB() SQLite
    ├── api/
    │   ├── donations.php         ← GET público / POST PUT DELETE autenticado
    │   ├── carousel.php          ← upload de imagens
    │   └── logout.php
    └── data/
        └── upup.db               ← banco SQLite (criado automaticamente)
```

---

## Admin

- URL: `https://upup.ong.br/admin/`
- Usuário: `admin`
- Senha padrão: `upup@2026` — **trocar em produção**
- Para gerar novo hash: `php -r "echo password_hash('nova_senha', PASSWORD_BCRYPT);"`
- Atualizar hash em `admin/config.php` → `ADMIN_PASS_HASH`

### Permissões necessárias no servidor
```bash
chmod 755 admin/data/
chmod 755 images/pascoa2026/
```

---

## Carrosséis CSS (radio button, sem bibliotecas)

| Classe CSS | Radio name | Imagens | Usado em |
|---|---|---|---|
| `.carousel1` | `radio-btn-1` | `images/carrossel1/` | Dia das Crianças |
| `.carousel2` | `radio-btn-2` | `images/carrossel2/` | Homepage |
| `.carousel3` | `radio-btn-3` | `images/pascoa2026/` | Páscoa 2026 (admin) |

---

## Paleta de cores

```css
--upup-yellow: #F5C400   /* marca principal */
--upup-blue:   #0A4C9A   /* apoio */

/* Páscoa 2026 */
--pascoa-purple:  #6B3FA0
--pascoa-purple2: #9B59B6
--pascoa-card-bg: #FAF5FF
```

---

## API de doações

- **Pública (GET)**: `admin/api/donations.php?event=pascoa2026`
  Retorna JSON. Usado pela página de prestação de contas.
- **Autenticada**: POST / PUT / DELETE via sessão PHP.

---

## Como adicionar novo evento

1. Em `admin/config.php`, adicionar nas constantes:
   - `EVENTS`
   - `CAROUSEL_SLOTS`
   - `EVENT_IMAGE_DIR`
2. Criar `{evento}.html` (campanha) e `{evento}_prestacao_de_contas.html` (tabela)
3. A prestação busca dados em `admin/api/donations.php?event={slug}`
4. Adicionar link no menu de todas as páginas (dentro de `#grp-atividades`)

---

## Contatos / dados fixos usados no código

| Campo | Valor |
|---|---|
| WhatsApp | (22) 99862-6059 |
| E-mail | upupoficial@gmail.com |
| PIX CNPJ | 48.211.513/0001-78 |
| PIX e-mail | upupoficial@gmail.com |
| Banco | Banco do Brasil |
| Endereço | Rua Dr. Antônio de Carvalho Cavalcanti, 248 — Cantinho Fiorello, Natividade-RJ — CEP 28380-000 |
| CNPJ | 48.211.513/0001-78 |

---

## Git

```bash
git pull          # atualizar na VM após push do dev
git status        # ver o que mudou
git add .         # stagear tudo (cuidado com upup.zip e imagens grandes)
git push          # enviar para GitHub
```

Repositório: `https://github.com/mspcris/upup.git`
Branch principal: `main`
