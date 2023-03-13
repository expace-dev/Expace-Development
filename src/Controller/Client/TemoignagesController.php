<?php

namespace App\Controller\Client;

use DateTime;
use App\Entity\Temoignages;
use App\Form\Client\TemoignagesType;
use App\Repository\TemoignagesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/profile/temoignages')]
class TemoignagesController extends AbstractController {

    /**
     * Permet au client de donner son avis
     * sur les services rendu
     *
     * @param Request $request
     * @param TemoignagesRepository $temoignagesRepository
     * @return Response
     */
    #[Route('/new', name: 'app_client_temoignages_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TemoignagesRepository $temoignagesRepository): Response
    {
        $temoignage = new Temoignages();
        $form = $this->createForm(TemoignagesType::class, $temoignage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $temoignage->setClient($this->getUser());
            $temoignage->setActif('1');
            $temoignage->setCreatedAt(new DateTime());

            $temoignagesRepository->save($temoignage, true);

            $this->addFlash(
                'success',
                "Votre avis a bien été enregistré"
            );

            return $this->redirectToRoute('client_temoignages_new', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('client/temoignages/new.html.twig', [
            'temoignage' => $temoignage,
            'form' => $form,
        ]);
    }
}