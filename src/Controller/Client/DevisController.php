<?php

namespace App\Controller\Client;

use App\Entity\Devis;
use App\Repository\DevisRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/panel/devis')]
class DevisController extends AbstractController {

    /**
     * Permet de lister les devis client
     *
     * @return Response
     */
    #[Route('/', name: 'app_client_devis_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('client/devis/index.html.twig');
    }

    /**
     * Permet d'afficher un devis client
     * 
     * @param Devis $devis
     * @return void
     */
    #[Route('/{slug}', name: 'app_client_devis_show', methods: ['GET'])]
    public function show(Devis $devis): void
    {

        if ($devis->getClient() !== $this->getUser()) {
            throw new AccessDeniedException("Vous n'avez pas l'autorisation d'accéder à cette page");
        }

        $mime = "application/pdf";
        $fichier = $devis->getUrl();

            header('Content-type: ' . $mime);
            readfile($fichier);
    }

}