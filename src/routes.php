<?php
// Routes

$app->get('/[{path:.*}]', \ActionGet::class);

$app->post('/[{path:.*}]', \ActionPost::class);

$app->delete('/[{path:.*}]', \ActionDelete::class);
