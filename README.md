# Music Label ERP

### "Система управления бизнес-процессами музыкального лейбла."

## Требования
- PHP 8.1+
- Composer
- Node.js 18+
- MySQL 5.7+ 
- Git

## Установка

1. Клон репозиторий:
   ````
   git clone https://github.com/your-team/music-label-erp.git
   ````
   ````
   cd music-label-erp

2. PHP-зависимости:
    ````
   composer install
3. Node-зависимости:
    ````
   npm install
4. Настройка файла окружения .env
    ````
    cp .env.example .env
    ````
   Параметры подключения к БД
    ````
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=music_label_erp
   DB_USERNAME=root
   DB_PASSWORD=8989babuldXXX8989
   ````
5. Генерация ключа приложения
    ````
   php artisan key:generate
   ````
6. Миграции и заполненение тестовыми данными
    ````
   php artisan migrate --seed
   ````
7. Сборка фронта
    ````
   npm run dev
   ````
8. Запуск сервера
    ````
   php artisan serve
   ````

