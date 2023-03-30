<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\NotificationSettingType;
use App\Repository\NotificationSettingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class NotificationSettingController extends AbstractController
{
    public function __construct(
        private TranslatorInterface $translator,
        private NotificationSettingRepository $notificationSettings,
    ) {
    }

    #[Route('/notification/setting', name: 'app_notification_setting')]
    public function index(Request $request): Response
    {
        $notificationSetting = $this->notificationSettings->find(1) ?? $this->notificationSettings->createDefault();

        $form = $this->createForm(NotificationSettingType::class, $notificationSetting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->notificationSettings->save($notificationSetting);

            $this->addFlash('success', $this->translator->trans('Notification settings have been saved.'));
            return $this->redirectToRoute('app_notification_setting');
        }

        return $this->render('notification_setting/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
