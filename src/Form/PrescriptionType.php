<?php

namespace App\Form;

use App\Entity\Prescription;
use App\Entity\Customer;
use App\Entity\Doctor;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrescriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prescriptionNumber', TextType::class, [
                'label' => 'N° Prescription',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: PRESC-2024-001'
                ]
            ])
            ->add('prescriptionDate', DateType::class, [
                'label' => 'Date de prescription',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('customer', EntityType::class, [
                'label' => 'Patient',
                'class' => Customer::class,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionner un patient',
                'required' => false,
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('doctor', EntityType::class, [
                'label' => 'Médecin',
                'class' => Doctor::class,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionner un médecin',
                'required' => false,
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'En attente' => 'pending',
                    'Préparée' => 'prepared',
                    'Livrée' => 'delivered',
                    'Annulée' => 'cancelled',
                ],
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('notes', TextareaType::class, [
                'label' => 'Notes',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3,
                    'placeholder' => 'Notes supplémentaires...'
                ]
            ])
            ->add('prescriptionItems', CollectionType::class, [
                'entry_type' => PrescriptionItemType::class,
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
            'data_class' => Prescription::class,
        ]);
    }
}