# Backend Developer Test (Rex Software)

## Candidate
**name:** Joseph Cumines

**date:** 27 August 2017

**notes:** This is my attempt at implementing the "Movie API" as per the document sent to me
via Arnie at _Just Digital People_, which is included as [task_outline.pdf](task_outline.pdf).

## Implementation Details

tl;dr PHP 7.0 Symfony 3.3 + various bundles, composed docker container, and some random bash scripts, as
well as various API documentation (hosted via the container).

See also, my fork: [docker-symfony](https://github.com/joeycumines/docker-symfony/tree/rex-software-test)

As it stands 4/09/2017 the task is maybe half complete, I tried to do a bit of everything for
demonstrative purposes, but it's unlikely I will have any more time to spend on this, it was fun
though, I just played around too much.

There is a script direc

### What is actually completed

#### Task 1. Authentication

Complete, uses FOSUserBundle + FOSOAuthServerBundle.

#### Task 2. Security rules via middleware

Complete, see `app/config/security.yml` if you want to check out the actual rules, kept it simple.

#### Task 3. Endpoints

Not even close. For the actual rest endpoints I implemented the `/movies` resource complete with
all the http verbs, and actual error messages and documentation. Roles (actor in movie) can be
set via these endpoints, if you add them to the database beforehand. Doubt I would ever get
`src/RexSoftwareTest/ApiBundle/Form/EventListener/CollectionChangeSubscriber.php` past code review,
but it was a fun exercise, haha.

#### Task 4. Upload

Nup. Discussion on strategy to handle files [here](docs/images_and_thumbnails.md).

#### Task 5. Migrations and seeders

I modified the docker container to include a migration image, which will automatically run migration
scripts against the container when brought up, see the `docker-symfony` submodule for that.

#### Task 6. Documentation via Swagger or API Blueprint

I used NelmioApiDocBundle in conjunction with FOSRestBundle, but nelmio is not particularly good,
so I used another script which I wrote (it's not committed) to generate a Swagger 2.0 doc, which
is served via `/api`, which can be easily viewed with the latest and greatest `swagger-ui`, which
is off a redirect from `/`.

#### Task 7. Implement a testing mechanism

Well I installed phpunit, does that count? Ha, I am kidding, would have liked to test all the entity
methods, in particular the logic around the `Movie::getRoleIds` method, not to mention it would be
super simple to setup API tests with the way the container is configured. There is one test
case.

#### Bonus points

Didn't really do any of these, excepting [a short doc on thumbnails](docs/images_and_thumbnails.md).
It would not be difficult to dockerize the whole thing though, it's pretty close as is.

## Getting Started

Follow the guide in the [docker-symfony](https://github.com/joeycumines/docker-symfony/tree/rex-software-test)
submodule, and configure parameters for both the docker and symfony environments (I have changed them
in the `.dist` files to be the same as my local/the one `migration.sql`, but your mileage may vary).
You can run `scripts/clear_cache.sh --composer` to composer install and clear all the caches, there
are also some scripts in the docker repo.

Auth might be tricky, but if you use the provided `migration.sql` you should be able to use the
request body in the details of the oauth endpoint doc in the Swagger 2.0 doc, you can then use
the UI, setting the auth header to `Bearer <token>`. If you get stuck
[this gist for setting up the bundles](https://gist.github.com/tjamps/11d617a4b318d65ca583)
may be helpful.

### Accessing app_dev.php

To access dev mode you can create a bookmark with the address:
```
javascript:(function() {document.cookie='yY9pWdMQRws6Prv0rdyUVERIwXD6sVmv=PF1m3kDMOy3YzrVstO4DLcdH6gwihhiH'+';path=/;'; location.reload();})()
```

### Api Docs

Assuming everything is setup and you have the server running on [symfony.dev](http://symfony.dev),
you should be redirected straight to a [swagger-ui](https://github.com/swagger-api/swagger-ui) page,
which is based on a semi-automatically generated _Swagger 2.0_ file (the code for that not part of
this branch). There is also the [NelmioApiDocBundle](https://github.com/nelmio/NelmioApiDocBundle),
which is available on [symfony.dev/api/doc](http://symfony.dev/api/doc).

### Auth

Authentication with the api can be performed via the oauth endpoint, as detailed in the swagger doc.
The `Authorization` header needs to be set with the returned `access_token` in the format
`Bearer <access_token>`, this can be done via the swagger ui, which can also be used to test the api.
