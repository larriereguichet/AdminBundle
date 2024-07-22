<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Bridge\Doctrine\ORM\EventListener\InitializeResourceIdentifiersListener;
use LAG\AdminBundle\Bridge\Doctrine\ORM\EventListener\InitializeResourcePropertiesListener;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Filter\TextFilterApplicator;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Filter\FilterApplicatorInterface;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataHelper;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataHelperInterface;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataPropertyFactory;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataPropertyFactoryInterface;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Processor\ORMProcessor;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider\DoctrineCollectionNormalizeProvider;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider\FilterProvider;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider\ORMProvider;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider\PaginationProvider;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider\QueryBuilderProvider;
use LAG\AdminBundle\Bridge\Flysystem\Registry\StorageRegistry;
use LAG\AdminBundle\Bridge\Flysystem\Registry\StorageRegistryInterface;
use LAG\AdminBundle\Event\ResourceEvents;
use LAG\AdminBundle\Filter\Resolver\FilterValuesResolver;
use LAG\AdminBundle\Filter\Resolver\FilterValuesResolverInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Registry
    $services->set(StorageRegistryInterface::class, StorageRegistry::class)
        ->arg('$storages', tagged_iterator(tag: 'flysystem.storage', indexAttribute: 'storage'))
    ;
};
