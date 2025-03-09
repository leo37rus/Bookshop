# Используем базовый образ с PHP 8.2 и Apache
FROM php:8.2-apache

# Обновляем список пакетов
RUN apt-get update

# Устанавливаем необходимые пакеты
RUN apt-get install -y \
    mc \
    openssh-server \
    passwd \
    curl \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libssl-dev \
    mariadb-server \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Устанавливаем расширения PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd dom

# Устанавливаем Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Настраиваем SSH
RUN mkdir /var/run/sshd

# Создаем пользователя admin и задаем пароль
RUN useradd -m admin && \
    echo 'admin:trust' | chpasswd && \
    usermod -aG sudo admin  # Добавляем пользователя в группу sudo (опционально)

# Настраиваем SSH для входа по паролю
RUN sed -i 's/#PermitRootLogin prohibit-password/PermitRootLogin yes/' /etc/ssh/sshd_config
RUN sed -i 's/#PasswordAuthentication yes/PasswordAuthentication yes/' /etc/ssh/sshd_config

# Настраиваем Apache
COPY ./apache/bookshop.loc.conf /etc/apache2/sites-available/bookshop.loc.conf
RUN a2ensite bookshop.loc.conf
RUN a2enmod rewrite

# Настраиваем MySQL (MariaDB)
# Изменяем bind-address на 0.0.0.0
RUN sed -i 's/bind-address = 127.0.0.1/bind-address = 0.0.0.0/' /etc/mysql/mariadb.conf.d/50-server.cnf

# Инициализируем базу данных
RUN service mariadb start && \
    mysql -e "CREATE DATABASE bookshop;" && \
    mysql -e "CREATE USER 'bookshop_user'@'localhost' IDENTIFIED BY 'secret';" && \
    mysql -e "GRANT ALL PRIVILEGES ON bookshop.* TO 'bookshop_user'@'localhost';" && \
    mysql -e "FLUSH PRIVILEGES;"

# Копируем проект Laravel
COPY ./laravel /var/www/bookshop

# Устанавливаем права на папку проекта
RUN chown -R www-data:www-data /var/www/bookshop
RUN chmod -R 755 /var/www/bookshop

# Настраиваем рабочую директорию
WORKDIR /var/www/bookshop

# Устанавливаем зависимости Laravel
RUN composer install --no-dev --optimize-autoloader

# Открываем порты
EXPOSE 80 22 3306

# Создаем скрипт для запуска сервисов
COPY ./start.sh /start.sh
RUN chmod +x /start.sh

# Запускаем сервисы через скрипт
CMD ["/start.sh"]
