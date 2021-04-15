<?php

namespace LAG\AdminBundle\Field;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActionCollectionField extends AbstractField
{
    /**
     * @var FieldInterface[]
     */
    protected array $fields = [];

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
                        'edit' => [],
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
    ): array {
        $resolver = new OptionsResolver();
        $field = new ActionField($actionName, 'action');

        $actionLinkConfiguration['class'] = 'btn btn-secondary';
        $field->configureOptions($resolver);
        $field->setOptions($resolver->resolve($actionLinkConfiguration));

        $this->fields[$actionName] = $field;

        return $field->getOptions();
    }
}
