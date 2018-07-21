#!/bin/sh
for PIN in ${OPENER_PIN} ${SENSOR_PIN} ${BUTTON_PIN} ${LIGHT_PIN}; do
    if [ "${PIN%:*}" != "${PIN#*:}" ]; then
      if [ ! -L /sys/class/gpio/gpio${PIN%:*} ]; then
        echo ${PIN%:*} > /sys/class/gpio/export
      fi

      echo ${PIN#*:} > /sys/class/gpio/gpio${PIN%:*}/direction
    fi
done

groupadd -fg $(stat -c '%g' /sys/class/gpio/) gpio
usermod -aG gpio apache

chown apache: /config

if [ ! -d /config/sessions ]; then
    install -o apache -g apache -d /config/sessions
fi

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

$(which memcached) \
    -l 127.0.0.1 \
    -d \
    -u memcached

$(which apachectl) \
    -D ${HTTPD_SECURITY:-HTTPD_SSL} \
    -D ${HTTPD_REDIRECT:-HTTPD_REDIRECT_SSL}

exec su \
    -c $(which notifications.php) \
    -s /bin/sh \
    apache
