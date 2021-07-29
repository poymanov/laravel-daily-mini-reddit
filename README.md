# Laravel Daily - Mini Reddit

Приложение, повторяющее идеи сервиса [reddit](https://reddit.com). Позволяет создавать сообщества по интересам, добавлять в них публикации и оставлять к ним комментарии.
Подробности в [документации](docs/README.md).

### Установка

Для запуска приложения требуется **Docker** и **Docker Compose**.

Для инициализации приложения выполнить команду:
```
make init
```

### Управление

Запуск:
```
make up
```

Остановка приложения:

```
make down
```

### Интерфейсы

Приложение - http://localhost:8080

Почта (MailHog) - http://localhost:8025

### Тесты

```
make backend-test
```

### Цель проекта

Код написан в образовательных целях в рамках курса [Creating mini-Reddit in Laravel 8](https://laraveldaily.teachable.com/p/creating-mini-reddit-in-laravel-8).
