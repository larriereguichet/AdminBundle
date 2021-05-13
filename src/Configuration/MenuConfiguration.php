<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Configuration;

use JK\Configuration\Configuration;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuConfiguration extends Configuration
{
    private string $menuName;

    public function __construct(string $menuName)
    {
        $this->menuName = $menuName;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'children' => [],
                'attributes' => [],
                'extras' => [
                    'permissions' => ['ROLE_USER'],
                ],
                'inherits' => true,
            ])
            ->setAllowedTypes('inherits', 'boolean')
            ->setNormalizer('children', function (Options $options, $value) {
                if (!\is_array($value)) {
                    $value = [];
                }
                $innerResolver = new OptionsResolver();

                foreach ($value as $name => $item) {
                    if (!$item) {
                        $item = [];
                    }
                    $configuration = new MenuItemConfiguration($name, $this->menuName);
                    $configuration->configureOptions($innerResolver);
                    $value[$name] = $innerResolver->resolve($item);
                    $innerResolver->clear();
                }

                return $value;
            })
            ->setNormalizer('extras', function (Options $options, $extras) {
                if (!\is_array($extras)) {
                    $extras = [];
                }

                if (!\array_key_exists('permissions', $extras)) {
                    $extras['permissions'] = ['ROLE_USER'];
                }

                return $extras;
            })
        ;
    }

    public function getMenuName(): string
    {
        return $this->menuName;
    }

    public function getExtras()
    {
        return $this->get('extras');
    }

    public function hasExtra(string $extra): bool
    {
        return \array_key_exists($extra, $this->getExtras());
    }

    public function getExtra(string $extra)
    {
        return $this->hasExtra($extra) ? $this->getExtras()[$extra] : null;
    }

    public function hasPermissions(): bool
    {
        return $this->hasExtra('permissions');
    }

    public function getPermissions(): array
    {
        return $this->hasPermissions() ? $this->getExtra('permissions') : [];
    }
}
