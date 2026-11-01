<?php

namespace App\Form;

use App\Entity\Location;
use App\Entity\Measurement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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

            ->add('date', DateType::class, [
                'label' => 'Data prognozy',
                'widget' => 'single_text',
                'input' => 'datetime',
                'invalid_message' => 'Podaj poprawną datę.',
            ])

            ->add('celsius', NumberType::class, [
                'label' => 'Temperatura w °C',
                'input' => 'string',
                'scale' => 0,
                'html5' => true,
                'invalid_message' => 'Podaj poprawną temperaturę.',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Measurement::class,
        ]);
    }
}
