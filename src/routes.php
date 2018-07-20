<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->get('/[{path:.*}]', \ActionGet::class);
$app->post('/[{path:.*}]', \ActionPost::class);
