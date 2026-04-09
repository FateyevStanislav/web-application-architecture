## Часть A. MySQL — установка и настройка

### Задание 1. Установка MySQL
![01-mysql-status.png](screenshots/01-mysql-status.png)

### Задание 2. База данных и пользователь
![02-db-charset.png](screenshots/02-db-charset.png)

**Ответ:**  
`utf8mb4` нужен, потому что MySQL‑`utf8` поддерживает только до 3 байт на символ и не покрывает все Unicode (например, эмодзи); `utf8mb4` использует до 4 байт и полностью покрывает Unicode.  
Collation задаёт правила сравнения и сортировки строк; `utf8mb4_unicode_ci` даёт более точное Unicode‑согласованное сравнение, корректно работающее с мультиязычными текстами.

### Задание 3. phpMyAdmin
![03-phpmyadmin.png](screenshots/03-phpmyadmin.png)


## Часть B. Таблицы и связи

### Задание 4. Три таблицы
![04-tables-cli.png](screenshots/04-tables-cli.png)  
![05-tables-pma.png](screenshots/05-tables-pma.png)

**Ответ:**  
`FOREIGN KEY` связывает колонку в дочерней таблице с первичным ключом родительской, обеспечивая ссылочную целостность (не может быть ссылки на несуществующую запись).  
`ON DELETE CASCADE` означает, что при удалении родителя автоматически удаляются все связанные дочерние записи, чтобы не оставалось «орфанных» строк.  
Используется движок InnoDB, потому что он поддерживает внешние ключи, `ON DELETE CASCADE`, транзакции и работу с целостностью данных.

### Задание 5. SQL‑скрипт
![06-schema-sql.png](screenshots/06-schema-sql.png)


## Часть C. SQL — базовые операции

### Задание 6. INSERT
![07-data-cli.png](screenshots/07-data-cli.png)  
![08-data-pma.png](screenshots/08-data-pma.png)

### Задание 7. SELECT + JOIN
![09-join.png](screenshots/09-join.png)

**Ответ:**  
`JOIN` нужен, чтобы за один запрос получить данные из связанных таблиц (например, пост + имя автора), а не делать много отдельных запросов к `users`.  
Без `JOIN` пришлось бы отдельно выбрать посты, затем в цикле для каждого `author_id` делать `SELECT` из `users`, что медленнее и создаёт больше запросов.

### Задание 8. Foreign Key — защита целостности
![10-fk-error.png](screenshots/10-fk-error.png)

### Задание 9. CASCADE
![11-cascade.png](screenshots/11-cascade.png)

### Задание 10. SQL‑инъекция
![12-injection.png](screenshots/12-injection.png)

**Ответ:**  
SQL‑инъекция возникает, когда пользовательский ввод вставляется в SQL‑строку напрямую; злоумышленник может подставить условие (`'1'='1'`), которое делает выражение всегда истинным, и получает доступ ко всем строкам.  
`prepared statement` защищает, потому что SQL‑запрос компилируется заранее, а данные передаются отдельно как параметры; СУБД не интерпретирует их как часть SQL‑кода, поэтому злоумышленник не может изменить структуру запроса.


## Часть D. PHP + MySQL

### Задание 11. db.php
![13-db-php.png](screenshots/13-db-php.png)

### Задание 12. submit.php через MySQL
![14-submit.png](screenshots/14-submit.png)  
![15-submit-pma.png](screenshots/15-submit-pma.png)

### Задание 13. messages.php через MySQL
![16-messages.png](screenshots/16-messages.png)


## Часть E. FastAPI + MySQL

### Задание 14. aiomysql
![17-api-messages.png](screenshots/17-api-messages.png)  
![18-api-users.png](screenshots/18-api-users.png18-api-users.png)

**Ответ:**  
`aiomysql` используется, потому что это асинхронный драйвер для MySQL, совместимый с Python‑`asyncio` и FastAPI; он не блокирует event loop при ожидании ответа от БД, позволяя обрабатывать другие запросы параллельно.  
С синхронным драйвером каждый запрос к MySQL блокировал бы event loop до получения ответа, и все остальные запросы к FastAPI пришлось бы выполнять последовательно, что убивает параллелизм и производительность.
