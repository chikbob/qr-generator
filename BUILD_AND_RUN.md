# Сборка и запуск приложения QR Generator

Полное руководство по компиляции, сборке и запуску приложения в Docker контейнере.

## 📋 Требования

- Docker (версия 20.10+)
- Docker Compose (версия 2.0+)
- Git

## 🚀 Быстрый старт

### 1. Клонирование репозитория

```bash
git clone <repository-url>
cd qr-generator-app
```

### 2. Подготовка окружения

```bash
# Скопировать пример .env файла
cp .env.example .env

# Убедиться, что используются правильные настройки базы данных в .env:
# DB_HOST=db
# DB_DATABASE=qr-generator-app
# DB_USERNAME=root
# DB_PASSWORD=secret
```

### 3. Сборка и запуск контейнеров

```bash
# Полная пересборка всех контейнеров с нуля
docker-compose down -v
docker-compose build --no-cache
docker-compose up -d

# Или просто запуск (сборка будет, если образ не существует)
docker-compose up -d --build
```

### 4. Инициализация приложения

```bash
# Генерирование ключа приложения (если его нет)
docker-compose exec app php artisan key:generate

# Установка зависимостей NPM
docker-compose exec app npm install

# Компиляция фронтенда (Vite)
docker-compose exec app npm run build

# Запуск миграций и заполнение БД
docker-compose exec app php artisan migrate:fresh --seed

# Очистка кэша
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
```

---

## 🔧 Команды для разработки и сборки

### Запуск приложения

```bash
# Запуск всех контейнеров
docker-compose up -d

# Просмотр логов приложения
docker-compose logs -f app

# Перезагрузка приложения
docker-compose restart app

# Остановка приложения
docker-compose down
```

### Работа с зависимостями

```bash
# Установка PHP зависимостей (Composer)
docker-compose exec app composer install

# Обновление PHP зависимостей
docker-compose exec app composer update

# Установка NPM зависимостей
docker-compose exec app npm install

# Обновление NPM зависимостей
docker-compose exec app npm update

# Проверка уязвимостей
docker-compose exec app npm audit
docker-compose exec app composer audit
```

### Компиляция фронтенда

```bash
# Построение фронтенда для production
docker-compose exec app npm run build

# Построение фронтенда в development режиме
docker-compose exec app npm run dev

# Запуск watcher для разработки (с горячей перезагрузкой)
docker-compose exec app npm run dev

# Проверка кода (Vite предварительная сборка)
docker-compose exec app npm run type-check
```

### Работа с базой данных

```bash
# Запуск миграций
docker-compose exec app php artisan migrate

# Откат последней миграции
docker-compose exec app php artisan migrate:rollback

# Откат всех миграций
docker-compose exec app php artisan migrate:reset

# Запуск миграций с заполнением БД
docker-compose exec app php artisan migrate:fresh --seed

# Создание резервной копии БД
docker-compose exec db mysqldump -u root -psecret qr-generator-app > backup_$(date +%Y%m%d_%H%M%S).sql

# Восстановление из резервной копии
docker-compose exec -T db mysql -u root -psecret qr-generator-app < backup.sql

# Доступ к MySQL консоли
docker-compose exec db mysql -u root -psecret qr-generator-app
```

### Тестирование

```bash
# Запуск всех тестов
docker-compose exec app php artisan test

# Запуск конкретного тестового файла
docker-compose exec app php artisan test tests/Feature/HealthCheckTest.php

# Запуск тестов с покрытием кода
docker-compose exec app php artisan test --coverage
```

### Оптимизация и кэширование

```bash
# Кэширование конфигурации
docker-compose exec app php artisan config:cache

# Кэширование маршрутов
docker-compose exec app php artisan route:cache

# Кэширование представлений
docker-compose exec app php artisan view:cache

# Очистка всех кэшей
docker-compose exec app php artisan cache:clear

# Оптимизация автозагрузчика Composer
docker-compose exec app composer dump-autoload -o

# Очистка неиспользуемых файлов
docker-compose exec app php artisan tinker
# Внутри tinker: Cache::flush()
```

### Очистка хранилища

```bash
# Очистка логов
docker-compose exec app rm -rf storage/logs/*

# Очистка временных файлов
docker-compose exec app php artisan storage:link

# Полная очистка хранилища
docker-compose exec app rm -rf storage/app/public/*
```

---

## 📦 Компиляция фронтенда (подробно)

### Vite конфигурация

Приложение использует **Vite** для сборки фронтенда. Конфигурация находится в `vite.config.js`.

```bash
# Сборка для production (оптимизированная, минифицированная)
docker-compose exec app npm run build

# Вывод:
# ✓ 481 modules transformed.
# public/build/manifest.json       ...
# public/build/assets/app-*.js     ...
# ✓ built in 4.60s
```

После сборки файлы находятся в `/public/build/` и подключаются автоматически.

### Добавление terser для минификации JavaScript

```bash
docker-compose exec app npm install terser --save-dev
docker-compose exec app npm run build
```

---

## 🗄️ PHPMyAdmin - управление БД

Доступ к веб-интерфейсу PHPMyAdmin:

