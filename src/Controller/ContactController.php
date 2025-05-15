<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Security\OTPService;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    public function __construct(
        private OTPService $otpService,
        private RequestStack $requestStack
    ) {}

    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($contact);
            $entityManager->flush();

            // Send confirmation email
            try {
                $this->emailService->sendConfirmationEmail(
                    $contact->getEmail(),
                    $contact->getName(),
                    $contact->getSubject()
                );
            } catch (\Exception $e) {
                // Log the error but don't stop execution
                $this->addFlash('warning', 'Your message was received but we could not send a confirmation email.');
            }

            // Store contact data in session for OTP verification
            $this->requestStack->getSession()->set('contact_data', [
                'name' => $contact->getName(),
                'email' => $contact->getEmail(),
                'phone' => $contact->getPhone(),
                'subject' => $contact->getSubject()
            ]);
            
            // Add flash message
            $this->addFlash('contact_form_submitted', true);

            

            $this->addFlash('success', 'Your message has been sent successfully!');
            return $this->redirectToRoute('app_contact');
        }

        return $this->render('pages/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/verify-contact', name: 'app_verify_contact')]
    public function verifyContact(Request $request): Response
    {
        $session = $this->requestStack->getSession();
        $contactData = $session->get('contact_data');
        
        if (!$contactData) {
            return $this->redirectToRoute('app_contact');
        }

        if ($request->isMethod('POST')) {
            $otp = $request->request->get('otp');
            
            if ($this->otpService->isValidOTP($otp, $contactData['phone'])) {
                $this->addFlash('success', 'Phone number verified successfully!');
                $session->remove('contact_data'); // Clear the session data
                return $this->redirectToRoute('app_contact');
            }

            $this->addFlash('error', 'Invalid verification code.');
        }

        return $this->render('contact/verify.html.twig', [
            'phone' => $contactData['phone']
        ]);
    }

    #[Route('/admin/contact-messages', name: 'admin_contact_messages')]
    public function listMessages(ContactRepository $contactRepository): Response
    {
        return $this->render('admin/contact_messages.html.twig', [
            'messages' => $contactRepository->findBy([], ['createdAt' => 'DESC'])
        ]);
    }
}