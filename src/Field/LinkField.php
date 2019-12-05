<?php

namespace LAG\AdminBundle\Field;

use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Field\Traits\EntityAwareTrait;
use LAG\AdminBundle\Field\Traits\TwigAwareTrait;
use LAG\AdminBundle\Routing\RoutingLoader;
use LAG\AdminBundle\Utils\TranslationUtils;
use LAG\Component\StringUtils\StringUtils;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

class LinkField extends StringField implements TwigAwareFieldInterface, EntityAwareFieldInterface
{
    use TwigAwareTrait;
    use EntityAwareTrait;

    /**
     * @var ActionConfiguration
     */
    protected $actionConfiguration;

    public function isSortable(): bool
    {
        return true;
    }

    public function configureOptions(OptionsResolver $resolver, ActionConfiguration $actionConfiguration)
    {
        parent::configureOptions($resolver, $actionConfiguration);

        $this->actionConfiguration = $actionConfiguration;

        $resolver
            ->setDefaults([
                'template' => '@LAGAdmin/Field/link.html.twig',
                'title' => '',
                'icon' => '',
                'target' => '_self',
                'route' => '',
                'parameters' => [],
                'url' => '',
                'text' => '',
                'admin' => null,
                'action' => null,
                'class' => '',
            ])
            ->setAllowedTypes('route', 'string')
            ->setAllowedTypes('parameters', 'array')
            ->setAllowedTypes('length', 'integer')
            ->setAllowedTypes('url', 'string')
            ->setAllowedValues('target', [
                '_self',
                '_blank',
            ])
            ->setNormalizer('route', function (Options $options, $value) use ($actionConfiguration) {
                // route or url should be defined
                if (!$value && !$options->offsetGet('url') && !$options->offsetGet('admin')) {
                    throw new InvalidOptionsException('Either an url or a route should be defined');
                }

                if ($options->offsetGet('admin')) {
                    $value = RoutingLoader::generateRouteName(
                        $options->offsetGet('admin'),
                        $options->offsetGet('action'),
                        $actionConfiguration->getAdminConfiguration()->getParameter('routing_name_pattern')
                    );
                }

                return $value;
            })
            ->setNormalizer('admin', function (Options $options, $value) {
                // if a Admin is defined, an Action should be defined too
                if ($value && !$options->offsetGet('action')) {
                    throw new InvalidOptionsException('An Action should be provided if an Admin is provided');
                }

                return $value;
            })
            ->setNormalizer('parameters', function (Options $options, $values) {
                $cleanedValues = [];

                foreach ($values as $name => $method) {
                    if (null === $method) {
                        $method = $name;
                    }
                    $cleanedValues[$name] = $method;
                }

                return $cleanedValues;
            })
            ->setNormalizer('text', function (Options $options, $value) use ($actionConfiguration) {
                if ($value) {
                    return $value;
                }

                if ($actionConfiguration->getAdminConfiguration()->isTranslationEnabled() && $options->offsetGet('action')) {
                    return $this
                        ->translator
                        ->trans(TranslationUtils::getActionTranslationKey(
                            $actionConfiguration->getAdminConfiguration()->getTranslationPattern(),
                            $actionConfiguration->getAdminName(),
                            $options->offsetGet('action')
                        ))
                    ;
                }

                return $options->offsetGet('route');
            })
        ;
    }

    public function render($value = null): string
    {
        $value = parent::render($value);
        $accessor = PropertyAccess::createPropertyAccessor();
        $options = $this->options;

        foreach ($options['parameters'] as $name => $method) {
            // Allow static values by prefixing it with an underscore
            if (StringUtils::startsWith($method, '_')) {
                $options['parameters'][$name] = StringUtils::end($method, -1);
            } else {
                $options['parameters'][$name] = $accessor->getValue($this->entity, $method);
            }
        }

        if ($value) {
            $options['text'] = $value;
        }

        if ('' === $options['text'] && $options['action']) {
            $text = ucfirst($options['action']);

            if (!$this->actionConfiguration->getAdminConfiguration()->isTranslationEnabled()) {
                $translationKey = TranslationUtils::getActionTranslationKey(
                    $this->actionConfiguration->getAdminConfiguration()->getTranslationPattern(),
                    $this->actionConfiguration->getAdminName(),
                    $options['action']
                );
                $text = $this
                    ->translator
                    ->trans($translationKey, [], $this->actionConfiguration->getAdminConfiguration()->getTranslationCatalog())
                ;
            }
            $options['text'] = $text;
        }

        return $this->twig->render($this->options['template'], [
            'options' => $options,
        ]);
    }
}
