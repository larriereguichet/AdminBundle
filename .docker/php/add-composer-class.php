<?php

$data = json_decode(file_get_contents('composer.json'), true);
$data['autoload']['psr-4']['\\\\'] = '../admin/src';
file_put_contents('composer.json', json_encode($data));

$bundleLine = "LAG\AdminBundle\LAGAdminBundle::class => ['all' => true],";
$content = str_replace('];', $bundleLine.PHP_EOL.'];', file_get_contents('config/bundles.php'));
file_put_contents('config/bundles.php', $content);
