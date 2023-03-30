<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Receipt;
use App\Manager\ReceiptManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ReceiptType extends AbstractType
{
    public function __construct(
        private TranslatorInterface $translator,
        private ReceiptManager $receiptManager,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('receiptDate', DateType::class, [
                'widget' => 'single_text',
                'placeholder' => 'Select a value',
            ])
            ->add('items', CollectionType::class, [
                'entry_type' => ReceiptItemType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'by_reference' => false,
                'label' => false,
            ])
            ->addEventListener(
                FormEvents::SUBMIT,
                [$this, 'onSubmit']
            );
    }

    public function onSubmit(FormEvent $event): void
    {
        /** @var Receipt $receipt */
        $receipt = $event->getData();
        $form = $event->getForm();

        if (count($receipt->getItems()) === 0) {
            $form->addError(
                new FormError($this->translator->trans('Receipt must have items. Please add at least one item.'))
            );
        }

        if ($this->receiptManager->hasDuplicatePackagingMaterial($receipt)) {
            $form->addError(new FormError($this->translator->trans('Duplicate product')));
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Receipt::class,
        ]);
    }
}
