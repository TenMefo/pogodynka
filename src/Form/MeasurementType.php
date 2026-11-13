<?php

namespace App\Form;

use App\Entity\Location;
use App\Entity\Measurement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MeasurementType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('location', EntityType::class, [
                'label' => 'Miejscowość',
                'class' => Location::class,

                // Tak będą podpisane miasta na liście.
                'choice_label' => static fn (Location $location): string =>
                    $location->getCity().' ('.$location->getCountry().')',
            ])

            ->add('date', DateTimeType::class, [
                'label' => 'Data i godzina pomiaru',
                'widget' => 'single_text',
                'input' => 'datetime',
                'invalid_message' => 'Podaj poprawną datę i godzinę.',
            ])

            ->add('celsius', NumberType::class, [
                'label' => 'Temperatura w °C',
                'input' => 'string',
                'scale' => 2,
                'html5' => true,
                'attr' => ['step' => '0.01'],
                'invalid_message' => 'Podaj poprawną temperaturę.',
            ])
            ->add('humidity', IntegerType::class, [
                'label' => 'Wilgotność w %',
                'attr' => ['min' => 0, 'max' => 100],
                'invalid_message' => 'Podaj wilgotność jako liczbę całkowitą.',
            ])
            ->add('pressureHpa', NumberType::class, [
                'label' => 'Ciśnienie w hPa',
                'input' => 'string',
                'scale' => 2,
                'html5' => true,
                'attr' => ['step' => '0.01'],
                'invalid_message' => 'Podaj poprawne ciśnienie.',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Measurement::class,
        ]);
    }
}
