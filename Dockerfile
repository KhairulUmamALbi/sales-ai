# Base image: FrankenPHP sudah include Caddy + PHP + workers mode
FROM dunglas/frankenphp:1.9.0

# Install system dependencies (only what we actually need)
RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev \
    zlib1g-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libicu-dev \
    libxml2-dev \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        zip \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        intl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set Timezone Asia/Jakarta
RUN ln -snf /usr/share/zoneinfo/Asia/Jakarta /etc/localtime && echo "Asia/Jakarta" > /etc/timezone

# Install Node.js 20 untuk build frontend assets (Tailwind via Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www

# ----------------------------
# ✅ Layer caching: install deps before copying full source
# ----------------------------
COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --no-scripts --optimize-autoloader --no-dev

# Build frontend assets
COPY package.json package-lock.json* ./
RUN npm install

# Copy all project files
COPY . .

# Build production assets (Vite + Tailwind)
RUN npm run build && rm -rf node_modules

# Finalize composer (trigger post-install scripts now that artisan is available)
RUN composer dump-autoload --optimize

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Storage symlink
RUN rm -rf public/storage && \
    ln -s /var/www/storage/app/public /var/www/public/storage || true

# Laravel cache optimization (done on container start via entrypoint instead, to pick up env vars)
# php artisan config:cache is run by the entrypoint below

# Expose app port
EXPOSE 7000

# Entrypoint: run migrations + cache config + start FrankenPHP
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["frankenphp", "php-server", "--listen", ":7000", "--root", "/var/www/public"]
