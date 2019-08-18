<?php

namespace LAG\AdminBundle\Field;

use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Field\Traits\EntityAwareTrait;
use LAG\AdminBundle\Field\Traits\TranslatorTrait;
use LAG\AdminBundle\Field\Traits\TwigAwareTrait;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActionCollectionField extends AbstractField implements TwigAwareFieldInterface, EntityAwareFieldInterface, TranslatorAwareFieldInterface
{
    use TwigAwareTrait;
    use EntityAwareTrait;
    use TranslatorTrait;

    /**
     * @var FieldInterface[]
     */
    protected $fields = [];

    public function configureOptions(OptionsResolver $resolver, ActionConfiguration $actionConfiguration)
    {
        $resolver
            ->setDefaults([
                'template' => '@LAGAdmin/Field/actionCollection.html.twig',
                'actions' => [],
            ])
            ->setNormalizer('actions', function (Options $options, $value) use ($actionConfiguration) {
                if (!is_array($value) || 0 === count($value)) {
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
                        $actionConfiguration,
                        $name,
                        $actionLinkConfiguration
                    );
                }

                return $data;
            })
        ;
    }

    public function isSortable(): bool
    {
        return false;
    }

    public function render($value = null): string
    {
        $content = '';

        foreach ($this->fields as $field) {
            if ($field instanceof EntityAwareFieldInterface) {
                $field->setEntity($this->entity);
            }
            $content .= $field->render($value);
        }

        return $this->twig->render($this->options['template'], [
            'fields_content' => $content,
        ]);
    }

    protected function resolveActionLinkConfiguration(
        ActionConfiguration $actionConfiguration,
        string $actionName,
        array $actionLinkConfiguration = []
    ): array {
        $resolver = new OptionsResolver();

        $field = new ActionField($actionName);
        $field->configureOptions($resolver, $actionConfiguration);
        $field->setTwig($this->twig);
        $field->setTranslator($this->translator);

        $actionLinkConfiguration['class'] = 'btn btn-secondary';

        $options = $resolver->resolve($actionLinkConfiguration);
        $field->setOptions($options);

        $this->fields[$actionName] = $field;

        return $options;
    }
}
