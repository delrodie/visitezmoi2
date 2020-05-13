<?php

namespace App\Form;

use App\Entity\Partenaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PartenaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class,['attr'=>['class'=>'form-control', 'autocomplete'=>'off']])
            ->add('email', EmailType::class,[
                'attr'=>['class'=>'form-control','autocomplete'=>'off'],
                'required' => false
            ])
            ->add('contact', TextType::class,[
                'attr'=>['class'=>'form-control', 'autocomplete'=>'off'],
                'required'=>false
            ])
            ->add('adresse', TextType::class,[
                'attr'=>['class'=>'form-control', 'autocomplete'=>'off'],
                'required'=> false
            ])
            //->add('nombreBien')
            ->add('logo', FileType::class,[
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
                'label' => "Logo du partenaire "
            ])
            //->add('slug')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Partenaire::class,
        ]);
    }
}
