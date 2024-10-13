<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Bridge\Doctrine\ORM\EventListener\InitializeResourceIdentifiersListener;
use LAG\AdminBundle\Bridge\Doctrine\ORM\EventListener\InitializeResourcePropertiesListener;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Filter\EntityFilterApplicator;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Filter\TextFilterApplicator;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataHelper;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataHelperInterface;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataPropertyFactory;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataPropertyFactoryInterface;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Processor\ORMProcessor;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider\DoctrineCollectionNormalizeProvider;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider\IdentifierProvider;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider\ORMProvider;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider\PaginationProvider;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider\ResultProvider;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider\SortingProvider;
use LAG\AdminBundle\Event\ResourceEvents;
use LAG\AdminBundle\Filter\Applicator\FilterApplicatorInterface;
use LAG\AdminBundle\Filter\Resolver\FilterValuesResolver;
use LAG\AdminBundle\Filter\Resolver\FilterValuesResolverInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // State providers
    $services->set(ORMProvider::class)
        ->arg('$registry', service('doctrine'))
        ->tag('lag_admin.state_provider', ['identifier' => 'doctrine', 'priority' => 0])
    ;
    $services->set(SortingProvider::class)
        ->decorate(ProviderInterface::class, priority: 300)
        ->arg('$provider', service('.inner'))
    ;
    $services->set(PaginationProvider::class)
        ->decorate(ProviderInterface::class, priority: 210)
        ->arg('$provider', service('.inner'))
    ;
    $services->set(ResultProvider::class)
        ->decorate(ProviderInterface::class, priority: 200)
        ->arg('$provider', service('.inner'))
    ;
    $services->set(DoctrineCollectionNormalizeProvider::class)
        ->decorate(ProviderInterface::class, priority: -200)
        ->arg('$provider', service('.inner'))
        ->arg('$normalizer', service(NormalizerInterface::class))
        ->arg('$denormalizer', service(DenormalizerInterface::class))
    ;

    // State processors
    $services->set(ORMProcessor::class)
        ->arg('$registry', service('doctrine'))
        ->tag('lag_admin.state_processor', ['identifier' => 'doctrine', 'priority' => 0])
    ;

    // Event listeners
    $services->set(InitializeResourcePropertiesListener::class)
        ->arg('$propertyFactory', service(MetadataPropertyFactoryInterface::class))
        ->tag('kernel.event_listener', [
            'event' => ResourceEvents::RESOURCE_CREATE,
            'dispatcher' => 'lag_admin.build_event_dispatcher',
            'priority' => 255,
        ])
    ;
    $services->set(InitializeResourceIdentifiersListener::class)
        ->arg('$metadataHelper', service(MetadataHelperInterface::class))
        ->tag('kernel.event_listener', [
            'event' => ResourceEvents::RESOURCE_CREATE,
            'dispatcher' => 'lag_admin.build_event_dispatcher',
            'priority' => 255,
        ])
    ;

    // Doctrine metadata helpers
    $services->set(MetadataPropertyFactoryInterface::class, MetadataPropertyFactory::class)
        ->arg('$metadataHelper', service(MetadataHelperInterface::class))
    ;
    $services->set(MetadataHelperInterface::class, MetadataHelper::class)
        ->arg('$entityManager', service('doctrine.orm.entity_manager'))
    ;

    // Context resolvers
    $services->set(FilterValuesResolverInterface::class, FilterValuesResolver::class);

    // Filter applicators
    $services->set(TextFilterApplicator::class)
        ->arg('$registry', service('doctrine'))
        ->tag(FilterApplicatorInterface::SERVICE_TAG)
    ;
    $services->set(EntityFilterApplicator::class)
        ->arg('$registry', service('doctrine'))
        ->tag(FilterApplicatorInterface::SERVICE_TAG)
    ;
};
