## How I would implement image upload + thumbnails

At a larger scale, some kind of service like Amazon S3 is going to be used, not a blob in
MySQL, so that is immediately off the table. I would want to store it locally on the server for now,
and in dev environments, but implement it generically enough that it could be easily aliased to any 
different location, including external service providers, so in PHP land it would probably look
like a bunch of services implementing interfaces.

The easiest way to do this implementation would be probably just have a file token that was a valid
and safe path (randomized), and store it on the entities. Thumbnails could be implemented using this
same file token, with a prefix or the like, pre-calculating them or doing it in the background.
PHP has decent image support in my experience, so external tools probably aren't necessary for
generating the thumbnail.

It would probably be better to have a file table, so metadata could be stored easily, and
to make it less of a nightmare if there is more than one table storing files. I would bet money on
it being a bit of a nightmare either way though. The advantage of this approach is that you can be
more certain what files you actually need, for data integrity reasons.

Thumbnail generation doesn't take long enough that a worker queue is going to be necessary so that
would be ok.
