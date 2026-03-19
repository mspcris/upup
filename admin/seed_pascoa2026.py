#!/usr/bin/env python3
"""
Seed: popula o banco SQLite com os itens da Páscoa Solidária 2026.
Execute: python3 admin/seed_pascoa2026.py
"""
import sqlite3, os

DB = os.path.join(os.path.dirname(__file__), "data", "upup.db")
os.makedirs(os.path.dirname(DB), exist_ok=True)

# (item, valor, responsavel, pagou, tipo_doacao, ordem)
# pagou='sim' → QUITADO   responsavel='' → PENDENTE   responsavel+pagou=nao → ASSUMIDO
# tipo_doacao: 'produto' (padrão) ou 'dinheiro'
DADOS = [
    # (item, valor, responsavel, pagou, tipo_doacao, ordem)
    # ── QUITADOS (verde) ──────────────────────────────────────
    ("50 caixas de BOMBOM",             "R$ 650,00", "Roberto Faria",    "sim", "produto",  10),
    ("08 caixas de BOMBOM",             "R$ 100,00", "Romildo Jose",     "sim", "produto",  20),
    ("23 caixas de BOMBOM",             "R$ 300,00", "Pr. Eder",         "sim", "produto",  30),
    ("08 caixas de BOMBOM",             "R$ 100,00", "Jose Eugenio",     "sim", "produto",  40),
    ("05 caixas de BOMBOM",             "R$ 65,00",  "Prof. ROBERTO",    "sim", "produto",  50),
    ("07 Caixas de BOMBOM",             "R$ 100,00", "Miss. Jaqueline G","sim", "produto",  60),
    ("8kg trigo com fermento",          "R$ 56,00",  "Fabiana Sobreira", "sim", "produto",  70),
    ("2L oleo",                         "R$ 10,00",  "Romario",          "sim", "produto",  80),
    ("10kg de açúcar",                  "R$ 36,00",  "Romario",          "sim", "produto",  90),
    ("150 palitos algodão doce",        "R$ 20,00",  "Janaina Tupini",   "sim", "produto", 100),
    ("150 palitos algodão doce",        "R$ 20,00",  "Paulo de Moura",   "sim", "produto", 110),
    ("02 saco de milho de pipoca",      "R$ 20,00",  "Romario",          "sim", "produto", 120),
    ("02 pct. geladinhos (chup chup)",  "R$ 20,00",  "Adalgisa Righetti","sim", "produto", 130),
    ("100 balões de gas nº 9 colorida", "R$ 26,00",  "Caio RJ",          "sim", "produto", 140),
    ("100 balões de gas nº 9 colorida", "R$ 26,00",  "Caio RJ",          "sim", "produto", 150),
    ("100 balões de gas nº 9 colorida", "R$ 26,00",  "Caio RJ",          "sim", "produto", 160),

    # ── PENDENTES (branco — sem responsável) ───────────────────
    ("15 caixas de BOMBOM",             "R$ 195,00", "",                 "nao", "produto", 170),
    ("10 Caixas de BOMBOM",             "R$ 130,00", "",                 "nao", "produto", 180),
    ("5 engradado de refrigerante",     "R$ 175,00", "",                 "nao", "produto", 190),
    ("3 engradado de refrigerante",     "R$ 105,00", "",                 "nao", "produto", 200),
    ("500 copos descartáveis",          "R$ 48,00",  "",                 "nao", "produto", 210),
    ("08 saco de milho de pipoca",      "R$ 40,00",  "",                 "nao", "produto", 220),
    ("08 pct. geladinhos (chup chup)",  "R$ 70,00",  "",                 "nao", "produto", 230),

    # ── ASSUMIDOS (amarelo — comprometido mas não quitado) ──────
    ("05 Caixas de BOMBOM",             "R$ 65,00",  "Lourdinha Tupini", "nao", "produto", 240),
    ("15 caixas de BOMBOM",             "R$ 200,00", "Richard RJ",       "nao", "produto", 250),
    ("05 Caixas de BOMBOM",             "R$ 65,00",  "GI",               "nao", "produto", 260),
    ("10kg de açúcar",                  "R$ 36,00",  "Lourdinha Tupini", "nao", "produto", 270),
    ("2L oleo",                         "R$ 10,00",  "MAYA",             "nao", "produto", 280),
    ("2L oleo",                         "R$ 10,00",  "LULU",             "nao", "produto", 290),
    ("5 cx creme de leite",             "R$ 18,00",  "STEFANI",          "nao", "produto", 300),
    ("1 engradado de refrigerante",     "R$ 35,00",  "LULU",             "nao", "produto", 310),
    ("1 engradado de refrigerante",     "R$ 35,00",  "Thays Sousa",      "nao", "produto", 320),
    ("1 engradado de refrigerante",     "R$ 35,00",  "STEFANI",          "nao", "produto", 330),
    ("2 engradado de refrigerante",     "R$ 35,00",  "SARA RJ (esther)", "nao", "produto", 340),
    ("1 cartela de ovos",               "R$ 15,00",  "STEFANI",          "nao", "produto", 350),
    ("2 cartela de ovos",               "R$ 30,00",  "savio",            "nao", "produto", 360),
    ("2 cartela de ovos",               "R$ 30,00",  "Pastora IONA",     "nao", "produto", 370),
    ("2 cx de chantilly amélia",        "R$ 40,00",  "ESTHER",           "nao", "produto", 380),
    ("10 lata leite condensado",        "R$ 60,00",  "Valeria Zanelli",  "nao", "produto", 390),
    ("500 folhas de ofício",            "R$ 30,00",  "Gisele Campel",    "nao", "produto", 400),
    ("1 caixa chocolate 50% cacau",     "R$ 20,00",  "miss. Tatiana",    "nao", "produto", 410),
    ("1 pula pula",                     "R$ 150,00", "Gisele Campel",    "nao", "produto", 420),
    ("2 pula pula",                     "R$ 300,00", "Sandra",           "nao", "produto", 430),
    ("casinha de bolinha",              "R$ 80,00",  "Raphael",          "nao", "produto", 440),
    ("Mesas para Decoração",            "R$ 150,00", "Nicole",           "nao", "produto", 450),
    ("Maquina de Algodão Doce",         "R$ 150,00", "Gisele Campel",    "nao", "produto", 460),
]

con = sqlite3.connect(DB)
con.execute("PRAGMA journal_mode=WAL")
con.execute("""
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
    )
""")

# Migração: adiciona coluna se banco já existia
try:
    con.execute("ALTER TABLE donations ADD COLUMN tipo_doacao TEXT NOT NULL DEFAULT 'produto'")
    con.commit()
except Exception:
    pass  # Coluna já existe

# Limpa somente os dados de pascoa2026 antes de reinserir
con.execute("DELETE FROM donations WHERE event = 'pascoa2026'")

con.executemany(
    "INSERT INTO donations (event, item, valor, responsavel, pagou, tipo_doacao, ordem) VALUES (?,?,?,?,?,?,?)",
    [("pascoa2026", d[0], d[1], d[2], d[3], d[4], d[5]) for d in DADOS]
)
con.commit()

total = con.execute("SELECT COUNT(*) FROM donations WHERE event='pascoa2026'").fetchone()[0]
con.close()

print(f"✅ {total} itens inseridos em {DB}")
