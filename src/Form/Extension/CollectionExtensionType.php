<?php

namespace LAG\AdminBundle\Form\Extension;

use LAG\AdminBundle\Assets\Registry\ScriptRegistryInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Contracts\Translation\TranslatorInterface;

class CollectionExtensionType extends AbstractTypeExtension
{
    /**
     * @var ScriptRegistryInterface
     */
    private $registry;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public static function getExtendedTypes(): iterable
    {
        return [
            CollectionType::class,
        ];
    }

    public function __construct(ScriptRegistryInterface $registry, TranslatorInterface $translator)
    {
        $this->registry = $registry;
        $this->translator = $translator;
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $this->registry->register(ScriptRegistryInterface::LOCATION_FOOTER, '/bundles/lagadmin/assets/admin.collection.js');

        if (!key_exists('class', $view->vars['attr'])) {
            $view->vars['attr']['class'] = 'collection-container';
        } else {
            $view->vars['attr']['class'] .= ' collection-container';
        }

        if ($options['allow_delete']) {
            $view->vars['attr']['data-remove-button'] = str_replace(
                '%s',
                $this->translator->trans('lag.admin.delete'),
                '<button class="remove-link btn btn-danger"><i class="fa fa-times"></i>&nbsp;%s</button>'
            );
        }
    }
}
