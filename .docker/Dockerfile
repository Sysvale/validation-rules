FROM registry.gitlab.com/syssus/infra/images/debian-apache/dev:1.0

RUN apt-get update \
  && apt-get install -y --no-install-recommends \
    php8.1 \
    php8.1-curl \
    php8.1-zip \
    php8.1-dev

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

RUN mkdir -p /var/www

WORKDIR /var/www

COPY . /var/www

VOLUME /var/www

CMD ["/bin/sh"]

ENTRYPOINT ["/bin/sh", "-c"]
