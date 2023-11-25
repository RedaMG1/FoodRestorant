<?php

namespace App\Form;

use App\Entity\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Enter your email',
                    'class' => 'form-control',
                ]

            ])
            ->add('firstName', TextType::class, [
                'required' => true,
                'label' => 'First Name',
                'attr' => [
                    'placeholder' => 'Enter your first name',
                    'class' => 'form-control',
                ]
            ])
            ->add('lastName', TextType::class, [
                'required' => true,
                'label' => 'Last Name',
                'attr' => [
                    'placeholder' => 'Enter your last name',
                    'class' => 'form-control',
                ]
            ])
            ->add('password', PasswordType::class, [
                'required' => true,
                'attr' => [
                    // 'autocomplete' => 'new-password', // This is to hint to the browser that it should not suggest previous passwords
                    // 'placeholder' => '********',      // This is to display asterisks as a placeholder
                    'class' => 'form-control',
                    'placeholder' => 'Enter your password',
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    
                    'class' => 'btn btn-primary'
                ],
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
