<?php

namespace App\Controller;

use App\Form\UserProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_user_profile')]
    public function profile(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = $this->getUser();
        $form = $this->createForm(UserProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Update basic information
                $user->setEmail($form->get('email')->getData());
                $user->setCountryCode($form->get('countryCode')->getData());
                $user->setPhoneNumber($form->get('phoneNumber')->getData());

                // Update password if provided
                if ($plainPassword = $form->get('plainPassword')->getData()) {
                    $hashedPassword = $passwordHasher->hashPassword(
                        $user,
                        $plainPassword
                    );
                    $user->setPassword($hashedPassword);
                }

                // Save changes to database
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Profile updated successfully!');
                return $this->redirectToRoute('app_user_profile');
            } catch (\Exception $e) {
                $this->addFlash('error', 'An error occurred while updating your profile.');
            }
        }

        return $this->render('user/profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}