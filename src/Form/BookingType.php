<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Your Name'
            ])
            ->add('email', EmailType::class, [
                'label' => 'Your Email'
            ])
            ->add('dateTime', DateTimeType::class, [
                'label' => 'Date & Time',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'datetimepicker-input'],
            ])
            ->add('people', ChoiceType::class, [
                'label' => 'No Of People',
                'choices' => [
                    'People 1' => 1,
                    'People 2' => 2,
                    'People 3' => 3,
                    'People 4' => 4,
                    'People 5' => 5,
                ],
            ])
            ->add('specialRequest', TextareaType::class, [
                'label' => 'Special Request',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your data class if you have one
        ]);
    }
}