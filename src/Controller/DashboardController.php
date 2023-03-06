<?php

namespace App\Controller;

use DateTime;
use App\Repository\DevisRepository;
use App\Repository\ProjetsRepository;
use App\Repository\FacturesRepository;
use App\Services\DashboardAdminService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController
{
    #[Route('/client/dashboard', name: 'app_client_dashboard')]
    public function index(
        DevisRepository $devisRepository, 
        FacturesRepository $facturesRepository, 
        ProjetsRepository $projetsRepository,
        DashboardAdminService $dashboardAdminService
    ): Response
    {
        $now = new DateTime();
        $annee = $now->format('Y');
        
        return $this->render('client/dashboard/index.html.twig', [
            'devis' => $devisRepository->count(['client' => $this->getUser()]),
            'factures' => $facturesRepository->count(['client' => $this->getUser()]),
            'projets' => $projetsRepository->count(['client' => $this->getUser()]),
            'chart_devis' => $dashboardAdminService->chartClientDevis($annee),
            'chart_factures' => $dashboardAdminService->chartClientFactures($annee),
            'chart_projets' => $dashboardAdminService->chartClientProjets($annee)
        ]);
    }
}
