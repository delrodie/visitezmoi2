<?php

namespace App\Form;

use App\Entity\ProduitMagasin;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProduitMagasinType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reference', TextType::class,['attr'=>['class'=>'form-control', 'autocomplete'=>'off']])
            ->add('description',TextareaType::class,['attr'=>['class'=>'form-control summernote', 'rows'=>8]])
            ->add('tags', TextType::class,['attr'=>['class'=>'form-control tag', 'autocomplete'=>'off']])
            ->add('marque', TextType::class,['attr'=>['class'=>'form-control', 'autocomplete'=>'off']])
            ->add('prix',IntegerType::class,['attr'=>['class'=>'form-control', 'autocomplete'=>'off']])
            //->add('nombreVue')
            ->add('media', FileType::class,[
                'mapped'=>false,
                'required' =>false,
                'constraints'=>[
                    new File([
                        'maxSize' => '100000k',
                        'mimeTypes' =>[
                            'image/png',
                            'image/jpeg',
                            'image/jpg',
                            'image/gif',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => "Votre fichier doit être de type image"
                    ])
                ],
                'attr'=>['onchange'=>'getFileInfo()'],
                'label' => "Photo d'illustration "
            ])
            ->add('mode', EntityType::class,[
                'attr'=>['class'=>'form-control select2 rubrique-select', 'width'=>"100%"],
                'class'=> 'App\Entity\Mode',
                'query_builder' => function(EntityRepository $er){
                    return $er->liste();
                },
                'choice_label' => 'libelle',
                'label' => 'Mode',
                'required'=>true,
                'multiple' => false
            ])
            ->add('bien', EntityType::class,[
                'attr'=>['class'=>'form-control select2 rubrique-select', 'width'=>"100%"],
                'class'=> 'App\Entity\Bien',
                'query_builder' => function(EntityRepository $er){
                    return $er->liste();
                },
                'choice_label' => 'titre',
                'label' => 'Bien',
                'required'=>true,
                'multiple' => false
            ])
            ->add('famille', EntityType::class,[
                'attr'=>['class'=>'form-control select2 rubrique-select', 'width'=>"100%"],
                'class'=> 'App\Entity\Famille',
                'query_builder' => function(EntityRepository $er){
                    return $er->liste();
                },
                'choice_label' => 'libelle',
                'label' => 'Famille',
                'required'=>true,
                'multiple' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProduitMagasin::class,
        ]);
    }
}
