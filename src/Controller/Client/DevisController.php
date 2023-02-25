<?php

namespace App\Controller\Client;

use App\Entity\Devis;
use App\Repository\DevisRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/client/devis')]
class DevisController extends AbstractController {

    #[Route('/', name: 'app_client_devis_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('client/devis/index.html.twig');
    }

    #[Route('/{slug}', name: 'app_client_devis_show', methods: ['GET'])]
    public function show(Devis $devis)
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