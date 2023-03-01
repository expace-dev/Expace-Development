<?php

namespace App\Controller\Admin;

use DateTime;
use App\Repository\UsersRepository;
use App\Repository\ProjetsRepository;
use App\Repository\FacturesRepository;
use App\Repository\TemoignagesRepository;
use App\Services\DashboardAdminService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController
{
    /**
     * Permet d'afficher les statistiques de l'administration
     *
     * @param UsersRepository $usersRepository
     * @param FacturesRepository $facturesRepository
     * @param ProjetsRepository $projetsRepository
     * @param TemoignagesRepository $temoignagesRepository
     * @param DashboardAdminService $dashboardService
     * @return Response
     */
    #[Route('/admin/dashboard', name: 'app_admin_dashboard')]
    public function index(
        UsersRepository $usersRepository, 
        FacturesRepository $facturesRepository,
        ProjetsRepository $projetsRepository,
        TemoignagesRepository $temoignagesRepository,
        DashboardAdminService $dashboardService
    ): Response
    {

        $now = new DateTime();
        $annee = $now->format('Y');

        return $this->render('admin/dashboard/index.html.twig', [
            'clients' => $usersRepository->count([]),
            'factures' => $facturesRepository->count([]),
            'projets' => $projetsRepository->count([]),
            'temoignages' => $temoignagesRepository->count([]),
            'chart_utilisateurs' => $dashboardService->createChartUser($annee),
            'chart_blog' => $dashboardService->createChartBlog($annee)

        ]);
    }
}
