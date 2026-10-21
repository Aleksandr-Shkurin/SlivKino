# SlivKino

Локальный сайт на Node.js + Express + SQLite.

## Быстрый запуск для разработки (Docker)

Требуются Git и Docker с Docker Compose v2.

Из корня проекта выполните:

```bash
docker compose -f devops/compose.dev.yaml up --build
```

Откройте <http://localhost:3000/>. Админ-панель: <http://localhost:3000/admin.html>.

В dev-режиме исходники подключены с диска: изменения HTML/CSS/браузерного JavaScript доступны без пересборки, а изменение `server.js` перезапускает Node.js. Данные SQLite сохраняются в отдельном Docker volume для разработки.

Остановить: `Ctrl+C`, затем:

```bash
docker compose -f devops/compose.dev.yaml down
```

## Запуск без Docker

Нужен Node.js 22 или совместимая версия с поддержкой `node --watch`.

```bash
npm install
npm run dev
```

Для обычного запуска без автоматического перезапуска сервера: `npm start`.

## Запуск в production-like режиме

Этот конфиг не монтирует исходники с хоста, а собирает их в образ:

```bash
docker compose -f devops/compose.yaml up -d --build
```

Откройте <http://localhost:3000/>. Логи: `docker compose -f devops/compose.yaml logs -f app`. Остановить: `docker compose -f devops/compose.yaml down`.

Dev и production-like конфиги используют разные SQLite volumes. Порт опубликован только на `127.0.0.1` и не предназначен для доступа из локальной сети.

## Документация

- [DevOps и Docker](devops/README-DEVOPS.md)
- [SQLite и админ-панель](devops/README-DB.md)
- [Правила совместной разработки](CONTRIBUTING.md)

## Безопасность

Текущая админ-панель не имеет полноценной авторизации. Не публикуйте этот проект в интернет до добавления входа, проверки прав доступа и резервного копирования базы.
