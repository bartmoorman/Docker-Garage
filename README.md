## This is designed for a Raspberry Pi Zero W

### Usage
```
docker run \
--detach \
--name garage \
--publish 8440:8440 \
--env "HTTPD_SERVERNAME=**sub.do.main**" \
--volume garage-config:/config \
bmoorman/garage:latest
```
