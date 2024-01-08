<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\Type\Resource;

use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResourceChoiceType extends AbstractType
{
    public function __construct(
        private ResourceRegistryInterface $registry,
        private ProviderInterface $dataProvider,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->addNormalizer('choices', function (Options $options, $value) {
                if (\count($value) > 0) {
                    return $value;
                }
                $resource = $this->registry->get($options->offsetGet('resource'));

                if ($resource->hasOperation($options->offsetGet('operation'))) {
                    return $value;
                }
                $operation = $resource->getOperation($options->offsetGet('operation'));

                return $this->dataProvider->provide($operation);
            })
            ->define('resource')
            ->required()
            ->allowedTypes('string')

            ->define('operation')
            ->required()
            ->allowedTypes('string')
        ;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
