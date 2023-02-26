<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\Type\Security;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginType extends AbstractType
{
    public function __construct(
        private ApplicationConfiguration $configuration
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('login', TextType::class, [
                'attr' => ['class' => 'form-control-user'],
                'label' => 'lag_admin.security.login_label',
                'translation_domain' => $this->configuration->get('translation_domain'),
                'block_name' => 'login',
            ])
            ->add('password', PasswordType::class, [
                'attr' => ['class' => 'form-control-user'],
                'label' => 'lag_admin.security.password_label',
                'translation_domain' => $this->configuration->get('translation_domain'),
                'block_name' => 'password',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        // Use the csrf token generator in the login template
        $resolver->setDefaults(['csrf_protection' => false]);
    }

    public function getBlockPrefix(): string
    {
        // No login prefix as the login form is read directly by the Symfony security
        return '';
    }
}
