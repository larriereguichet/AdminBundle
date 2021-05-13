<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\Type\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('_login', TextType::class, [
                'attr' => ['class' => 'form-control-user'],
                'label' => 'lag.admin.login_form.login',
                'block_prefix' => '_',
            ])
            ->add('_password', PasswordType::class, [
                'attr' => ['class' => 'form-control-user'],
                'label' => 'lag.admin.login_form.password',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
