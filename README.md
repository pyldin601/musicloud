# MusicLoud
Web Based Audio Player

I started writing this service in 2015. Just to have fun and improve programming skills.
It's buggy and unstable but can play music. In those days I didn't know anything about composer,
containers and any build utilities. It is tested on my home server with 600 GB of music.

## Screenshot
![Screenshot 1](https://raw.githubusercontent.com/pldin601/musicloud/master/docs/musicloud.png?raw=true)

Today I'm going to continue making it as pet project.

## Development
To start service in development mode type:
```shell
make docker-build docker-up
```
and open in your browser url: http://localhost:8080/.

Now it requires login and password what is **guest** : **please** accordingly. It is hardcoded and will be remove in future when authentication will be done.

## Docker
Docker image is available [here](https://hub.docker.com/r/pldin601/musicloud/).
Example of stack configuration:
```yaml
db:
  environment:
    - POSTGRES_PASSWORD=musicloud
    - POSTGRES_USER=musicloud
  image: 'postgres:latest'
web:
  environment:
    - DB_DATABASE=musicloud
    - DB_HOSTNAME=db
    - DB_PASSWORD=musicloud
    - DB_USERNAME=musicloud
  image: 'pldin601/musicloud:latest'
  links:
    - db
```

## Checklist
- [x] Dockerize
- [ ] Build frontend with webpack (in progress)
- [ ] Improve error handling
- [ ] Save music to Amazon AWS
- [ ] Throw out Angular and bring the React
- [ ] Authentication
