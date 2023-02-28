<?php

namespace App\Controller\Admin;

use App\Repository\FacturesRepository;
use App\Repository\ProjetsRepository;
use App\Repository\TemoignagesRepository;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'app_admin_dashboard')]
    public function index(
        UsersRepository $usersRepository, 
        FacturesRepository $facturesRepository,
        ProjetsRepository $projetsRepository,
        TemoignagesRepository $temoignagesRepository
    ): Response
    {
        return $this->render('admin/dashboard/index.html.twig', [
            'clients' => $usersRepository->count([]),
            'factures' => $facturesRepository->count([]),
            'projets' => $projetsRepository->count([]),
            'temoignages' => $temoignagesRepository->count([])

        ]);
    }
}
