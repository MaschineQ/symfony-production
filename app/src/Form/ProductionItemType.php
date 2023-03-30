<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Product;
use App\Entity\ProductionItem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProductionItemType extends AbstractType
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', null, [
                'attr' => [
                    'class' => 'block w-full shadow-sm border-gray-300 rounded-md border p-2 mt-1 mb-2',
                ],
                'label_attr' => [
                    'class' => 'block text-sm font-medium text-gray-700',
                ],
            ])
            ->add('product', EntityType::class, [
                'class' => Product::class,
               'placeholder' => $this->translator->trans('Choose a product'),
                'required' => true,
                'label' => 'Product',
                'mapped' => true,
                'choice_label' => 'name',
                'constraints' => [new Assert\NotBlank()],
                    'attr' => [
                        'class' => 'block w-full shadow-sm border-gray-300 rounded-md border p-2 mt-1 mb-2',
                    ],
                    'label_attr' => [
                        'class' => 'block text-sm font-medium text-gray-700',
                    ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductionItem::class,
        ]);
    }
}
