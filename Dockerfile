# Dockerfile

# Utiliser une image PHP avec Apache et Composer
FROM php:8.2-apache

# Installer les dépendances système nécessaires
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    zip \
    git \
    unzip \
    && docker-php-ext-install intl opcache pdo pdo_mysql zip

# Activer mod_rewrite pour Symfony
RUN a2enmod rewrite

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers de l'application Symfony dans le conteneur
COPY . .

# Installer les dépendances PHP avec Composer
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Donner les permissions au dossier var
RUN chown -R www-data:www-data /var/www/html/var

# Exposer le port 80 pour l'application web
EXPOSE 80

# Commande par défaut à exécuter
CMD ["apache2-foreground"]
