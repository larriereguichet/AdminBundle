<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Configuration;

use JK\Configuration\Configuration;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Symfony\Component\String\u;

class MenuItemConfiguration extends Configuration
{
    private string $itemName;
    private string $menuName;

    public function __construct(string $itemName, string $menuName)
    {
        $this->itemName = $itemName;
        $this->menuName = $menuName;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'admin' => null,
                'action' => null,
                'route' => null,
                'routeParameters' => [],
                'uri' => null,
                'attributes' => [],
                'linkAttributes' => [],
                'labelAttributes' => [],
                'label' => null,
                'icon' => null,
                'text' => null,
                'children' => [],
                'extras' => ['safe_label' => true],
            ])

            ->setNormalizer('admin', function (Options $options, $adminName) {
                // The user has to be defined either an admin name and an action name, or a route name with optional
                // parameters, or an url
                if (
                    $adminName === null
                    && $options->offsetGet('route') === null
                    && $options->offsetGet('uri') === null
                    && $options->offsetGet('children') === null
                ) {
                    throw new InvalidOptionsException('You should either defined an admin name, or route name or an uri');
                }

                return $adminName;
            })
            // if an admin name is set, an action name can be provided. This action will be the menu link
            ->setNormalizer('action', function (Options $options, $action) {
                // if an action name is provided, an admin name should be defined too
                if (
                    $action !== null
                    && $options->offsetGet('admin') === null
                    && $options->offsetGet('children') === null
                ) {
                    throw new InvalidOptionsException('You should provide an admin name for this action '.$action);
                }

                // default to list action
                if (null !== $options->offsetGet('admin') && null === $action) {
                    $action = 'list';
                }

                return $action;
            })
            ->setNormalizer('children', function (Options $options, $items) {
                if (!\is_array($items)) {
                    $items = [];
                }
                $resolvedItems = [];

                foreach ($items as $name => $item) {
                    $itemConfiguration = new self($name, $this->menuName);
                    $itemConfiguration->configure($item);
                    $resolvedItems[$name] = $itemConfiguration->toArray();
                }

                return $resolvedItems;
            })
            ->setNormalizer('text', function (Options $options, $text) {
                if ($options->offsetGet('admin') && !$text) {
                    $text = u($options->offsetGet('admin'))->replace('_', ' ')->title(true);

                    if ($options->offsetGet('action') === 'list') {
                        if ($text->endsWith('y')) {
                            $text = $text->beforeLast('y')->append('ies');
                        } else {
                            $text = $text->append('s');
                        }
                    }

                    return $text->toString();
                }

                if (!$text) {
                    return u($this->itemName)->replace('_', ' ')->title(true)->toString();
                }

                return $text;
            })
            ->setNormalizer('linkAttributes', function (Options $options, $linkAttributes) {
                if (!$linkAttributes) {
                    $linkAttributes = [
                        'class' => 'nav-link text-white'
                    ];
                }

//                if ('left' === $this->menuName && empty($linkAttributes)) {
//                    $linkAttributes = ['class' => 'nav-link text-white'];
//                }

                return $linkAttributes;
            })
        ;
    }
}
