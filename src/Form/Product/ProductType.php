<?php

namespace App\Form\Product;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Informations de base
            ->add('name', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: Paracétamol 500mg'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 3],
            ])
            ->add('barcode', TextType::class, [
                'label' => 'Code-barres',
                'attr' => ['class' => 'form-control', 'placeholder' => '1234567890123'],
            ])
            ->add('sku', TextType::class, [
                'label' => 'SKU',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'SKU-001'],
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie',
                'class' => Category::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'form-select'],
            ])

            // Prix et TVA
            ->add('purchasePrice', MoneyType::class, [
                'label' => 'Prix d\'achat',
                'currency' => 'TND',
                'attr' => ['class' => 'form-control', 'step' => '0.01'],
            ])
            ->add('sellingPrice', MoneyType::class, [
                'label' => 'Prix de vente',
                'currency' => 'TND',
                'attr' => ['class' => 'form-control', 'step' => '0.01'],
            ])
            ->add('taxRate', NumberType::class, [
                'label' => 'Taux de TVA (%)',
                'required' => false,
                'attr' => ['class' => 'form-control', 'step' => '0.01', 'value' => '19.00'],
            ])

            // Stock
            ->add('stockQuantity', IntegerType::class, [
                'label' => 'Quantité en stock',
                'attr' => ['class' => 'form-control', 'min' => 0],
            ])
            ->add('minStockLevel', IntegerType::class, [
                'label' => 'Seuil minimum',
                'attr' => ['class' => 'form-control', 'min' => 0, 'value' => 10],
            ])
            ->add('maxStockLevel', IntegerType::class, [
                'label' => 'Seuil maximum',
                'attr' => ['class' => 'form-control', 'min' => 0, 'value' => 1000],
            ])
            ->add('unit', TextType::class, [
                'label' => 'Unité',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: boîte, flacon, comprimé'],
            ])
            ->add('packSize', IntegerType::class, [
                'label' => 'Taille du conditionnement',
                'required' => false,
                'attr' => ['class' => 'form-control', 'min' => 1],
            ])

            // Fabricant et lot
            ->add('manufacturer', TextType::class, [
                'label' => 'Fabricant',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('batchNumber', TextType::class, [
                'label' => 'Numéro de lot',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('expiryDate', DateType::class, [
                'label' => 'Date d\'expiration',
                'required' => false,
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])

            // Informations médicales
            ->add('composition', TextareaType::class, [
                'label' => 'Composition',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 2],
            ])
            ->add('dosage', TextareaType::class, [
                'label' => 'Posologie',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 2],
            ])
            ->add('sideEffects', TextareaType::class, [
                'label' => 'Effets secondaires',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 2],
            ])
            ->add('contraindications', TextareaType::class, [
                'label' => 'Contre-indications',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 2],
            ])
            ->add('storageInstructions', TextareaType::class, [
                'label' => 'Instructions de conservation',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 2],
            ])

            // Options
            ->add('requiresPrescription', CheckboxType::class, [
                'label' => 'Nécessite une ordonnance',
                'required' => false,
                'attr' => ['class' => 'form-check-input'],
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'Actif',
                'required' => false,
                'attr' => ['class' => 'form-check-input'],
            ])

            // Image
            ->add('imageFile', FileType::class, [
                'label' => 'Image du produit',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPG, PNG ou GIF)',
                    ])
                ],
                'attr' => ['class' => 'form-control'],
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