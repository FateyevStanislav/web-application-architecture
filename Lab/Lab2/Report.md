# Практическая работа №2: Развёртывание сервера и работа с Git

## 1. SSH-ключ
```
ssh-keygen -t ed25519 -C "student@boardy"
ls -la ~/.ssh/id_ed25519
```

![01-ssh-keygen.png](screenshots/01-ssh-keygen.png)

## 2. VPS и файрвол
Создал VPS в VK Cloud (Ubuntu 22.04). Настроил файрвол: TCP 22, 80, 443 + ICMP (in/out).

![02-vps.png](screenshots/02-vps.png)
![03-firewall.png](screenshots/03-firewall.png)

## 3. Подключение через PuTTY
Подключился к VPS через PuTTY с SSH-ключом (.ppk).

![04-putty.png](screenshots/04-putty.png)

## 4. Настройка сервера
```
sudo apt update && sudo apt upgrade -y
sudo hostnamectl set-hostname <fateyev>
sudo timedatectl set-timezone Europe/Moscow
hostname && timedatectl
```

![05-hostname.png](screenshots/05-hostname.png)

## 5. Пользователь student
Создал пользователя student. Переподключился как student.

![06-student.png](screenshots/06-student.png)

## 6. Git и SSH-ключ → GitHub
```
sudo apt install git -y
git config --global user.name "FateyevStanislav"
git config --global user.email "fateev0803@gmail.com"
git config --list
```
Добавил ключ из `~/.ssh/id_ed25519.pub` в GitHub.
```
ssh -T git@github.com
```

![07-git-config.png](screenshots/07-git-config.png)
![08-ssh-github.png](screenshots/08-ssh-github.png)

## 7. Репозиторий и структура
Склонировал репозиторий, создал структуру, запушил.

![09-github-repo.png](screenshots/09-github-repo.png)
