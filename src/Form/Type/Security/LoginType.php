<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\Type\Security;

use LAG\AdminBundle\Translation\Helper\TranslationHelperInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class LoginType extends AbstractType
{
    public function __construct(private TranslationHelperInterface $translationHelper)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('_username', TextType::class, [
                'attr' => ['class' => 'form-control-user'],
                'label' => $this->translationHelper->getTranslationKey('login_form.login'),
                'translation_domain' => $this->translationHelper->getTranslationDomain(),
                'block_prefix' => '_',
            ])
            ->add('_password', PasswordType::class, [
                'attr' => ['class' => 'form-control-user'],
                'label' => $this->translationHelper->getTranslationKey('login_form.password'),
                'translation_domain' => $this->translationHelper->getTranslationDomain(),
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        // No login prefix as the login form is read directly by the Symfony security
        return '';
    }
}
