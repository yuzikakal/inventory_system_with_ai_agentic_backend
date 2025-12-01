FROM php:8.2-apache

# Install ekstensi MySQL (Wajib buat PHP Native)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Aktifkan mod_rewrite (Biar URL cantik / .htaccess jalan)
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy semua file (Hanya backup, karena di docker-compose kita override pakai volume)
COPY . .

# Beri hak akses ke www-data (User Apache)
RUN chown -R www-data:www-data /var/www/html