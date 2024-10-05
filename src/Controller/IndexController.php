<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(ContactType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // TODO: Send email

            $this->addFlash('success', 'Votre message a bien été envoyé.');

            $this->addFlash('error', 'Une erreur est survenue. Veuillez réessayer plus tard.');

            return $this->redirectToRoute('app_index');
        }

        return $this->render('index/index.html.twig', [
            'domain' => $request->getHost(),
            'contactForm' => $form->createView(),
        ]);
    }
}
