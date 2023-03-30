<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Production;
use App\Manager\ProductionManager;
use App\Repository\ProductRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProductionType extends AbstractType
{
    public function __construct(
        private TranslatorInterface $translator,
        private ProductRepository   $products,
        private ProductionManager $productionManager
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('productionDate', DateType::class, [
                'widget' => 'single_text',
                'placeholder' => 'Select a value',
            ])
            ->add('items', CollectionType::class, [
                'entry_type' => ProductionItemType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'by_reference' => false,
                'label' => false,
            ])
            ->addEventListener(
                FormEvents::SUBMIT,
                [$this, 'onSubmit']
            )
        ;
    }

    public function onSubmit(FormEvent $event): void
    {
        /** @var Production $production */
        $production = $event->getData();
        $form = $event->getForm();

        if ($this->productionManager->hasDuplicateProduct($production)) {
            $form->addError(new FormError($this->translator->trans('Duplicate product')));
        }

        foreach ($production->getItems() as $item) {
            if ($item->getProduct() === null) {
                $form->addError(new FormError($this->translator->trans('Product is required')));
            } else {
                $product = $this->products->find($item->getProduct()->getId());
                if (null !== $product && null !== $item->getQuantity()) {
                    if (!$this->productionManager->isRightNumberOfPiecesPerUnit(
                        $this->productionManager->getnumberOfPiecesPerUnit(
                            $item->getQuantity(),
                            $product->getQuantityPerPiece()
                        ),
                        $item->getQuantity()
                    )) {
                        $form->addError(
                            new FormError(
                                $this->translator->trans(
                                    'The quantity does not match the package.',
                                    [
                                        '%quantityPerPiece%' => $product->getQuantityPerPiece(),
                                        '%packagingType%' => $product->getPackagingType()
                                    ],
                                    'validators'
                                )
                            )
                        );
                    }
                }
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Production::class,
        ]);
    }
}
