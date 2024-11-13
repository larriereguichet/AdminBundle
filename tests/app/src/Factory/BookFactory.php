<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Application\Factory;

use LAG\AdminBundle\Tests\Application\Entity\Book;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

final class BookFactory extends PersistentObjectFactory
{
    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->sentence(),
            'isbn' => self::faker()->isbn13(),
        ];
    }

    public static function class(): string
    {
        return Book::class;
    }
}