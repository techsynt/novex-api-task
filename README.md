# novex-api-task

Это тестовое задание (API для CRUD операций над пользователем) от компании Новэкс.

На локальной машине нужен установленный:  composer, git, docker и docker compose
Если их нет -пригодятся следующие ссылки:

- https://docs.docker.com/engine/install/
- https://docs.docker.com/engine/install/linux-postinstall/ (для линукс-пользователей)
- https://git-scm.com/downloads

Запуск приложения: Выполняем сначала билд через докер композ командой ниже

```

docker compose up --build -d
```

затем в контейнере php запускаем команды:

```
composer install
php bin/console doctrine:migrations:migrate
```

После запуска - api-doc будет доступен по адресу http://localhost:8080/api
