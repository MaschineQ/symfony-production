<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Form\UserProfileType;
use App\Repository\UserProfileRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserProfileController extends AbstractController
{
    public function __construct(
        private TranslatorInterface $translator
    ) {
    }

    #[Route('/profile', name: 'app_profile')]
    public function index(Request $request, UserProfileRepository $profiles, UserRepository $users): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $userProfile = new UserProfile();
        $userProfile = $user->getUserProfile() ?? $userProfile;

        $form = $this->createForm(UserProfileType::class, $userProfile);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUserProfile($userProfile);

            $users->save($user, true);

            /** @var string $locale */
            $locale = $user->getLocale() ?? $this->getParameter('locale');
            $request->getSession()->set('_locale', $locale);

            $this->redirectToRoute('app_profile');

            $this->addFlash(
                'success',
                $this->translator->trans('Your user profile settings were saved.', [], 'messages', $locale)
            );

            return $this->redirectToRoute('app_profile');
        }

        return $this->render(
            'profile/index.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
}
