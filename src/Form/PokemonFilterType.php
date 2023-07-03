<?php

namespace App\Form;

use App\Entity\PokemonType;
use App\Repository\PokemonTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PokemonFilterType extends AbstractType
{
    public function __construct(private PokemonTypeRepository $pokemonTypeRepository,
    private EntityManagerInterface $entityManager)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $extraChoice = new PokemonType("Tous");
        $this->entityManager->persist($extraChoice);

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
                    "required" => false,
                    "class" => PokemonType::class,
                    'choice_label' => 'name',
                    'choices' => array_merge(
                        [$extraChoice],
                        $this->pokemonTypeRepository->findAll()
                    )
                ]
            )
            ->add('Rechercher', SubmitType::class)
            // ->add('Reset', ResetType::class)
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
