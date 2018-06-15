FROM php:7.2.6-apache
COPY . /var/www/html

# install tools
RUN apt-get update && apt-get install -y wget

# install libyaml
RUN mkdir /tmp/install
RUN wget -P /tmp/install http://pyyaml.org/download/libyaml/yaml-0.1.7.tar.gz
RUN tar -xzf /tmp/install/yaml-0.1.7.tar.gz -C /tmp/install/
WORKDIR /tmp/install/yaml-0.1.7
RUN ./configure
RUN make
RUN make install

# install yaml
RUN pecl install yaml
RUN echo extension=yaml.so > /usr/local/etc/php/conf.d/docker-php-ext-yaml.ini

WORKDIR /var/www/html
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN a2enmod rewrite
RUN echo "RewriteEngine On" >> /etc/apache2/apache2.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf