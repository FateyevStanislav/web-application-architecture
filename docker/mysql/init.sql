CREATE DATABASE IF NOT EXISTS boardylaravel
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

CREATE DATABASE IF NOT EXISTS boardyapi
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

GRANT ALL PRIVILEGES ON boardylaravel.* TO 'boardy'@'%';
GRANT ALL PRIVILEGES ON boardyapi.* TO 'boardy'@'%';

FLUSH PRIVILEGES;
