<?php

namespace App\Form;

use App\Entity\Location;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('city', TextType::class, [
                'label' => 'Miejscowość',
                'empty_data' => '',
            ])

            ->add('country', ChoiceType::class, [
                'label' => 'Państwo',
                'choices' => [
                    'Polska' => 'PL',
                    'Niemcy' => 'DE',
                    'Francja' => 'FR',
                    'Hiszpania' => 'ES',
                    'Włochy' => 'IT',
                    'Wielka Brytania' => 'GB',
                    'Stany Zjednoczone' => 'US',
                ],
            ])

            ->add('latitude', NumberType::class, [
                'label' => 'Szerokość geograficzna',
                'input' => 'string',
                'scale' => 7,
                'html5' => true,
                'invalid_message' => 'Podaj poprawną liczbę.',
            ])

            ->add('longitude', NumberType::class, [
                'label' => 'Długość geograficzna',
                'input' => 'string',
                'scale' => 7,
                'html5' => true,
                'invalid_message' => 'Podaj poprawną liczbę.',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
        ]);
    }
}
