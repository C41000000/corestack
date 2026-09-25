ARG BUILD_ENV=development

# ==========================================
# Stage 1: Base (Dependências do Sistema e PHP via Alpine)
# ==========================================
FROM php:8.4-cli-alpine AS base


RUN apk update && apk add --no-cache \
    postgresql-dev \
    libzip-dev \
    libpng-dev \
    jpeg-dev \
    freetype-dev \
    icu-dev \
    unzip \
    git \
    bash \
    linux-headers \
    autoconf \
    g++ \
    make \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        pgsql \
        pcntl \
        zip \
        gd \
        intl \
        opcache \
        sockets \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del autoconf g++ make

# ==========================================
# Stage 2: Builder (Composer)
# ==========================================
FROM base AS builder

ARG BUILD_ENV

WORKDIR /app

# Permite o uso do Composer como superuser no ambiente de build
ENV COMPOSER_ALLOW_SUPERUSER=1

# Copia o binário oficial do Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Copia os arquivos do Composer primeiro
COPY composer.json composer.lock ./

# Instala as dependências de acordo com o ambiente
RUN if [ "$BUILD_ENV" = "production" ]; then \
        composer install --no-dev --no-interaction --prefer-dist --no-scripts --no-autoloader; \
    else \
        composer install --no-interaction --prefer-dist --no-scripts --no-autoloader; \
    fi

# Copia o restante dos arquivos
COPY . .

# Gera o autoloader otimizado
RUN if [ "$BUILD_ENV" = "production" ]; then \
        composer dump-autoload --optimize --no-dev; \
    else \
        composer dump-autoload --optimize; \
    fi

# ==========================================
# Stage 3: Imagem Final (Produção/Execução)
# ==========================================
FROM base

WORKDIR /var/www/html


RUN echo "opcache.memory_consumption=128" >> $PHP_INI_DIR/conf.d/opcache-recommended.ini \
    && echo "opcache.interned_strings_buffer=8" >> $PHP_INI_DIR/conf.d/opcache-recommended.ini \
    && echo "opcache.max_accelerated_files=4000" >> $PHP_INI_DIR/conf.d/opcache-recommended.ini \
    && echo "opcache.revalidate_freq=2" >> $PHP_INI_DIR/conf.d/opcache-recommended.ini \
    && echo "opcache.fast_shutdown=1" >> $PHP_INI_DIR/conf.d/opcache-recommended.ini
COPY --from=ghcr.io/roadrunner-server/roadrunner:latest /usr/bin/rr /usr/local/bin/rr


RUN addgroup -g 1000 laravel \
    && adduser -u 1000 -G laravel -s /bin/bash -D laravel


COPY --from=builder --chown=laravel:laravel /app /var/www/html


COPY --chown=laravel:laravel entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh


RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R laravel:laravel /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache


USER laravel

EXPOSE 8000


ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Comando que será executado pelo Entrypoint (RoadRunner)
CMD ["php", "artisan", "octane:start", "--server=roadrunner", "--host=0.0.0.0", "--port=8000"]
