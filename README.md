# Mini-CRM Project

Система для сбора и обработки заявок с сайтов через универсальный Blade-виджет. Проект реализован на Laravel 13 с соблюдением принципов SOLID, MVC и стандартов PSR-12.

## Технологический стек

- **PHP:** 8.4
- **Framework:** Laravel 13
- **Environment:** Laravel Sail (Docker)
- **Packages:** - `spatie/laravel-permission` — роли и права (Менеджер).
    - `spatie/laravel-medialibrary` — работа с вложениями к заявкам.
- **Frontend:** Blade, Breeze, AJAX (Fetch API/Axios).

---

## Установка и запуск

1. **Клонирование репозитория:**

    ```bash
    git clone [https://github.com/Sayrendil/mini-crm.git](https://github.com/Sayrendil/mini-crm.git)
    cd mini-crm
    ```

2. **Настройка окружения и установка зависимостей:**

    ```bash
    cp .env.example .env

    # Установка composer-пакетов через Docker
    docker run --rm \
        -u "$(id -u):$(id -g)" \
        -v "$(pwd):/var/www/html" \
        -w /var/www/html \
        laravelsail/php84-composer:latest \
        composer install --ignore-platform-reqs
    ```

3. **Запуск Laravel Sail:**

    ```bash
    ./vendor/bin/sail up -d
    ```

4. **Настройка Alias:**

    ```bash
    alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'
    ```

5. **Инициализация приложения:**
    ```bash
    sail artisan key:generate
    sail artisan migrate --seed
    sail npm install
    sail npm run build
    ```

---

## Тестовые данные (Seeders)

Для входа в админ-панель:

- **URL:** `http://localhost/login`
- **Email:** `admin@gmail.com`
- **Password:** `password`

---

## Интеграция виджета

Для встраивания формы на сторонние сайты используйте `iframe`:

```html
<iframe
    src="http://localhost/widget"
    width="400"
    height="600"
    frameborder="0"
></iframe>
```

## API Reference

Все ответы API возвращаются через `TicketResource`:

---Создание заявки
Endpoint: POST /api/tickets/store
Content-Type: multipart/form-data (для поддержки файлов)

Payload:

```json
{
    "name": "Иван Иванов",
    "email": "ivan@example.com",
    "phone": "+79001234567",
    "subject": "Техподдержка",
    "message": "Описание проблемы...",
    "files[]": "file_binary_data"
}
```
