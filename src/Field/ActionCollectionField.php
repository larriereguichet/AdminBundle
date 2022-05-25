<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Field;

use LAG\AdminBundle\Factory\FieldFactoryInterface;
use LAG\AdminBundle\Field\Render\FieldRendererInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActionCollectionField extends AbstractField implements FieldFactoryAwareInterface
{
    /** @var Field[] */
    protected array $fields = [];
    private FieldFactoryInterface $fieldFactory;

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'template' => '@LAGAdmin/fields/action-collection.html.twig',
                'actions' => [],
            ])
            ->setNormalizer('actions', function (Options $options, $value) {
                if (!\is_array($value) || 0 === \count($value)) {
                    $value = [
                        'update' => [],
                        'delete' => [],
                    ];
                }
                $data = [];

                foreach ($value as $name => $actionLinkConfiguration) {
                    if (null === $actionLinkConfiguration) {
                        $actionLinkConfiguration = [];
                    }
                    $data[$name] = $this->resolveActionLinkConfiguration(
                        $name,
                        $actionLinkConfiguration
                    );
                }

                return $data;
            })
        ;
    }

    protected function resolveActionLinkConfiguration(
        string $actionName,
        array $actionLinkConfiguration = []
    ): mixed {
        $resolver = new OptionsResolver();
        $field = new ActionField($actionName, 'action');

        //$actionLinkConfiguration['class'] = 'btn btn-secondary';
        $field->configureDefaultOptions($resolver);
        $field->configureOptions($resolver);
        $field->setOptions($resolver->resolve($actionLinkConfiguration));

        return $field->createView();
//
//        $this->fields[$actionName] = $field;
//
//        return $field->getOptions();
    }

    public function setRenderer(FieldFactoryInterface $factory)
    {
        $this->fieldFactory = $factory;
    }
}
