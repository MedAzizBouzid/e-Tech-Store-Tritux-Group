<?php

namespace App\Form;

use App\Entity\Shop;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ShopType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class,[
                'label' => "Name:",
                'attr' => [
                    'placeholder'=>'Name',
                    'class' =>'form-control'
                ]
                ])
            ->add('contact', TextType::class,[
                'label' => "Contact:",
                'attr' => [
                    'placeholder'=>'Contact',
                    'class' =>'form-control'
                ]
                ])
            ->add('localisation', TextType::class,[
                'label' => "Location:",
                'attr' => [
                    'placeholder'=>'Location',
                    'class' =>'form-control'
                ]
                ])
            ->add('image', FileType::class, [
                'label' => 'Shopimage:',
                'attr' => [
                    
                    'class' =>'form-control'
                ],

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/png',
                            'image/gif',
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image',
                    ])
                ],
            ])
            ->add('horaires', TextType::class,[
                'label' => "Schedule:",
                'attr' => [
                    'placeholder'=>'Schedule',
                    'class' =>'form-control'
                ]
                ])
       
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Shop::class,
        ]);
    }
}
