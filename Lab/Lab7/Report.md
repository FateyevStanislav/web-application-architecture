## Часть A. PHP‑FPM

### Задание 1. Установка PHP‑FPM
![01-php-version.png](Lab/Lab7/screenshots/01-php-version.png)

### Задание 2. Форма и сообщения на PHP
![02-php-form.png](Lab/Lab7/screenshots/02-php-form.png)
![03-php-messages.png](Lab/Lab7/screenshots/03-php-messages.png)

### Задание 3. Конфиг Nginx для PHP
![04-nginx-php.png](Lab/Lab7/screenshots/04-nginx-php.png)

**Ответ:**  
`fastcgi_pass` идёт к FastCGI‑приложению (PHP‑FPM), а не к CGI‑скрипту через fcgiwrap.  
PHP‑FPM быстрее, потому что использует пул воркеров и не делает `fork + exec` на каждый запрос.

### Задание 4. Shared nothing
![05-shared-nothing.png](Lab/Lab7/screenshots/05-shared-nothing.png)

**Ответ:**  
Счётчик не растёт из‑за shared nothing: каждый запрос PHP стартует с нуля, без общего состояния.

### Задание 5. Блокировка воркеров
![06-php-slow.png](Lab/Lab7/screenshots/06-php-slow.png)

**Ответ:**  
Общее время примерно равно числу занятых воркеров PHP‑FPM; `sleep` «забирает» воркер, новые запросы ждут, параллелизм ломается.

---

## Часть B. FastAPI

### Задание 6. Установка и приложение
![07-api-status.png](Lab/Lab7/screenshots/07-api-status.png)
![08-api-messages.png](Lab/Lab7/screenshots/08-api-messages.png)

### Задание 7. Живой процесс (счётчик)
![09-counter.png](Lab/Lab7/screenshots/09-counter.png)

**Ответ:**  
Счётчик растёт, потому что FastAPI — один живой процесс с общим состоянием, а PHP каждый раз стартует заново.

### Задание 8. Async: 10 запросов за ~2 секунды
![10-async-slow.png](Lab/Lab7/screenshots/10-async-slow.png)

**Ответ:**  
10 async‑запросов по 2 секунды сразу идут параллельно в event loop, поэтому общее время ~2 секунды.

### Задание 9. Блокирующий код убивает event loop
![11-blocking.png](Lab/Lab7/screenshots/11-blocking.png)

**Ответ:**  
`/api/slow` — async, `/api/slow-blocking` — синхронный `time.sleep()`, который блокирует event loop, запросы выполняются последовательно.

### Задание 10. Swagger
![12-swagger.png](Lab/Lab7/screenshots/12-swagger.png)

### Задание 11. systemd‑сервис
![13-systemd.png](Lab/Lab7/screenshots/13-systemd.png)

### Задание 12. Nginx proxy_pass
![14-nginx-api.png](Lab/Lab7/screenshots/14-nginx-api.png)

**Ответ:**  
`fastcgi_pass` — к FastCGI (PHP‑FPM), `proxy_pass` — к HTTP‑серверу (Uvicorn); для PHP нужна FastCGI‑проксировка, для Python — HTTP‑проксирование.

---

## Часть C. Сравнение

### Задание 13. Два формата
![15-compare.png](Lab/Lab7/screenshots/15-compare.png)

**Ответ:**  
`messages.php` — HTML для браузера, `/api/messages` — JSON для API‑клиентов; первый для рендера, второй для программной обработки.

### Задание 14. Процессы
![16-processes.png](Lab/Lab7/screenshots/16-processes.png)
