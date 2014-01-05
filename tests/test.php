<?php
    require __DIR__ . '/../src/View.php';

    class context {
        public $foo = 'foo';
    }

    // INIT :
    $view = new \tiny\View(
        new context()
        , 'bootstrap'
    );
    // CONFIGURE :
    $view->path(
        __DIR__ . '/views',
        __DIR__ . '/layout'
    );

    // RUN :
    echo $view->set(array(
        'title' => 'Hello world',
        'var1' => 'azerty',
        'var2' => 'qwerty'
    ))->respond('hello', array(
        'name' => 'John'
    ));
    
    // TEST OVERRIDING
    $view->insertPath(
        __DIR__ . '/override/views'
        , __DIR__ . '/override/layout'
    );

    // RE-RUN :
    echo $view->set(array(
        'title' => 'Hello world N#2'
    ))->respond('hello', array(
        'name' => 'Retry :)'
    ));