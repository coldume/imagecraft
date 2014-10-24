<?php

$loader = require __DIR__ . "/../vendor/autoload.php";
$loader->addPsr4('Imagecraft\\', __DIR__);

\TranslatedException\TranslatedException::init();
\ImcStream\ImcStream::register();

date_default_timezone_set('UTC');
