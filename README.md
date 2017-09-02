# Backend Developer Test (Rex Software)

## Candidate
**name:** Joseph Cumines

**date:** 27 August 2017

**notes:** This is my attempt at implementing the "Movie API" as per the document sent to me
via Arnie at _Just Digital People_, which is included as [task_outline.pdf](task_outline.pdf).

## Implementation Details

## Getting Started

### Accessing app_dev.php

To access dev mode you can create a bookmark with the address:
```
javascript:(function() {document.cookie='yY9pWdMQRws6Prv0rdyUVERIwXD6sVmv=PF1m3kDMOy3YzrVstO4DLcdH6gwihhiH'+';path=/;'; location.reload();})()
```

### Setting up the environment

See the submodule 
[docker-symfony](https://github.com/joeycumines/docker-symfony/tree/rex-software-test) for the docker 
container, which is simply forked and modified to provide tools for db migrations, and a script to 
make it slightly easier to setup `/etc/hosts`.

After the containers are up and running, run `scripts/clear_cache.sh --composer` to install the
required dependencies, and you should be good to go.

### Api Docs

Assuming everything is setup and you have the server running on [symfony.dev](http://symfony.dev),
you should be redirected straight to a [swagger-ui](https://github.com/swagger-api/swagger-ui) page,
which is based on a semi-automatically generated _Swagger 2.0_ file (the code for that not part of
this branch). There is also the [NelmioApiDocBundle](https://github.com/nelmio/NelmioApiDocBundle),
which is available on [symfony.dev/api/doc](http://symfony.dev/api/doc).
