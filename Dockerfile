FROM php:8.4-fpm

# Copy composer.lock and composer.json
# COPY composer.lock composer.json /var/www/

# Set working directory
WORKDIR /var/www

# Install dependencies
RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    libpq-dev \
    libonig-dev \
    libzip-dev \
    vim \
    git \
    zip \
    unzip \
    curl
# RUN apt-get update && apt-get install -y \
#     build-essential \
#     libpng-dev \
# 	libpq-dev \
# 	libzip-dev \
# 	libonig-dev \
# 	libcurl4-openssl-dev \
#     libjpeg62-turbo-dev \
#     libfreetype6-dev \
#     locales \
# 	libaio1 \
# 	libaio-dev \
#     zip \
#     jpegoptim optipng pngquant gifsicle \
#     vim \
#     unzip \
#     git \
#     curl \
#     libldb-dev

# Supervisor
RUN apt-get update && apt-get install -y supervisor
RUN mkdir -p /var/log/supervisor




# install redis
# RUN apt-get update \
#  && pecl install redis && docker-php-ext-enable redis

# install imagick
# RUN apt-get update; \
#     apt-get install -y libmagickwand-dev; \
#     pecl install imagick; \
#      docker-php-ext-enable imagick;

# install gd
# RUN apt-get update && apt-get install -y \
#         libpng-dev \
#         libwebp-dev \
#         libfreetype6-dev \
#         libjpeg62-turbo-dev \
#         libpng-dev

# RUN docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype
# RUN docker-php-ext-install gd

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*



# Install Postgre PDO
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql





# Install extensions
RUN docker-php-ext-install mbstring curl zip
# RUN docker-php-ext-install mbstring zip exif pcntl curl pdo_mysql soap intl

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# NPM install
# RUN curl -sL https://deb.nodesource.com/setup_19.x | bash -
# RUN apt-get install -y nodejs

# Add user for laravel application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www


# Copy existing application directory contents
# COPY . /var/www

# COPY --chown=www:www . /var/www
RUN chown -R www:www /var/log/
RUN chown -R www:www /etc/supervisor/
RUN chown -R www:www /var/run/
# Change current user to www
USER www

# Move env
#COPY .env.example .env

#COPY . /var/www

#install app
#RUN composer install
#RUN php artisan key:generate

# Expose port 9000 and start php-fpm server
EXPOSE 9000

