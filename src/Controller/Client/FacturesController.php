<?php

namespace App\Controller\Client;

use App\Entity\Devis;
use App\Entity\Factures;
use App\Repository\DevisRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/client/factures')]
class FacturesController extends AbstractController {

    #[Route('/', name: 'app_client_factures_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('client/factures/index.html.twig');
    }

    #[Route('/{slug}', name: 'app_client_factures_show', methods: ['GET'])]
    public function show(Factures $factures)
    {

        if ($factures->getClient() !== $this->getUser()) {
            throw new AccessDeniedException("Vous n'avez pas l'autorisation d'accéder à cette page");
        }

        $mime = "application/pdf";
        $fichier = $factures->getUrl();

            header('Content-type: ' . $mime);
            readfile($fichier);
    }

}