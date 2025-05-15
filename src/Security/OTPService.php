<?php

namespace App\Security;

use Twilio\Rest\Client;
use Twilio\Rest\Verify\V2\ServiceContext;
use Twilio\Exceptions\TwilioException;

class OTPService 
{
    private ServiceContext $twilio;
    private array $verifiedNumbers = [];
    private bool $isTestMode;

    public function __construct(
        string $twilioSID,
        string $twilioToken,
        string $twilioVerificationSID,
        string $env = 'dev'
    ) {
        $client = new Client($twilioSID, $twilioToken);
        $this->twilio = $client->verify->v2->services($twilioVerificationSID);
        $this->isTestMode = $env === 'dev';
        
        $this->verifiedNumbers = [
            '+21623037188', 
        ];
    }

    public function generateOTP(string $phoneNumber): void 
    {
        if ($this->isTestMode && !in_array($phoneNumber, $this->verifiedNumbers)) {
            throw new \Exception(
                'This phone number is not verified in Twilio trial account. ' .
                'Please verify it first at twilio.com/console/phone-numbers/verified ' .
                'or contact administrator.'
            );
        }

        try {
            $this->twilio->verifications->create($phoneNumber, 'sms');
        } catch (TwilioException $e) {
            throw new \Exception('Failed to send OTP: ' . $e->getMessage());
        }
    }

    public function isValidOTP(string $otp, string $phoneNumber): bool 
    {
        // For development, accept '123456' as valid OTP for verified numbers
        if ($this->isTestMode && in_array($phoneNumber, $this->verifiedNumbers) && $otp === '123456') {
            return true;
        }

        try {
            $verificationResponse = $this->twilio->verificationChecks->create([
                'code' => $otp,
                'to' => $phoneNumber
            ]);

            return $verificationResponse->status === 'approved';
        } catch (TwilioException $e) {
            throw new \Exception('Failed to verify OTP: ' . $e->getMessage());
        }
    }
}