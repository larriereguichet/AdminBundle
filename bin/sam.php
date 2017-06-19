#!/usr/bin/env php
<?php

use JK\Sam\Event\NotificationEvent;
use JK\Sam\File\Locator;
use JK\Sam\File\Normalizer;
use JK\Sam\Filter\FilterBuilder;
use JK\Sam\Task\TaskBuilder;
use JK\Sam\Task\TaskRunner;
use Symfony\Component\EventDispatcher\EventDispatcher;

require __DIR__.'/../vendor/autoload.php';

$configuration = [
    'compass' => [],
    'merge' => [],
    'minify' => [],
    'copy' => [],
];
$eventDispatcher = new EventDispatcher();
$eventDispatcher->addListener(NotificationEvent::NAME, function (NotificationEvent $event) {
    echo $event->getMessage()."\n";
});

$builder = new FilterBuilder($eventDispatcher);
$filters = $builder->build($configuration);

$tasks = [
    'admin.css' => [
        'filters' => [
            'compass',
            'minify',
            'merge',
        ],
        'sources' => [
            'vendor/components/bootstrap/css/bootstrap.min.css',
            'vendor/components/font-awesome/css/font-awesome.min.css',
            'src/LAG/AdminBundle/Resources/assets/scss/*',
        ],
        'destinations' => [
            'src/LAG/AdminBundle/Resources/public/css/admin.css',
        ],
    ],
    'admin.js' => [
        'filters' => [
            'minify',
            'merge',
        ],
        'sources' => [
            'src/LAG/AdminBundle/Resources/assets/js/*',
            'vendor/components/jquery/jquery.min.js',
            'vendor/components/bootstrap/js/bootstrap.min.js',
        ],
        'destinations' => [
            'src/LAG/AdminBundle/Resources/public/js/admin.js',
        ],
    ],
    'fonts' => [
        'filters' => null,
        'sources' => [
            'vendor/components/font-awesome/fonts/'
        ],
        'destinations' => [
            'src/LAG/AdminBundle/Resources/public/fonts/',
        ],
    ],
];
$builder = new TaskBuilder();
$tasks = $builder->build($tasks);

$normalizer = new Normalizer(realpath(__DIR__.'/..'));
$locator = new Locator($normalizer);

$runner = new TaskRunner(
    $filters,
    $locator,
    false
);

// run your task
foreach ($tasks as $task) {
    //var_dump($task);
    $runner->run($task);
}
