<?php

declare(strict_types=1);

use Symfony\Bridge\Doctrine\Security\User\EntityUserProvider;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('security', [
        'password_hashers' => [
            PasswordAuthenticatedUserInterface::class => 'auto',
        ],
        'providers' => [
            'admin_users' => [
                'memory' => [
                    'users' => [
                        'admin@admin.com' => [
                            'password' => '$2y$13$a7dE0ZvG4z/XFuDnM19QQOlMTOzAstyX.ho6pZ/5FzvGHsiN5EinO',
                            'roles' => [
                                'ROLE_ADMIN',
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'firewalls' => [
            'dev' => [
                'pattern' => '^/(_(profiler|wdt)|css|images|js)/',
                'security' => false,
            ],
            'admin' => [
                'pattern' => '^/admin',
                'lazy' => true,
                'provider' => 'admin_users',
                'form_login' => [
                    'login_path' => 'lag_admin_login',
                    'check_path' => 'lag_admin_login_check',
                    'default_target_path' => 'lag_admin.homepage',
                ],
                'logout' => [
                    'path' => 'lag_admin_logout',
                ],
            ],
        ],
        'access_control' => [
            [
                'path' => '^/admin/login',
                'role' => 'PUBLIC_ACCESS',
            ],
            [
                'path' => '^/admin',
                'roles' => 'ROLE_ADMIN',
            ],
        ],
    ]);
    if ($containerConfigurator->env() === 'test') {
        $containerConfigurator->extension('security', [
            'password_hashers' => [
                PasswordAuthenticatedUserInterface::class => [
                    'algorithm' => 'auto',
                    'cost' => 4,
                    'time_cost' => 3,
                    'memory_cost' => 10,
                ],
            ],
        ]);
    }

    $services = $containerConfigurator->services();

    $services->set(EntityUserProvider::class);
};
