# MusicLoud
Web Based Audio Player

I started writing this service in 2015. Just to have fun and improve programming skills.
It's buggy and unstable but can play music. In those days I didn't know anything about composer,
containers and any build utilities. It is tested on my home server with 600 GB of music.

## Screenshot
![Screenshot 1](/docs/musicloud.png?raw=true)

Today I'm going to restore developing it as pet project.

## Run
To start the service type:
```shell
docker-compose start
```
and open in your browser url: http://localhost:8080/.

## Checklist
- [x] Dockerize
- [ ] Improve error handling
- [ ] Save music to Amazon AWS
- [ ] Build frontend with webpack
- [ ] Throw out Angular and bring the React
- [ ] Authentication
