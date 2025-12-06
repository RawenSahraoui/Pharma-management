<?php

namespace App\Form;

use App\Entity\Sale;
use App\Entity\Customer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SaleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('invoiceNumber', TextType::class, [
                'label' => 'Numéro de facture',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: INV-2024-001'
                ]
            ])
            ->add('saleDate', DateTimeType::class, [
                'label' => 'Date de vente',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('customer', EntityType::class, [
                'label' => 'Client',
                'class' => Customer::class,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionner un client',
                'required' => false,
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('saleItems', CollectionType::class, [
                'entry_type' => SaleItemType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sale::class,
        ]);
    }
}