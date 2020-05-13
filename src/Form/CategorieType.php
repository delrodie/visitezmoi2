<?php

namespace App\Form;

use App\Entity\Categorie;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelle', TextType::class,['attr'=>['class'=>'form-control', 'placeholder'=>"Libelle", 'autocomplete'=>"off"]])
            ->add('domaine', EntityType::class,[
                'attr'=>['class'=>'form-control custom-select domaine-select', 'width'=>"100%"],
                'class'=> 'App\Entity\Domaine',
                'query_builder' => function(EntityRepository $er){
                    return $er->liste();
                },
                'choice_label' => 'libelle',
                'label' => 'Domaine',
                'required'=>true,
                'multiple' => false
            ])
            //->add('slug')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
        ]);
    }
}
