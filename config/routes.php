<?php


$app
    ->get('/hello/{name}', 'SallePW\SlimApp\Controller\HelloController')
    ->add('SallePW\SlimApp\Controller\Middleware\TestMiddleware');
