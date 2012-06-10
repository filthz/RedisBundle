Filth's RedisBundle
=====================

The `FilthRedisBundle` adds support for object oriented approach in Symfony2 to the Redis key - value storage.
This bundle helps you to keep track of the used Redis keys. It will ensure that no keys will overlap unintentionally 

Installation
============

### Step 1: Download the FilthRedisBundle

Ultimately, the FilthRedisBundle files should be downloaded to the
'vendor/bundles/Filth/RedisBundle' directory.

You can accomplish this several ways, depending on your personal preference.
The first method is the standard Symfony2 method.

***Using the vendors script***

Add the following lines to your `deps` file:

```
    [FilthRedisBundle]
        git=https://github.com/filthz/RedisBundle.git
        target=/bundles/Filth/RedisBundle
```

Now, run the vendors script to download the bundle:

``` bash
$ php bin/vendors install
```

***Using submodules***

If you prefer instead to use git submodules, then run the following:

``` bash
$ git submodule add git://github.com/filthz/RedisBundle.git vendor/bundles/Filth/RedisBundle
$ git submodule update --init
```

### Step 2: Configure the Autoloader

Now you will need to add the `Filth` namespace to your autoloader:

``` php
<?php
// app/autoload.php

$loader->registerNamspaces(array(
    // ...
    'Filth' => __DIR__.'/../vendor/bundles',
));
```
### Step 3: Enable the bundle

Finally, enable the bundle in the kernel:

```php
<?php
// app/appKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Filth\RedisBundle\FilthRedisBundle(),
    );
}
```

### Step 4: Install Predis
The `FilthRedisBundle` uses Predis, see https://github.com/nrk/predis for installation details