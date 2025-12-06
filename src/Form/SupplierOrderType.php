<?php

namespace App\Form;

use App\Entity\SupplierOrder;
use App\Entity\Supplier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType; // ✅ Correct
use Symfony\Component\OptionsResolver\OptionsResolver;

class SupplierOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('supplier', EntityType::class, [
                'class' => Supplier::class,
                'choice_label' => 'name',
            ])
            ->add('orderDate', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('status', TextType::class)
            ->add('total', MoneyType::class, [
                'currency' => 'EUR',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SupplierOrder::class,
        ]);
    }
}
