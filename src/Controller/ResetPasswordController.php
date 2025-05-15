<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\OTPService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ResetPasswordController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private OTPService $otpService
    ) {}

    #[Route('/forgot-password/request', name: 'app_forgot_password_request', methods: ['POST'])]
    public function requestReset(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $phoneNumber = $data['countryCode'] . $data['phoneNumber'];
            
            $user = $this->entityManager->getRepository(User::class)
                ->findOneBy([
                    'countryCode' => $data['countryCode'],
                    'phoneNumber' => $data['phoneNumber']
                ]);

            if (!$user) {
                throw new \Exception('No user found with this phone number.');
            }

            // Generate and send OTP
            $this->otpService->generateOTP($phoneNumber);
            
            // Store phone number in session for verification
            $request->getSession()->set('reset_password_phone', $phoneNumber);

            return $this->json(['success' => true]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    #[Route('/forgot-password/verify', name: 'app_verify_reset_token', methods: ['POST'])]
    public function verifyResetToken(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $sessionPhone = $request->getSession()->get('reset_password_phone');

            if (!$sessionPhone || $sessionPhone !== $data['phoneNumber']) {
                throw new \Exception('Invalid request.');
            }

            if (!$this->otpService->isValidOTP($data['otp'], $data['phoneNumber'])) {
                throw new \Exception('Invalid verification code.');
            }

            return $this->json(['success' => true]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    #[Route('/reset-password', name: 'app_reset_password', methods: ['POST'])]
    public function resetPassword(
        Request $request,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);
            $sessionPhone = $request->getSession()->get('reset_password_phone');

            if (!$sessionPhone || $sessionPhone !== $data['phoneNumber']) {
                throw new \Exception('Invalid request.');
            }

            $user = $this->entityManager->getRepository(User::class)
                ->findOneBy([
                    'countryCode' => substr($data['phoneNumber'], 0, 4),
                    'phoneNumber' => substr($data['phoneNumber'], 4)
                ]);

            if (!$user) {
                throw new \Exception('User not found.');
            }

            // Update password
            $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
            $this->entityManager->flush();

            // Clear session
            $request->getSession()->remove('reset_password_phone');

            return $this->json(['success' => true]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}