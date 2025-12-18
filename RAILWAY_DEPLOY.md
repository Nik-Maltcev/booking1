# Развёртывание LibreBooking на Railway

Данное руководство описывает процесс развёртывания системы бронирования LibreBooking на платформе Railway с русской локализацией.

## Содержание

- [Требования](#требования)
- [Быстрый старт](#быстрый-старт)
- [Настройка базы данных](#настройка-базы-данных)
- [Переменные окружения](#переменные-окружения)
- [Настройка почты](#настройка-почты)
- [Устранение неполадок](#устранение-неполадок)

## Требования

- Аккаунт на [Railway](https://railway.app)
- Репозиторий с кодом LibreBooking (GitHub, GitLab или Bitbucket)

## Быстрый старт

### Шаг 1: Создание проекта в Railway

1. Войдите в [Railway Dashboard](https://railway.app/dashboard)
2. Нажмите **New Project**
3. Выберите **Deploy from GitHub repo**
4. Авторизуйте Railway для доступа к вашему репозиторию
5. Выберите репозиторий с LibreBooking

### Шаг 2: Добавление базы данных MySQL

1. В проекте нажмите **New** → **Database** → **Add MySQL**
2. Railway автоматически создаст базу данных и установит переменные окружения

### Шаг 3: Настройка переменных окружения

1. Выберите сервис LibreBooking в проекте
2. Перейдите во вкладку **Variables**
3. Добавьте необходимые переменные (см. раздел [Переменные окружения](#переменные-окружения))

### Шаг 4: Деплой

Railway автоматически запустит сборку и деплой после настройки. Процесс занимает 2-5 минут.

### Шаг 5: Получение URL

1. Перейдите во вкладку **Settings** сервиса
2. В разделе **Domains** нажмите **Generate Domain**
3. Скопируйте полученный URL (например: `your-app.up.railway.app`)

## Настройка базы данных

### Автоматическая настройка

При добавлении MySQL в Railway автоматически создаются следующие переменные:
- `MYSQLHOST` - хост базы данных
- `MYSQLDATABASE` - имя базы данных
- `MYSQLUSER` - пользователь
- `MYSQLPASSWORD` - пароль
- `MYSQLPORT` - порт (обычно 3306)

Приложение автоматически использует эти переменные.

### Инициализация схемы базы данных

При первом запуске необходимо инициализировать схему базы данных:

1. Откройте URL приложения в браузере
2. Перейдите по адресу `/Web/install/`
3. Следуйте инструкциям мастера установки
4. Используйте пароль установки из переменной `LB_INSTALL_PASSWORD`

**Важно:** После завершения установки удалите или измените `LB_INSTALL_PASSWORD` для безопасности.

### Ручная инициализация (опционально)

Если требуется ручная инициализация:

1. Подключитесь к базе данных через Railway CLI или внешний клиент
2. Выполните SQL-скрипты из директории `database_schema/`:
   ```sql
   source database_schema/create-schema.sql
   source database_schema/create-data.sql
   ```


## Переменные окружения

### Обязательные переменные

| Переменная | Описание | Пример |
|------------|----------|--------|
| `MYSQL_HOST` | Хост MySQL (или используйте `MYSQLHOST` от Railway) | `containers-us-west-1.railway.app` |
| `MYSQL_DATABASE` | Имя базы данных | `librebooking` |
| `MYSQL_USER` | Пользователь MySQL | `root` |
| `MYSQL_PASSWORD` | Пароль MySQL | `your-password` |

### Рекомендуемые переменные

| Переменная | Описание | По умолчанию |
|------------|----------|--------------|
| `LB_APP_TITLE` | Название приложения | `LibreBooking` |
| `LB_ADMIN_EMAIL` | Email администратора | `admin@example.com` |
| `LB_ADMIN_EMAIL_NAME` | Имя администратора | `LB Administrator` |
| `LB_DEFAULT_LANGUAGE` | Язык по умолчанию | `ru_ru` |
| `LB_DEFAULT_TIMEZONE` | Часовой пояс | `Europe/Moscow` |
| `LB_INSTALL_PASSWORD` | Пароль для мастера установки | (пусто) |

### Переменные для почты (SMTP)

| Переменная | Описание | По умолчанию |
|------------|----------|--------------|
| `LB_MAILER` | Тип отправки почты | `smtp` |
| `LB_SMTP_HOST` | SMTP сервер | (пусто) |
| `LB_SMTP_PORT` | Порт SMTP | `587` |
| `LB_SMTP_SECURE` | Шифрование (`tls` или `ssl`) | `tls` |
| `LB_SMTP_AUTH` | Использовать аутентификацию | `true` |
| `LB_SMTP_USERNAME` | Логин SMTP | (пусто) |
| `LB_SMTP_PASSWORD` | Пароль SMTP | (пусто) |
| `LB_EMAIL_ENABLED` | Включить отправку почты | `true` |
| `LB_EMAIL_FROM_ADDRESS` | Адрес отправителя | (пусто) |
| `LB_EMAIL_FROM_NAME` | Имя отправителя | `LibreBooking` |

### Дополнительные переменные

| Переменная | Описание | По умолчанию |
|------------|----------|--------------|
| `LB_COMPANY_NAME` | Название компании | (пусто) |
| `LB_COMPANY_URL` | URL компании | (пусто) |
| `LB_DEBUG` | Режим отладки | `false` |
| `LB_LOG_LEVEL` | Уровень логирования (`DEBUG`, `INFO`, `WARNING`, `ERROR`) | `ERROR` |
| `LB_INACTIVITY_TIMEOUT` | Таймаут сессии (минуты) | `30` |
| `LB_ALLOW_REGISTRATION` | Разрешить самостоятельную регистрацию | `true` |
| `LB_CAPTCHA_ENABLED` | Включить CAPTCHA | `false` |
| `LB_REQUIRE_EMAIL_ACTIVATION` | Требовать активацию по email | `false` |
| `LB_PUBLIC_SCHEDULES` | Публичный доступ к расписаниям | `true` |
| `LB_PUBLIC_RESERVATIONS` | Публичный доступ к бронированиям | `false` |
| `LB_GUEST_RESERVATIONS` | Разрешить гостевые бронирования | `false` |
| `LB_API_ENABLED` | Включить API | `false` |
| `LB_ATTACHMENTS_ENABLED` | Разрешить вложения | `false` |

## Настройка почты

### Gmail

```
LB_SMTP_HOST=smtp.gmail.com
LB_SMTP_PORT=587
LB_SMTP_SECURE=tls
LB_SMTP_AUTH=true
LB_SMTP_USERNAME=your-email@gmail.com
LB_SMTP_PASSWORD=your-app-password
LB_EMAIL_FROM_ADDRESS=your-email@gmail.com
```

**Примечание:** Для Gmail необходимо создать [пароль приложения](https://support.google.com/accounts/answer/185833).

### Yandex

```
LB_SMTP_HOST=smtp.yandex.ru
LB_SMTP_PORT=465
LB_SMTP_SECURE=ssl
LB_SMTP_AUTH=true
LB_SMTP_USERNAME=your-email@yandex.ru
LB_SMTP_PASSWORD=your-password
LB_EMAIL_FROM_ADDRESS=your-email@yandex.ru
```

### Mail.ru

```
LB_SMTP_HOST=smtp.mail.ru
LB_SMTP_PORT=465
LB_SMTP_SECURE=ssl
LB_SMTP_AUTH=true
LB_SMTP_USERNAME=your-email@mail.ru
LB_SMTP_PASSWORD=your-password
LB_EMAIL_FROM_ADDRESS=your-email@mail.ru
```


## Устранение неполадок

### Ошибка подключения к базе данных

**Симптомы:** Страница показывает "Сервис временно недоступен" или ошибку 503.

**Решения:**

1. **Проверьте переменные окружения:**
   - Убедитесь, что MySQL сервис добавлен в проект
   - Проверьте, что переменные `MYSQL_*` или `MYSQLHOST` установлены

2. **Проверьте статус MySQL:**
   - В Railway Dashboard выберите MySQL сервис
   - Убедитесь, что статус "Running"

3. **Проверьте логи:**
   - Выберите сервис LibreBooking
   - Перейдите во вкладку **Deployments**
   - Нажмите на последний деплой и просмотрите логи

### Белый экран или ошибка 500

**Решения:**

1. **Включите режим отладки:**
   ```
   LB_DEBUG=true
   LB_LOG_LEVEL=DEBUG
   ```

2. **Проверьте логи контейнера** в Railway Dashboard

3. **Убедитесь, что схема БД инициализирована:**
   - Перейдите на `/Web/install/`
   - Выполните установку

### Не отправляются письма

**Решения:**

1. **Проверьте настройки SMTP:**
   - Убедитесь, что все переменные `LB_SMTP_*` заполнены
   - Проверьте правильность пароля

2. **Для Gmail:**
   - Используйте пароль приложения, не основной пароль
   - Убедитесь, что двухфакторная аутентификация включена

3. **Проверьте переменную `LB_EMAIL_ENABLED`:**
   ```
   LB_EMAIL_ENABLED=true
   ```

### Интерфейс на английском языке

**Решения:**

1. **Установите язык по умолчанию:**
   ```
   LB_DEFAULT_LANGUAGE=ru_ru
   ```

2. **Пересоберите контейнер:**
   - В Railway Dashboard нажмите **Redeploy**

### Неправильный часовой пояс

**Решение:**

Установите переменную:
```
LB_DEFAULT_TIMEZONE=Europe/Moscow
```

Доступные часовые пояса для России:
- `Europe/Moscow` - Москва (UTC+3)
- `Europe/Samara` - Самара (UTC+4)
- `Asia/Yekaterinburg` - Екатеринбург (UTC+5)
- `Asia/Novosibirsk` - Новосибирск (UTC+7)
- `Asia/Krasnoyarsk` - Красноярск (UTC+7)
- `Asia/Irkutsk` - Иркутск (UTC+8)
- `Asia/Vladivostok` - Владивосток (UTC+10)

### Ошибка "Permission denied"

**Симптомы:** Ошибки записи в директории uploads или logs.

**Решение:** Эта проблема обычно решается автоматически при запуске контейнера. Если проблема сохраняется:

1. Проверьте логи запуска контейнера
2. Убедитесь, что entrypoint скрипт выполняется корректно

### Медленная работа приложения

**Решения:**

1. **Увеличьте ресурсы в Railway:**
   - Перейдите в Settings сервиса
   - Увеличьте лимиты CPU и RAM

2. **Оптимизируйте базу данных:**
   - Добавьте индексы для часто используемых запросов
   - Очистите старые данные

### Проблемы с SSL/HTTPS

Railway автоматически предоставляет SSL-сертификат для сгенерированных доменов. Если используете собственный домен:

1. Добавьте домен в настройках сервиса
2. Настройте DNS записи согласно инструкциям Railway
3. Дождитесь выпуска сертификата (до 24 часов)

## Полезные ссылки

- [Документация Railway](https://docs.railway.app/)
- [Документация LibreBooking](https://librebooking.org/docs/)
- [Список часовых поясов PHP](https://www.php.net/manual/en/timezones.php)

## Поддержка

При возникновении проблем:

1. Проверьте раздел [Устранение неполадок](#устранение-неполадок)
2. Просмотрите логи в Railway Dashboard
3. Создайте issue в репозитории проекта
