<?php

namespace App\Form;

use App\Entity\PokemonType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PokemonFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, ["required" => false])
            ->add('legendary', ChoiceType::class, [
                'choices' => [
                    'reset' => null,
                    'Yes' => true,
                    'No' => false,
                ]]
            )
            ->add('generation',null, ["required" => false])
            ->add('type', EntityType::class, 
                [
                    "class" => PokemonType::class,
                    'choice_label' => 'name',
                ]
            )
            ->add('save', SubmitType::class)
        ;
    }

    public function getBlockPrefix()
    {
        return "filter";
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }
}
