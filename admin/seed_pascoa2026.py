#!/usr/bin/env python3
"""
Seed: popula o banco SQLite com os itens da Páscoa Solidária 2026.
Execute: python3 admin/seed_pascoa2026.py
"""
import sqlite3, os

DB = os.path.join(os.path.dirname(__file__), "data", "upup.db")
os.makedirs(os.path.dirname(DB), exist_ok=True)

# (item, valor, responsavel, pagou, ordem)
# pagou='sim' → QUITADO (verde)   responsavel='' → PENDENTE (branco)   responsavel+pagou=nao → ASSUMIDO (amarelo)
DADOS = [
    # ── QUITADOS (verde) ──────────────────────────────────────
    ("50 caixas de BOMBOM",             "R$ 650,00", "Roberto Faria",    "sim",  10),
    ("08 caixas de BOMBOM",             "R$ 100,00", "Romildo Jose",     "sim",  20),
    ("23 caixas de BOMBOM",             "R$ 300,00", "Pr. Eder",         "sim",  30),
    ("08 caixas de BOMBOM",             "R$ 100,00", "Jose Eugenio",     "sim",  40),
    ("05 caixas de BOMBOM",             "R$ 65,00",  "Prof. ROBERTO",    "sim",  50),
    ("07 Caixas de BOMBOM",             "R$ 100,00", "Miss. Jaqueline G","sim",  60),
    ("8kg trigo com fermento",          "R$ 56,00",  "Fabiana Sobreira", "sim",  70),
    ("2L oleo",                         "R$ 10,00",  "Romario",          "sim",  80),
    ("10kg de açúcar",                  "R$ 36,00",  "Romario",          "sim",  90),
    ("150 palitos algodão doce",        "R$ 20,00",  "Janaina Tupini",   "sim", 100),
    ("150 palitos algodão doce",        "R$ 20,00",  "Paulo de Moura",   "sim", 110),
    ("02 saco de milho de pipoca",      "R$ 20,00",  "Romario",          "sim", 120),
    ("02 pct. geladinhos (chup chup)",  "R$ 20,00",  "Adalgisa Righetti","sim", 130),
    ("100 balões de gas nº 9 colorida", "R$ 26,00",  "Caio RJ",          "sim", 140),
    ("100 balões de gas nº 9 colorida", "R$ 26,00",  "Caio RJ",          "sim", 150),
    ("100 balões de gas nº 9 colorida", "R$ 26,00",  "Caio RJ",          "sim", 160),

    # ── PENDENTES (branco — sem responsável) ───────────────────
    ("15 caixas de BOMBOM",             "R$ 195,00", "",                 "nao", 170),
    ("10 Caixas de BOMBOM",             "R$ 130,00", "",                 "nao", 180),
    ("5 engradado de refrigerante",     "R$ 175,00", "",                 "nao", 190),
    ("3 engradado de refrigerante",     "R$ 105,00", "",                 "nao", 200),
    ("500 copos descartáveis",          "R$ 48,00",  "",                 "nao", 210),
    ("08 saco de milho de pipoca",      "R$ 40,00",  "",                 "nao", 220),
    ("08 pct. geladinhos (chup chup)",  "R$ 70,00",  "",                 "nao", 230),

    # ── ASSUMIDOS (amarelo — comprometido mas não quitado) ──────
    ("05 Caixas de BOMBOM",             "R$ 65,00",  "Lourdinha Tupini", "nao", 240),
    ("15 caixas de BOMBOM",             "R$ 200,00", "Richard RJ",       "nao", 250),
    ("05 Caixas de BOMBOM",             "R$ 65,00",  "GI",               "nao", 260),
    ("10kg de açúcar",                  "R$ 36,00",  "Lourdinha Tupini", "nao", 270),
    ("2L oleo",                         "R$ 10,00",  "MAYA",             "nao", 280),
    ("2L oleo",                         "R$ 10,00",  "LULU",             "nao", 290),
    ("5 cx creme de leite",             "R$ 18,00",  "STEFANI",          "nao", 300),
    ("1 engradado de refrigerante",     "R$ 35,00",  "LULU",             "nao", 310),
    ("1 engradado de refrigerante",     "R$ 35,00",  "Thays Sousa",      "nao", 320),
    ("1 engradado de refrigerante",     "R$ 35,00",  "STEFANI",          "nao", 330),
    ("2 engradado de refrigerante",     "R$ 35,00",  "SARA RJ (esther)", "nao", 340),
    ("1 cartela de ovos",               "R$ 15,00",  "STEFANI",          "nao", 350),
    ("2 cartela de ovos",               "R$ 30,00",  "savio",            "nao", 360),
    ("2 cartela de ovos",               "R$ 30,00",  "Pastora IONA",     "nao", 370),
    ("2 cx de chantilly amélia",        "R$ 40,00",  "ESTHER",           "nao", 380),
    ("10 lata leite condensado",        "R$ 60,00",  "Valeria Zanelli",  "nao", 390),
    ("500 folhas de ofício",            "R$ 30,00",  "Gisele Campel",    "nao", 400),
    ("1 caixa chocolate 50% cacau",     "R$ 20,00",  "miss. Tatiana",    "nao", 410),
    ("1 pula pula",                     "R$ 150,00", "Gisele Campel",    "nao", 420),
    ("2 pula pula",                     "R$ 300,00", "Sandra",           "nao", 430),
    ("casinha de bolinha",              "R$ 80,00",  "Raphael",          "nao", 440),
    ("Mesas para Decoração",            "R$ 150,00", "Nicole",           "nao", 450),
    ("Maquina de Algodão Doce",         "R$ 150,00", "Gisele Campel",    "nao", 460),
]

con = sqlite3.connect(DB)
con.execute("PRAGMA journal_mode=WAL")
con.execute("""
    CREATE TABLE IF NOT EXISTS donations (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        event TEXT NOT NULL DEFAULT 'pascoa2026',
        item TEXT NOT NULL,
        valor TEXT,
        responsavel TEXT,
        pagou TEXT NOT NULL DEFAULT 'nao',
        ordem INTEGER NOT NULL DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )
""")

# Limpa somente os dados de pascoa2026 antes de reinserir
con.execute("DELETE FROM donations WHERE event = 'pascoa2026'")

con.executemany(
    "INSERT INTO donations (event, item, valor, responsavel, pagou, ordem) VALUES (?,?,?,?,?,?)",
    [("pascoa2026", d[0], d[1], d[2], d[3], d[4]) for d in DADOS]
)
con.commit()

total = con.execute("SELECT COUNT(*) FROM donations WHERE event='pascoa2026'").fetchone()[0]
con.close()

print(f"✅ {total} itens inseridos em {DB}")
