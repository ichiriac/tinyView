tinyView
========

A minimalistic view layer implementation based on phtml

The main idea behind this package/class is that PHP is already a templating engine itself, so no need (for a mojority of projects) to add an intermediate rendering engine like smarty.

So, PHP does the job, but that's not means you will not need some helpers to handle rendering or vars. No need of a complicated bunch of classes, just one minimalistic class does perfectly the job.

What does tinyView Handles :
============================

* Context injection
* Nested rendering
* Path overriding

Usage :
=======

```php
<?php
    require __DIR__ . '/../src/View.php';

    class context {
        public $foo = 'foo';
    }

    // INIT :
    $view = new \tiny\View(
        new context()
        , 'bootstrap' // <-- layout
    );
    // CONFIGURE :
    $view->path(
        __DIR__ . '/views',
        __DIR__ . '/layout'
    );

    // RUN :
    echo $view->set(array(
        'title' => 'Hello world', // <-- GLOBAL SCOPE VARS
        'var1' => 'azerty',
        'var2' => 'qwerty'
    ))->respond(
        'hello' // <-- VIEW
         , array(
        'name' => 'John' // <-- VIEW SPECIFIC VAR
    )); // <-- use the defaut $view->__toString()
```
