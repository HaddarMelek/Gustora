<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\OTPService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Psr\Log\LoggerInterface;

class RegistrationController extends AbstractController
{
    public function __construct(
        private OTPService $otpService,
        private LoggerInterface $logger
    ) {}

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Add debug logging
                $this->logger->debug('Form submitted and valid');
                
                // Store in session
                $session = $request->getSession();
                $hashedPassword = $passwordHasher->hashPassword(
                    $user, 
                    $form->get('plainPassword')->getData()
                );
                
                // Format phone number
                $phoneNumber = $user->getCountryCode() . $user->getPhoneNumber();
                $this->logger->debug('Attempting to send OTP to: ' . $phoneNumber);
                
                // Try to generate OTP first
                $this->otpService->generateOTP($phoneNumber);
                
                // If OTP generation successful, store user data
                $session->set('pending_registration', [
                    'user' => $user,
                    'hashedPassword' => $hashedPassword
                ]);
                
                $this->logger->debug('OTP sent successfully');
                
                return new JsonResponse([
                    'success' => true,
                    'message' => 'Verification code sent to your phone.'
                ]);
                
            } catch (\Exception $e) {
                $this->logger->error('Registration error: ' . $e->getMessage());
                return new JsonResponse([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }
            
            $this->logger->debug('Form validation errors: ' . implode(', ', $errors));
            
            return new JsonResponse([
                'success' => false,
                'message' => implode(', ', $errors)
            ], 400);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/register/verify-otp', name: 'app_verify_registration_otp', methods: ['POST'])]
    public function verifyRegistrationOtp(
        Request $request,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        try {
            $otp = $request->request->get('otp');
            $session = $request->getSession();
            $pendingRegistration = $session->get('pending_registration');

            if (!$pendingRegistration) {
                throw new \Exception('No pending registration found');
            }

            $user = $pendingRegistration['user'];
            $phoneNumber = $user->getCountryCode() . $user->getPhoneNumber();
            
            $this->logger->info('Verifying OTP for: ' . $phoneNumber);

            if (!$this->otpService->isValidOTP($otp, $phoneNumber)) {
                throw new \Exception('Invalid OTP code');
            }

            // Save user if OTP is valid
            $user->setPassword($pendingRegistration['hashedPassword']);
            $user->setIsVerified(true);
            
            $entityManager->persist($user);
            $entityManager->flush();

            $session->remove('pending_registration');

            return new JsonResponse([
                'success' => true,
                'redirect' => $this->generateUrl('app_login')
            ]);

        } catch (\Exception $e) {
            $this->logger->error('OTP verification error: ' . $e->getMessage());
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    #[Route('/register/resend-otp', name: 'app_resend_registration_otp', methods: ['POST'])]
    public function resendOtp(Request $request): JsonResponse
    {
        try {
            $session = $request->getSession();
            $pendingRegistration = $session->get('pending_registration');

            if (!$pendingRegistration) {
                throw new \Exception('No pending registration found');
            }

            $user = $pendingRegistration['user'];
            $phoneNumber = $user->getCountryCode() . $user->getPhoneNumber();
            
            $this->logger->info('Resending OTP to: ' . $phoneNumber);
            $this->otpService->generateOTP($phoneNumber);

            return new JsonResponse([
                'success' => true,
                'message' => 'New OTP sent successfully'
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Resend OTP error: ' . $e->getMessage());
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
