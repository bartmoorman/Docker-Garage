## This is designed for a Raspberry Pi

### Usage
```
docker run \
--detach \
--name garage \
--publish 8440:8440 \
--env "HTTPD_SERVERNAME=**sub.do.main**" \
--env "PUSHOVER_APP_TOKEN=azGDORePK8gMaC0QOYAMyEEuzJnyUi" \
--env "LATITUDE=39.739235" \
--env "LONGITUDE=-104.990250" \
--volume /sys:/sys \
--volume garage-config:/config \
bmoorman/garage:latest
```
