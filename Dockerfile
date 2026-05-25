
# ================================
# STAGE 1 : Build des dépendances
# ================================
FROM php:8.2-cli AS builder

# Installation des extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Installation de Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copier uniquement les fichiers de dépendances d'abord (cache Docker)
COPY composer.json composer.lock ./

# Installer les dépendances sans les devDependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copier le reste du code
COPY . .

# ================================
# STAGE 2 : Image finale (légère)
# ================================
FROM php:8.2-fpm-alpine AS production

# Extensions PHP nécessaires en production
RUN apk add --no-cache \
    libzip-dev \
    && docker-php-ext-install zip pdo pdo_mysql

WORKDIR /var/www/html

# Copier uniquement ce qui est nécessaire depuis le builder
COPY --from=builder /app /var/www/html

# Permissions Laravel
RUN chown -R www-data:www-data /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# SÉCURITÉ : Exécuter sous utilisateur non-root (exigé par l'épreuve)
USER www-data

EXPOSE 9000

CMD ["php-fpm"]