```
URL: http://localhost:8081
Пользователь: root
Пароль: secret
Сервер: db
```

**Данные для подключения:**
- **Хост:** db
- **Пользователь:** root
- **Пароль:** secret
- **База данных:** qr-generator-app

---

## 🌐 Доступ к приложению

```
URL приложения: http://localhost:8080
PHPMyAdmin: http://localhost:8081
API: http://localhost:8080/api
```

---

## 📝 Структура проекта

```
├── app/                    # PHP код приложения
│   ├── Http/              # Контроллеры, middleware
│   ├── Models/            # Eloquent модели
│   └── Helpers/           # Вспомогательные функции
├── resources/             # Фронтенд ресурсы
│   ├── js/               # Vue.js компоненты (Inertia)
│   ├── css/              # SCSS стили
│   └── views/            # Blade шаблоны
├── database/              # Миграции и seeders
│   ├── migrations/       # Миграции БД
│   └── seeders/          # Seeders для заполнения БД
├── public/
│   ├── build/            # Скомпилированные фронтенд файлы (Vite)
│   └── index.php         # Точка входа приложения
├── routes/               # Определение маршрутов
│   └── web.php          # Веб маршруты
├── Dockerfile           # Конфигурация Docker образа
├── docker-compose.yml   # Конфигурация контейнеров
├── vite.config.js      # Конфигурация Vite
├── tailwind.config.js  # Конфигурация Tailwind CSS
├── package.json        # NPM зависимости
└── composer.json       # PHP зависимости
```

---

## 🐛 Решение проблем

### Ошибка: "Vite manifest not found"

```bash
# Необходимо собрать фронтенд:
docker-compose exec app npm install
docker-compose exec app npm run build
```

### Ошибка подключения к БД

```bash
# Проверить, что контейнер БД запущен и здоров:
docker-compose ps

# Проверить логи БД:
docker-compose logs db

# Перезапустить контейнер БД:
docker-compose restart db
docker-compose exec app php artisan migrate
```

### Ошибка: "The helper class InertiaHelpers is missing"

```bash
# Regенерировать автозагрузчик Composer:
docker-compose exec app composer dump-autoload
docker-compose restart app
```

### Проблемы с правами доступа

```bash
# Исправить права доступа к хранилищу:
docker-compose exec app sudo chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app sudo chmod -R 775 storage bootstrap/cache
```

### Очистка всего и начало заново

```bash
# Остановить все контейнеры и удалить томы
docker-compose down -v

# Удалить все образы приложения
docker rmi qr-generator-fullstack-app-app

# Пересборка с нуля
docker-compose build --no-cache

# Запуск
docker-compose up -d
```

---

## 📊 Мониторинг и отладка

### Просмотр логов

```bash
# Логи приложения (последние 50 строк)
docker-compose logs -f --tail=50 app

# Логи базы данных
docker-compose logs -f db

# Логи PHPMyAdmin
docker-compose logs phpmyadmin

# Все логи
docker-compose logs -f
```

### Статус контейнеров

```bash
# Просмотр статуса всех контейнеров
docker-compose ps

# Подробная информация о контейнере
docker inspect qr-generator-fullstack-app-app

# Использование ресурсов
docker stats
```

### Доступ к контейнерам

```bash
# Bash shell в контейнере приложения
docker-compose exec app bash

# PHP интерактивная консоль (Tinker)
docker-compose exec app php artisan tinker

# MySQL консоль
docker-compose exec db mysql -u root -psecret qr-generator-app
```

---

## 🔐 Переменные окружения (.env)

Основные переменные в `.env`:

```env
# Приложение
APP_NAME=Qr-generator
APP_ENV=local
APP_DEBUG=true
APP_KEY=base64:...
APP_URL=http://localhost:8080

# База данных
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=qr-generator-app
DB_USERNAME=root
DB_PASSWORD=secret

# Кэш и сессии
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# Mail (опционально)
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
```

---

## 📦 Версии зависимостей

- **PHP:** 8.2
- **Node.js:** 20
- **Laravel:** 10.x
- **Inertia.js:** 0.6.x
- **Vue.js:** 3.x
- **Tailwind CSS:** 3.x
- **Vite:** 4.x
- **MySQL:** 8.0

---

## ✅ Чек-лист развертывания

- [ ] Docker и Docker Compose установлены
- [ ] .env файл скопирован и настроен
- [ ] Все контейнеры запущены (`docker-compose ps`)
- [ ] Миграции запущены (`docker-compose exec app php artisan migrate`)
- [ ] Фронтенд собран (`docker-compose exec app npm run build`)
- [ ] Приложение доступно на http://localhost:8080
- [ ] БД заполнена данными (`docker-compose exec app php artisan migrate:fresh --seed`)
- [ ] PHPMyAdmin доступен на http://localhost:8081

---

## 🆘 Поддержка

Если возникли проблемы:

1. Проверьте логи: `docker-compose logs -f app`
2. Убедитесь, что все порты свободны (8080, 3306, 8081, 5173)
3. Перезагрузите Docker: `docker-compose down && docker-compose up -d`
4. Полная очистка: `docker-compose down -v && docker-compose up -d --build`
