<?php

define('ROOT', __DIR__);
require 'vendor/autoload.php';

use Devfw\Kernel;
$kernel = new Kernel();
$kernel->run();
?>