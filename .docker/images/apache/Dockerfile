FROM php:8.3-apache

# Instala as dependências do ambiente
RUN apt update

RUN apt-get install -y \
    zip \
    curl \
    sudo \
    unzip 

# Instalando extensão do PHP
RUN docker-php-ext-install pdo_mysql 

# Configura um Virtual Host para servir a aplicação
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Habilita o mod_rewrite
RUN a2enmod rewrite

# Copia o projeto para dentro do contêiner
ADD ./ /var/www/html/

# Altera os proprietários das pastas
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

WORKDIR /var/www/html/

# Copia o arquivo ".env" para dentro do contêiner
RUN cp .env /.env

CMD [".docker/images/apache/startup.sh"]