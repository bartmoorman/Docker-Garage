## This is designed for a Raspberry Pi

### Docker Run
```
docker run \
--detach \
--name memcached \
memcached:alpine

docker run \
--detach \
--name garage \
--link memcached \
--publish 8440:8440 \
--env "HTTPD_SERVERNAME=**sub.do.main**" \
--env "PUSHOVER_APP_TOKEN=azGDORePK8gMaC0QOYAMyEEuzJnyUi" \
--env "LATITUDE=39.739235" \
--env "LONGITUDE=-104.990250" \
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

  garage:
    image: bmoorman/garage:armhf-latest
    container_name: garage
    depends_on:
      - memcached
    ports:
      - "8440:8440"
    environment:
      - HTTPD_SERVERNAME=**sub.do.main**
      - PUSHOVER_APP_TOKEN=azGDORePK8gMaC0QOYAMyEEuzJnyUi
      - LATITUDE=39.739235
      - LONGITUDE=-104.990250
    volumes:
      - /sys:/sys
      - garage-config:/config

volumes:
  garage-config:
```
