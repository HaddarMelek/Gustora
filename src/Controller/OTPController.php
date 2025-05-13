<?php
namespace App\Controller;

use App\Security\OTPService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OTPController extends AbstractController
{
    private OTPService $otpService;

    public function __construct(OTPService $otpService) 
    {
        $this->otpService = $otpService;
    }

    #[Route('/generate-otp', name: 'generateOTP')]
    public function generateOTP(): JsonResponse
    {
        try {
            $user = $this->getUser();
            $phoneNumber = $user->getCountryCode() . $user->getPhoneNumber();
            
            $this->otpService->generateOTP($phoneNumber);
            
            return $this->json(['success' => true, 'message' => 'OTP sent successfully']);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route("/otp/verify", name: "verifyOTP", methods: ["POST"])]
    public function verify(Request $request): JsonResponse
    {
        try {
            $content = $request->getContent();
            $data = json_decode($content, true);
            
            if (!isset($data['otp'])) {
                throw new \Exception('OTP code is required');
            }

            $user = $this->getUser();
            if (!$user) {
                throw new \Exception('User not found');
            }

            $phoneNumber = $user->getCountryCode() . $user->getPhoneNumber();
            error_log('Verifying OTP for phone: ' . $phoneNumber); // Debug log
            
            $isValid = $this->otpService->isValidOTP($data['otp'], $phoneNumber);
            error_log('OTP validation result: ' . ($isValid ? 'valid' : 'invalid')); // Debug log

            if (!$isValid) {
                throw new \Exception('Invalid OTP provided');
            }

            return new JsonResponse([
                'success' => true,
                'message' => 'OTP verified successfully'
            ]);
        } catch (\Exception $e) {
            error_log('OTP Verification Error: ' . $e->getMessage());
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

   
}
