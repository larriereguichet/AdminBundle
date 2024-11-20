<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\Type\Resource;

use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LegacyResourceType extends AbstractType
{
    public function __construct(
        private readonly ResourceContextInterface $context,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resource = $this->context->getResource($this->requestStack->getCurrentRequest());
        $resolver
            ->setDefaults([
                'form_template' => '@LAGAdmin/forms/form.html.twig',
                'translation_domain' => $resource->getTranslationDomain(),
            ])
        ;
    }
}
