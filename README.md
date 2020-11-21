# MusicLoud
Web Based Audio Player

I started writing this service in 2015. Just to have fun and improve programming skills.
It's buggy and unstable but can play music. In those days I didn't know anything about composer,
containers and any build utilities. It is tested on my home server with 600 GB of music.

## Screenshot
![Screenshot 1](https://raw.githubusercontent.com/pldin601/musicloud/master/docs/musicloud.png?raw=true)

Today I'm going to continue making it as pet project.

### Development environment

Run necessary dependencies:
```bash
make start-dev-dependencies
```

Run database migration:
```bash
make run-database-migration
```

Enter development environment:
```bash
make enter-dev-environment
```

In development environment type:
```bash
composer install
npm install
npm start
```

Then open in your browser url: http://localhost:8080/.

It will prompt you to enter default login **guest** and password **please**.
