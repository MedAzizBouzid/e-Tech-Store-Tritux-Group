<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType ;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                'label' => "Name:",
                'attr' => [
                    'placeholder'=>'Name',
                    'class' =>'form-control'
                ]
                ])
            ->add('price', NumberType::class,[
                'label' => "Price:",
                'attr' => [
                    'placeholder'=>'Price',
                    'class' =>'form-control'
                ]
                ])
            ->add('quantity', NumberType::class,[
                'label' => "Quantity:",
                'attr' => [
                    'placeholder'=>'Quantity',
                    'class' =>'form-control'
                ]
                ])
            ->add('description', TextareaType::class,[
                'label' => "Description:",
                'attr' => [
                    'placeholder'=>'Description',
                    'class' =>'form-control'
                ]
                ])
          //->add('shops')
            ->add('category',EntityType::class, [
                'class' => Category::class,
                'attr' => [
                    
                    'class' =>'form-control'
                ],
                
                'choice_label' => 'name',
                'label' => 'Category:',
                'multiple' => false,
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
