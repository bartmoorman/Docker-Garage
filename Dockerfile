FROM alpine

ENV HTTPD_SERVERNAME="localhost" \
    OPENER_PIN="23:out" \
    SENSOR_PIN="24:in" \
    BUTTON_PIN="25:in"

RUN apk add --no-cache \
    apache2 \
    apache2-ctl \
    apache2-ssl \
    curl \
    php7 \
    php7-apache2 \
    php7-json \
    php7-session \
    php7-sqlite3

COPY apache2/ /etc/apache2/
COPY htdocs/ /var/www/localhost/htdocs/

VOLUME /config /gpio

EXPOSE 8440

CMD ["/etc/apache2/start.sh"]

HEALTHCHECK --interval=60s --timeout=5s CMD curl --silent --location --fail http://localhost:80/ > /dev/null || exit 1
