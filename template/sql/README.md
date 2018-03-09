#### The sql folder

In here you can place your own SQL scripts to be executed upon (re-)installation.  
Using naming convention you can distinguish between production and debug files.
Files will be executed in normal ordering.

```php
001-structure.sql       # Executed first
002-fixtures.sql        # Executed second
003-fixtures.debug.sql  # Executed third, but only if the --nodebug flag isn't passed.
```