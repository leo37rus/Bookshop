#!/bin/bash

# Запуск MariaDB
service mariadb start

# Запуск SSH
service ssh start

# Запуск Apache
apachectl -D FOREGROUND
