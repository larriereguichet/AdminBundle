<?php

namespace LAG\AdminBundle\Form\Type\Resource;

use LAG\AdminBundle\Metadata\Context\ResourceContextInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResourceType extends AbstractType
{
    public function __construct(
        private ResourceContextInterface $context,
        private RequestStack $requestStack,
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
