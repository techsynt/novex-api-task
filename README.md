# novex-api-task

Это тестовое задание (API для CRUD операций над пользователем) от компании Новэкс.

На локальной машине нужен установленный:  git, docker и docker compose
Если их нет -пригодятся следующие ссылки:

- https://docs.docker.com/engine/install/
- https://docs.docker.com/engine/install/linux-postinstall/ (для линукс-пользователей)
- https://git-scm.com/downloads

Запуск приложения: Выполняем в терминале поочередно команды ниже 

```

docker compose up --build -d
docker compose exec php composer install
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction

```

После запуска - api-doc будет доступен по адресу http://localhost:8080/api/doc
