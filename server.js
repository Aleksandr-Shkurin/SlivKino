const express = require('express');
const Database = require('better-sqlite3');
const path = require('path');
const fs = require('fs');
const vm = require('vm');

const app = express();
const PORT = process.env.PORT || 3000;
const DB_PATH = process.env.DB_PATH || path.join(__dirname, 'slivkino.sqlite');
fs.mkdirSync(path.dirname(DB_PATH), { recursive: true });
const db = new Database(DB_PATH);
db.pragma('journal_mode = WAL');
db.exec(`CREATE TABLE IF NOT EXISTS app_state (id INTEGER PRIMARY KEY CHECK (id = 1), payload TEXT NOT NULL, updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)`);
app.use(express.json({ limit: '10mb' }));
app.use(express.static(__dirname));

function loadInitialState() {
  // Эти файлы используются и браузером, и сервером для начальных данных.
  // При серверной загрузке отключаем браузерную синхронизацию через fetch('/api/state').
  const context = {
    fetch: () => Promise.resolve({ ok: false })
  };
  const movieFile = fs.readFileSync(path.join(__dirname, 'js/movies-data.js'), 'utf8');
  const cinemaFile = fs.readFileSync(path.join(__dirname, 'js/cinemas-data.js'), 'utf8');
  vm.runInNewContext(movieFile, context);
  vm.runInNewContext(cinemaFile, context);
  return {
    movies: JSON.parse(JSON.stringify(context.moviesDatabase)),
    cinemas: JSON.parse(JSON.stringify(context.cinemasDatabase)),
    halls: JSON.parse(JSON.stringify(context.hallsConfig || {})),
    bookings: []
  };
}
const getRow = db.prepare('SELECT payload FROM app_state WHERE id = 1');
if (!getRow.get()) {
  db.prepare('INSERT INTO app_state (id, payload) VALUES (1, ?)').run(JSON.stringify(loadInitialState()));
}
app.get('/api/state', (req, res) => {
  try { res.json(JSON.parse(getRow.get().payload)); }
  catch (e) { res.status(500).json({ error: 'Не удалось прочитать базу данных' }); }
});
app.put('/api/state', (req, res) => {
  const state = req.body;
  if (!state || typeof state !== 'object' || !state.movies || !Array.isArray(state.cinemas) || typeof state.halls !== 'object') {
    return res.status(400).json({ error: 'Некорректный формат данных' });
  }
  try {
    db.prepare(`INSERT INTO app_state (id, payload, updated_at) VALUES (1, ?, CURRENT_TIMESTAMP)
      ON CONFLICT(id) DO UPDATE SET payload = excluded.payload, updated_at = CURRENT_TIMESTAMP`)
      .run(JSON.stringify({
        movies: state.movies,
        cinemas: state.cinemas,
        halls: state.halls,
        bookings: Array.isArray(state.bookings) ? state.bookings : []
      }));
    res.json({ ok: true, savedAt: new Date().toISOString() });
  } catch (e) { res.status(500).json({ error: 'Ошибка сохранения' }); }
});
app.get('/api/health', (req, res) => res.json({ ok: true, database: 'sqlite' }));
app.listen(PORT, () => console.log(`SlivKino запущен: http://localhost:${PORT}`));
