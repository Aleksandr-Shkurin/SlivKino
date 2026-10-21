# DevOps: Docker и локальный запуск

## Что где лежит

- `Dockerfile` — образ приложения.
- `compose.yaml` — локальный запуск без bind mount, ближе к production.
- `compose.dev.yaml` — режим разработки с монтированием исходников и `node --watch`.
- `Dockerfile.dockerignore` — исключения для build context. Файл привязан к `Dockerfile`, поэтому его можно держать рядом с ним внутри `devops/`.

Оба Compose-файла собирают контекст из корня проекта (`context: ..`), потому что `package.json`, `server.js`, HTML, CSS и JS находятся в корне и рядом с ним.

## Разработка

```bash
docker compose -f devops/compose.dev.yaml up --build
```

Открыть `http://localhost:3000/`, панель `http://localhost:3000/admin.html`. Статические файлы отдаются из bind mount, изменение `server.js` вызывает перезапуск Node.

```bash
# логи и статус — отдельный терминал
docker compose -f devops/compose.dev.yaml logs -f app
docker compose -f devops/compose.dev.yaml ps

# остановить
docker compose -f devops/compose.dev.yaml down
```

Dev-база хранится в `slivkino_dev_data`. Для удаления именно dev-данных используйте осторожно:

```bash
docker compose -f devops/compose.dev.yaml down -v
```

Это удалит volume базы и volume с `node_modules` для dev-окружения. Команду нельзя использовать, если данные нужны.

## Production-like локальный запуск

```bash
docker compose -f devops/compose.yaml up -d --build
docker compose -f devops/compose.yaml logs -f app
docker compose -f devops/compose.yaml down
```

Основная база хранится в `slivkino_data`. Обычная команда `down` данные не удаляет; `down -v` удаляет и volume.

## Что важно перед размещением в интернете

Это локальная конфигурация, не полноценный production rollout. Админ-панель сейчас без аутентификации. Перед внешней публикацией нужны как минимум аутентификация и авторизация для admin API, TLS/reverse proxy, резервные копии SQLite и проверенный процесс восстановления. Не храните реальную базу, пароли или `.env` в Git.

## Если меняются зависимости Node.js

Если разработчик изменил `package.json`, обновите зависимости в dev-volume:

```bash
docker compose -f devops/compose.dev.yaml run --rm app npm install
docker compose -f devops/compose.dev.yaml up --build
```

Изменения зависимостей стоит коммитить вместе с `package-lock.json`, когда lock-файл будет сгенерирован. Не удаляйте все volumes только ради обновления зависимостей: в dev-volume также хранится SQLite-база.
