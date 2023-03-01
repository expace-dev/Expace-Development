<?php

namespace App\Controller\Admin;

use App\Repository\PaiementsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/paiements')]
class PaiementController extends AbstractController
{
    #[Route('/', name: 'app_admin_paiements_index', methods: ['GET'])]
    public function index(PaiementsRepository $paiementsRepository): Response
    {
        $paiements = $paiementsRepository->findAll();

        return $this->render('admin/paiement/index.html.twig', [
            'paiements' => $paiements,
        ]);
    }
}
