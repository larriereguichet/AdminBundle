<?php

namespace LAG\AdminBundle\Form\Type\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

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
