<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('address', TextType::class, [
                'required' => true,
                'label' => 'Address',
                'attr' => [
                    'placeholder' => 'Enter your address',
                    'class' => 'form-control',
                ]
            ])
            ->add('postal_code', TextType::class, [
                'required' => true,
                'label' => 'Postal code',
                'attr' => [
                    'placeholder' => 'Enter your postal code',
                    'class' => 'form-control',
                ]
            ])
            ->add('city', TextType::class, [
                'required' => true,
                'label' => 'City',
                'attr' => [
                    'placeholder' => 'Enter your city',
                    'class' => 'form-control',
                ]
            ])
            ->add('phone', TextType::class, [
                'required' => true,
                'label' => 'Phone',
                'attr' => [
                    'placeholder' => 'Enter your city',
                    'class' => 'form-control',
                ]
            ])
            // ->add('updated_at')
            // ->add('user')
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary',
                    'placeholder' => '********',

                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
