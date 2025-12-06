<?php

namespace App\Form;

use App\Entity\SaleItem;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SaleItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('product', EntityType::class, [
                'label' => 'Produit',
                'class' => Product::class,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionner un produit',
                'attr' => [
                    'class' => 'form-select product-select'
                ]
            ])
            ->add('quantity', NumberType::class, [
                'label' => 'Quantité',
                'attr' => [
                    'class' => 'form-control quantity-input',
                    'min' => 1
                ]
            ])
            ->add('unitPrice', NumberType::class, [
                'label' => 'Prix unitaire',
                'attr' => [
                    'class' => 'form-control unit-price-input',
                    'step' => '0.01'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SaleItem::class,
        ]);
    }
}