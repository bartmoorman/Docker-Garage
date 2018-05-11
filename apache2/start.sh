#!/bin/sh
chown apache: /config

if [ ! -d /config/httpd/ssl ]; then
    mkdir -p /config/httpd/ssl
    ln -sf /etc/ssl/apache2/server.pem /config/httpd/ssl/garage.crt
    ln -sf /etc/ssl/apache2/server.key /config/httpd/ssl/garage.key
fi

pidfile=/var/run/apache2/httpd.pid

if [ -f ${pidfile} ]; then
    pid=$(cat ${pidfile})

    if [ ! -d /proc/${pid} ] || [[ -d /proc/${pid} && $(basename $(readlink /proc/${pid}/exe)) != 'httpd' ]]; then
      rm ${pidfile}
    fi
elif [ ! -d /var/run/apache2 ]; then
    mkdir -p /var/run/apache2
fi

exec $(which apachectl) \
    -D FOREGROUND \
    -D ${HTTPD_SECURITY:-HTTPD_SSL} \
    -D ${HTTPD_REDIRECT:-HTTPD_REDIRECT_SSL}
