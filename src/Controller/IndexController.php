<?php

namespace App\Controller;

use App\Form\ContactType;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(Request $request, MailerInterface $mailer, LoggerInterface $logger): Response
    {
        $form = $this->createForm(ContactType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $email = (new Email())
                ->from('contact@'.$request->getHost())
                ->to($this->getParameter('mailerRecipient'))
                ->replyTo(
                    new Address(
                        $data['email'],
                        $data['name']
                    )
                )
                ->subject('Nouveau message de contact pour ' . $request->getHost())
                ->html($data['message']);

            try {
                $mailer->send($email);
                $this->addFlash('success', 'Votre message a bien été envoyé.');
            } catch (\Exception $e) {
                $logger->error('Error at sending email : ' . $e->getMessage());
                $this->addFlash('error', 'Une erreur est survenue. Veuillez réessayer plus tard.');
            }

            return $this->redirectToRoute('app_index');
        }

        return $this->render('index/index.html.twig', [
            'domain' => $request->getHost(),
            'contactForm' => $form->createView(),
        ]);
    }
}
