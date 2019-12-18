<?php

namespace LAG\AdminBundle\Field\Render;

use LAG\AdminBundle\Field\EntityAwareFieldInterface;
use LAG\AdminBundle\Field\FieldInterface;
use LAG\AdminBundle\Field\RendererAwareFieldInterface;
use LAG\AdminBundle\Utils\TranslationUtils;
use LAG\AdminBundle\View\ViewInterface;
use LAG\Component\StringUtils\StringUtils;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Contracts\Translation\TranslatorInterface;

class FieldRenderer implements FieldRendererInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function render(FieldInterface $field, $entity): string
    {
        $value = null;
        $accessor = PropertyAccess::createPropertyAccessor();

        if ('_' !== substr($field->getName(), 0, 1)) {
            // The name starts with a underscore, it is not a custom field and it should be mapped to the entity
            $value = $accessor->getValue($entity, $field->getName());
        }

        if ($field instanceof EntityAwareFieldInterface) {
            // The field required the entity to be rendered
            $field->setEntity($entity);
        }

        if ($field instanceof RendererAwareFieldInterface) {
            // Some fields types (collections...) can require children render
            $field->setRenderer($this);
        }

        return $field->render($value);
    }

    public function renderHeader(ViewInterface $admin, FieldInterface $field): string
    {
        if (StringUtils::startsWith($field->getName(), '_')) {
            return '';
        }
        $configuration = $admin->getAdminConfiguration();

        if ($configuration->isTranslationEnabled()) {
            $key = TranslationUtils::getTranslationKey(
                $configuration->getTranslationPattern(),
                $admin->getName(),
                StringUtils::underscore($field->getName())
            );
            $title = $this->translator->trans($key, [], $configuration->getTranslationCatalog());
        } else {
            $title = StringUtils::camelize($field->getName());
            $title = preg_replace('/(?<!\ )[A-Z]/', ' $0', $title);
            $title = trim($title);

            if ('Id' === $title) {
                $title = '#';
            }
        }

        return $title;
    }
}
