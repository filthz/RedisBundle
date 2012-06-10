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

        
Use Case and Usage
==================
Imagine you have a lot of pictures on your website, each time a picture is displayed a view counter should be incremented.
Usually you dont want to terrorize your DB and issue a query on each view. Instead you collect these views and insert a bunch of 
them in a single query. This is where `FilthRedisBundle` comes into play.

### Step 5: Create an Redis Entity

``` php
<?php

# src/Foo/Redis/EventPictureViewsRedisEntity.php

namespace Foo\Redis;

use Filth\RedisBundle\Annotation\RedisAnnotation;
use Filth\RedisBundle\Entity\BaseRedisEntity;

/**
 * @RedisAnnotation(redis_key="eventpic_views_{EVENTID}_{PICTUREID}")
 */
class EventPictureViewsRedisEntity extends BaseRedisEntity
{
    /**
     * @RedisAnnotation(required=true)
     */
    protected $eventID        = null;

    /**
     * @RedisAnnotation(required=true)
     */
    protected $eventPictureID = null;


    public function getEventID()
    {
        return $this->eventID;
    }

    public function getEventPictureID()
    {
        return $this->eventPictureID;
    }

    public function setEventID($eventID)
    {
        $this->eventID = $eventID;
    }

    public function setEventPictureID($eventPictureId)
    {
        $this->eventPictureID = $eventPictureId;
    }
}
```

Notice that we defined our redis key with an annotation ( @RedisAnnotation(redis_key="eventpic_views_{EVENTID}_{PICTUREID}") )
'FilthRedisBundle' will take care of the choosen keys and will not let you define the same key among different entities.

In my example there are 2 values required for later processing: $eventID and $eventPictureID. Both have been marked as required.
You need to create setter and getter for these fields.

### Step 6: Register the new entity

``` yaml
# app/config/config.yml

filth_redis:
   entities:
       - { alias: EVENTPICTURE_VIEWS, class: Foo\Redis\EventPictureViewsRedisEntity }
```

We are ready to roll!

Example
=======

Get the entity and set the required values:

``` php
    $redisClient  = $this->get('snc_redis.default_client');
    $redisFactory = $this->get('filth.redis.factory');
    $redisRepo    = new RedisRepository($redisClient);
    
    $picViewEntity = $redisFactory->getRedisEntityByAlias('EVENTPICTURE_VIEWS');
    $picViewEntity->setEventID(123);
    $picViewEntity->setEventPictureID(567);
    
    $redisRep->increase($picViewEntity);
```

Retrieve all Keys (f.e. different eventID or pictureID) for an Redis Entity:

``` php
    $redisClient  = $this->get('snc_redis.default_client');
    $redisFactory = $this->get('filth.redis.factory');
    $redisRepo    = new RedisRepository($redisClient);
    
    $picViewEntity = $redisFactory->getRedisEntityByAlias('EVENTPICTURE_VIEWS');
    $keys = $redisRepo->getKeys($picViewEntity);
```

Retrieve an Redis Entity by key

``` php
    $redisClient  = $this->get('snc_redis.default_client');
    $redisFactory = $this->get('filth.redis.factory');
    $redisRepo    = new RedisRepository($redisClient);
    
    $keys = $redisRepo->getKeys($picViewEntity);
    
    foreach($keys as $key)
    {
        $redisEntity = $redisRepo->getRedisEntityByKey($key, $redisFactory);
    }
```

$redisEntity will be an  Foo\Redis\EventPictureViewsRedisEntity Object and its values ($eventID, $eventPictureID and $value) 
will be set automaticly by 'FilthRedisBundle'. You can access these values with the getter functions of the Entity:

``` php
    $redisClient  = $this->get('snc_redis.default_client');
    $redisFactory = $this->get('filth.redis.factory');
    $redisRepo    = new RedisRepository($redisClient);
    
    $keys = $redisRepo->getKeys($picViewEntity);
    
    foreach($keys as $key)
    {
        $redisEntity = $redisRepo->getRedisEntityByKey($key, $redisFactory);
        
        $eventID   = $redisEntity->getEventID();
        $pictureID = $redisEntity->getEventPictureID();
        $value     = $redisEntity->getValue();
    }
```

