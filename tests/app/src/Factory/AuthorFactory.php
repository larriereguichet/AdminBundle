<?php

namespace LAG\AdminBundle\Tests\Application\Factory;

use LAG\AdminBundle\Tests\Application\Entity\Author;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

final class AuthorFactory extends PersistentObjectFactory
{
    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->firstName().' '.self::faker()->lastName(),
        ];
    }

    public static function class(): string
    {
        return Author::class;
    }
}