<?php

namespace LAG\AdminBundle\Configuration;

use JK\Configuration\Configuration;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuConfiguration extends Configuration
{
    /**
     * @var string
     */
    private $menuName;

    /**
     * @var string
     */
    private $applicationName;

    /**
     * MenuConfiguration constructor.
     *
     * @param string $menuName
     * @param string $applicationName
     */
    public function __construct(string $menuName, string $applicationName)
    {
        parent::__construct();

        $this->menuName = $menuName;
        $this->applicationName = $applicationName;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'items' => [],
                'attr' => [],
                'position' => null,
                'css_class' => 'list-group nav flex-column navbar-nav menu-'.$this->menuName,
                'item_css_class' => '',
                'link_css_class' => 'nav-link',
                'template' => '',
                'brand' => null,
            ])
            ->setAllowedValues('position', [
                'horizontal',
                'vertical',
                null
            ])
            ->setNormalizer('position', function (Options $options, $value) {
                if ('top' === $this->menuName && null === $value) {
                    $value = 'horizontal';
                }

                if ('left' === $this->menuName && null === $value) {
                    $value = 'vertical';
                }

                return $value;
            })
            ->setNormalizer('template', function (Options $options, $value) {
                // Define bootstrap navbar component template
                if ('horizontal' === $options->offsetGet('position')) {
                    $value = '@LAGAdmin/Menu/menu.horizontal.html.twig';

                }

                // Define bootstrap nav component template
                if ('vertical' === $options->offsetGet('position')) {
                    $value ='@LAGAdmin/Menu/menu.vertical.html.twig';
                }

                return $value;
            })
            ->setNormalizer('attr', function (Options $options, $value) {
                $position = $options->offsetGet('position');

                if (!key_exists('class', $value)) {
                    $value['class'] = '';
                }

                if ('horizontal' === $position) {
                    $value['class'] .= ' navbar navbar-expand-lg navbar-dark bg-dark fixed-top';
                }

                if ('vertical' === $position) {
                    $value['class'] .= ' list-group';
                }
                $value['class'] = trim($value['class']);

                // Do not return an array with an empty string as css class
                if ('' === $value['class']) {
                    unset($value['class']);
                }

                return $value;
            })
            ->setNormalizer('item_css_class', function (Options $options, $value) {
                $position = $options->offsetGet('position');

                if (!$value) {
                    $value = '';
                }

                if ('horizontal' === $position) {
                    $value .= ' navbar-nav mr-auto';
                }

                if ('vertical' === $position) {
                    $value .= ' list-group-item list-group-item-action';
                }

                return trim($value);
            })
            ->setNormalizer('brand', function (Options $options, $value) {
                if (null === $value && 'horizontal' === $options->offsetGet('position')) {
                    $value = $this->applicationName;
                }

                return $value;
            })
        ;
    }
}
