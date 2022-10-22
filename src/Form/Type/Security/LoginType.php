<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\Type\Security;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class LoginType extends AbstractType
{
    public function __construct(
        private ApplicationConfiguration $configuration
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('_username', TextType::class, [
                'attr' => ['class' => 'form-control-user'],
                'label' => 'lag_admin.security.login_label',
                'translation_domain' => $this->configuration->get('translation_domain'),
                'block_prefix' => '_',
            ])
            ->add('_password', PasswordType::class, [
                'attr' => ['class' => 'form-control-user'],
                'label' => 'lag_admin.security.password_label',
                'translation_domain' => $this->configuration->get('translation_domain'),
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        // No login prefix as the login form is read directly by the Symfony security
        return '';
    }
}
