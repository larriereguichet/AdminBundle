<?php

namespace LAG\AdminBundle\Metadata;

use LAG\AdminBundle\Action\Show;
use LAG\AdminBundle\Bridge\Doctrine\ORM\DataProcessor\ORMDataProcessor;
use LAG\AdminBundle\Bridge\Doctrine\ORM\DataProvider\ORMDataProvider;
use LAG\AdminBundle\Controller\Update;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class Admin
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $dataClass = null,
        public readonly ?string $title = null,
        public readonly ?string $group = null,
        public readonly ?string $adminClass = \LAG\AdminBundle\Admin\Admin::class,
        public readonly array $actions = [
            new Index(),
            new Create(),
            new Update(),
            new Delete(),
            new Show(),
        ],
        public readonly string $processor = ORMDataProcessor::class,
        public readonly string $provider = ORMDataProvider::class,
    ) {
    }
}
