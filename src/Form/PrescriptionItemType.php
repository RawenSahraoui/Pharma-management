<?php

namespace App\Form;

use App\Entity\PrescriptionItem;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrescriptionItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('product', EntityType::class, [
                'label' => 'Médicament',
                'class' => Product::class,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionner un médicament',
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('quantity', IntegerType::class, [
                'label' => 'Quantité',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1
                ]
            ])
            ->add('dosage', TextareaType::class, [
                'label' => 'Posologie',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 2,
                    'placeholder' => 'Ex: 1 comprimé 3 fois par jour'
                ]
            ])
            ->add('instructions', TextareaType::class, [
                'label' => 'Instructions',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 2,
                    'placeholder' => 'Ex: Prendre après les repas'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PrescriptionItem::class,
        ]);
    }
}