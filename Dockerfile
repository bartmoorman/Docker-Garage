FROM alpine

ENV HTTPD_SERVERNAME="localhost"

RUN apk add --no-cache \
    apache2 \
    apache2-ctl \
    apache2-ssl \
    curl \
    php7-apache2

COPY apache2/ /etc/apache2/
COPY htdocs/ /var/www/localhost/htdocs/

VOLUME /config

EXPOSE 8440

CMD ["/etc/apache2/start.sh"]

HEALTHCHECK --interval=60s --timeout=5s CMD curl --silent --location --fail http://localhost:80/ > /dev/null || exit 1
