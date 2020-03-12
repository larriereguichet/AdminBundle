<?php

namespace LAG\AdminBundle\Configuration;

use JK\Configuration\Configuration;
use LAG\AdminBundle\Exception\Exception;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuConfiguration extends Configuration
{
    /**
     * @var string
     */
    private $menuName;

    /**
     * MenuConfiguration constructor.
     */
    public function __construct(string $menuName)
    {
        parent::__construct();

        $this->menuName = $menuName;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'children' => [],
                'attributes' => [
                    'class' => 'list-group cms-menu-'.$this->menuName,
                ],
            ])
            ->setNormalizer('children', function (Options $options, $value) {
                if (!is_array($value)) {
                    throw new Exception('The menu items should an array for menu "'.$this->menuName.'"');
                }

                foreach ($value as $item) {
                    if (!$item instanceof MenuItemConfiguration) {
                        throw new Exception(sprintf('The menu items should an array of "%s" for menu "%s"', MenuItemConfiguration::class, $this->menuName));
                    }
                }

                return $value;
            })
        ;
    }

    public function all()
    {
        $values = parent::all();

        /** @var MenuItemConfiguration $value */
        foreach ($values['children'] as $name => $value) {
            $values['children'][$name] = $value->all();
        }

        return $values;
    }

    public function getMenuName(): string
    {
        return $this->menuName;
    }

    public function getRoute(): string
    {
        return $this->parameters->get('route');
    }
}
