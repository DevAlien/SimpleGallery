<?php
define('ROOT', __DIR__);

require 'vendor/autoload.php';

use Devfw\Kernel;
$kernel = new Kernel();
$container = $kernel->getContainer();
$em = $container->get('em');

$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
    'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection()),
    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em)
));