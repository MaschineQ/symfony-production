<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Expedition;
use App\Manager\ExpeditionManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExpeditionType extends AbstractType
{
    public function __construct(
        private TranslatorInterface $translator,
        private ExpeditionManager $expeditionManager,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('expeditionDate', DateType::class, [
                'input' => 'datetime_immutable',
                'widget' => 'single_text',
                'placeholder' => 'Select a value',
            ])
            ->add('items', CollectionType::class, [
                'entry_type' => ExpeditionItemType::class,
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
        /** @var Expedition $expedition */
        $expedition = $event->getData();
        $form = $event->getForm();

        if ($this->expeditionManager->hasDuplicateProduct($expedition)) {
            $form->addError(new FormError($this->translator->trans('Duplicate product')));
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Expedition::class,
        ]);
    }
}
