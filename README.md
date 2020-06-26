### Docker Run
```
docker run \
--detach \
--name memcached \
--restart unless-stopped \
memcached:alpine

docker run \
--detach \
--name garage \
--restart unless-stopped \
--link memcached \
--publish 8440:8440 \
--volume /sys:/sys \
--volume garage-config:/config \
bmoorman/garage:armhf-latest
```

### Docker Compose
```
version: "3.7"
services:
  memcached:
    image: memcached:alpine
    container_name: memcached
    restart: unless-stopped

  garage:
    image: bmoorman/garage:armhf-latest
    container_name: garage
    restart: unless-stopped
    depends_on:
      - memcached
    ports:
      - "8440:8440"
    volumes:
      - /sys:/sys
      - garage-config:/config

volumes:
  garage-config:
```

### Environment Variables
|Variable|Description|Default|
|--------|-----------|-------|
|TZ|Sets the timezone|`America/Denver`|
|HTTPD_SERVERNAME|Sets the vhost servername|`localhost`|
|HTTPD_PORT|Sets the vhost port|`8440`|
|HTTPD_SSL|Set to anything other than `SSL` (e.g. `NO_SSL`) to disable SSL|`SSL`|
|HTTPD_REDIRECT|Set to anything other than `REDIRECT` (e.g. `NO_REDIRECT`) to disable SSL redirect|`REDIRECT`|
|PUSHOVER_APP_TOKEN|Used to retrieve sounds from the Pushover API|`<empty>`|
|MEMCACHED_HOST|Sets the Memcached host|`memcached`|
|MEMCACHED_PORT|Sets the Memcached port|`11211`|
|OPENER_PIN|Sets the output pin which is attached to the garage door opener|`<empty>`|
|SENSOR_PIN|Sets the input pin to which the garage door sensor is attached|`<empty>`|
|BUTTON_LENGTH|Sets the amount of time (in seconds) to hold OPENER_PIN down for|`1`|
|LATITUDE|Sets the latitude used to determine sunrise/sunset|`date.default_latitude` (`31.7667`)|
|LONGITUDE|Sets the logitude used to determine sunrise/sunset|`date.default_longitude` (`35.2333`)|
|SUNRISE_ZENITH|Sets the sunrise zenith used to determine sunrise|`date.sunrise_zenith` (`90.583333`)|
|SUNSET_ZENITH|Sets the sunset zenith used to determine sunset|`date.sunset_zenith` (`90.583333`)|
