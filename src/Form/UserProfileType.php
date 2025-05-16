<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Email;

class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Please enter an email']),
                    new Email(['message' => 'The email {{ value }} is not a valid email.']),
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Email'
                ],
                'label' => 'Email'
            ])
           
            ->add('countryCode', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Please enter your country code']),
                    new Regex([
                        'pattern' => '/^\+\d{2,3}$/',
                        'message' => 'Country code must start with + followed by 2 or 3 digits'
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '+216'
                ],
                'label' => 'Country Code'
            ])
            ->add('phoneNumber', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Please enter your phone number']),
                    new Regex([
                        'pattern' => '/^\d{8}$/',
                        'message' => 'Phone number must be exactly 8 digits'
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '12345678'
                ],
                'label' => 'Phone Number'
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        'max' => 4096,
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'autocomplete' => 'new-password'
                ],
                'label' => 'Password (optional)',
                'help' => 'Leave this field empty if you don\'t want to change your password',
                'row_attr' => [
                    'class' => 'mb-3 password-field'
                ]
            ])
        ;
    }

    
}