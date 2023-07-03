<?php

namespace App\Form;

use App\Entity\Pokemon;
use App\Entity\PokemonType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PokemonFormType extends AbstractType
{

    public function __construct( private EntityManagerInterface $entityManager)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $extraChoice = new PokemonType(" --- ");
        $this->entityManager->persist($extraChoice);
        $choices = array_merge(
            [$extraChoice],
            $this->entityManager->getRepository(PokemonType::class)->findAll()
        );
        $types = $options["data"]->getType()->toArray();
        $builder
            ->add('name')
            ->add('legendary')
            ->add('generation')
            ->add('Type1', EntityType::class, [
                "class" => PokemonType::class,
                "mapped" => false,
                "required" => true,
                'choice_label' => 'name',
                'data' => $types[0],
                'choices' => $choices
            ])
            ->add('Type2', EntityType::class, [
                "class" => PokemonType::class,
                "mapped" => false, 
                "required" => false,
                'data' => $types[1] ?? null,
                'choice_label' => 'name',
                'choices' => $choices
            ])->add('Submit', SubmitType::class)
        ;

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();
            $data["type"] = [$data['Type1'], $data['Type2']];
            $event->setData($data);
            $form->remove('Type1');
            $form->remove('Type2');
            $form->add('type', EntityType::class, [
                "class" => PokemonType::class,
                "required" => false,
                "multiple" => true,
                'choice_label' => 'name'
            ]);

        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($extraChoice){
            $this->entityManager->remove($extraChoice);
        });
    }   

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pokemon::class,
            'allow_extra_fields' => true
        ]);
    }
}
