<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\StockMovement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StockMovementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('product', EntityType::class, [
                'label' => 'Produit',
                'class' => Product::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'form-select'],
                'placeholder' => 'Sélectionner un produit',
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de mouvement',
                'choices' => [
                    'Entrée (Réception)' => 'in',
                    'Sortie (Vente/Retrait)' => 'out',
                    'Ajustement' => 'adjustment',
                ],
                'attr' => ['class' => 'form-select'],
            ])
            ->add('quantity', IntegerType::class, [
                'label' => 'Quantité',
                'attr' => ['class' => 'form-control', 'min' => 1],
            ])
            ->add('reason', TextareaType::class, [
                'label' => 'Motif / Commentaire',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 3],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => StockMovement::class,
        ]);
    }
}