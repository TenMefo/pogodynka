<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ExternalAPIType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('latitude', NumberType::class, [
                'label' => 'Szerokość geograficzna',
                'html5' => true,
                'scale' => 6,
                'attr' => ['min' => -90, 'max' => 90, 'step' => 'any'],
                'constraints' => [new Assert\NotNull(), new Assert\Range(min: -90, max: 90)],
            ])
            ->add('longitude', NumberType::class, [
                'label' => 'Długość geograficzna',
                'html5' => true,
                'scale' => 6,
                'attr' => ['min' => -180, 'max' => 180, 'step' => 'any'],
                'constraints' => [new Assert\NotNull(), new Assert\Range(min: -180, max: 180)],
            ]);
    }
}
