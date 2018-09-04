#!/usr/bin/env php
<?php

use JK\Sam\Event\NotificationEvent;
use JK\Sam\File\Locator;
use JK\Sam\File\Normalizer;
use JK\Sam\Filter\FilterBuilder;
use JK\Sam\Task\TaskBuilder;
use JK\Sam\Task\TaskRunner;
use Symfony\Component\EventDispatcher\EventDispatcher;

require 'vendor/autoload.php';

$configuration = [
    'compass' => [],
    'merge' => [],
    'minify' => [],
    'copy' => [],
];
$eventDispatcher = new EventDispatcher();
$eventDispatcher->addListener(NotificationEvent::NAME, function(NotificationEvent $event) {
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
            'vendor/twbs/bootstrap/dist/css/bootstrap.min.css',
            'vendor/components/font-awesome/css/font-awesome.min.css',
            'src/Resources/assets/scss/*',
        ],
        'destinations' => [
            'src/Resources/public/css/admin.css',
        ],
    ],
    'admin.js' => [
        'filters' => [
            'minify',
            'merge',
        ],
        'sources' => [
            'src/Resources/assets/js/*',
            'vendor/components/jquery/jquery.min.js',
            'vendor/twbs/bootstrap/dist/js/bootstrap.min.js',
        ],
        'destinations' => [
            'src/Resources/public/js/admin.js',
        ],
    ],
    'fonts' => [
        'filters' => null,
        'sources' => [
            'vendor/components/font-awesome/fonts/'
        ],
        'destinations' => [
            'src/Resources/public/fonts/',
        ],
    ],
];
$builder = new TaskBuilder();
$tasks = $builder->build($tasks);

$normalizer = new Normalizer(realpath(__DIR__.'/AdminBundle'));
$locator = new Locator($normalizer);

$runner = new TaskRunner(
    $filters,
    $locator,
    false
);

// run your task
foreach ($tasks as $task) {
    $runner->run($task);
}
