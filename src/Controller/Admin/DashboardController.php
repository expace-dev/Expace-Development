<?php

namespace App\Controller\Admin;

use App\Repository\ArticlesRepository;
use App\Repository\CommentsRepository;
use App\Repository\DevisRepository;
use DateTime;
use App\Repository\UsersRepository;
use Symfony\UX\Chartjs\Model\Chart;
use App\Repository\ProjetsRepository;
use App\Repository\FacturesRepository;
use App\Repository\PaiementsRepository;
use App\Repository\TemoignagesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;

class DashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'app_admin_dashboard')]
    public function index(
        UsersRepository $usersRepository, 
        FacturesRepository $facturesRepository,
        ProjetsRepository $projetsRepository,
        TemoignagesRepository $temoignagesRepository,
        DevisRepository $devisRepository,
        PaiementsRepository $paiementsRepository,
        ArticlesRepository $articlesRepository,
        CommentsRepository $commentsRepository,
        ChartBuilderInterface $chartBuilder
    ): Response
    {

        $now = new DateTime();
        $annee = $now->format('Y');

        $inscriptions = [];
        $amountFactures = [];
        $amountDevis = [];
        $amountPaiements = [];
        $articles = [];
        $commentaires = [];

        for ($i=1; $i < 13; $i++) {
            $date = new DateTime('1991' . '-' . $i . '-01');

            $inscriptions[] = $usersRepository->nombreInscriptionMensuel($date->format('Y'), $date->format('m'));
            
            if ($facturesRepository->amountFacturesMensuel($date->format('Y'), $date->format('m')) === null) {
                $amountFactures[] = "0";
            }
            else {
                $amountFactures[] = $facturesRepository->amountFacturesMensuel($date->format('Y'), $date->format('m'));
            }

            if ($devisRepository->amountDevisMensuel($date->format('Y'), $date->format('m')) === null) {
                $amountDevis[] = "0";
            }
            else {
                $amountDevis[] = $devisRepository->amountDevisMensuel($date->format('Y'), $date->format('m'));
            }

            if ($paiementsRepository->amountPaiementsMensuel($date->format('Y'), $date->format('m')) === null) {
                $amountPaiements[] = "0";
            }
            else {
                $amountPaiements[] = $paiementsRepository->amountPaiementsMensuel($date->format('Y'), $date->format('m'));
            }

            $articles[] = $articlesRepository->nombreArticlesMensuel($date->format('Y'), $date->format('m'));
            $commentaires[] = $commentsRepository->nombreCommentairesMensuel($date->format('Y'), $date->format('m'));
        }

        $chartUtilisateurs = $chartBuilder->createChart(Chart::TYPE_LINE);

        $chartUtilisateurs->setData([
            'labels' => ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
            'datasets' => [
                [
                    'label' => 'Inscriptions',
                    'tension' => 0.2,
                    'radius' => 5,
                    'borderWidth' => 3,
                    'backgroundColor' => 'rgb(54, 162, 235)',
                    'borderColor' => 'rgb(54, 162, 235)',
                    'data' => $inscriptions 
                ],
                [
                    'tension' => 0.2,
                    'radius' => 5,
                    'borderWidth' => 3,
                    'label' => 'Factures',
                    'backgroundColor' => 'rgb(75, 192, 192)',
                    'borderColor' => 'rgb(75, 192, 192)',
                    'data' => $amountFactures 
                ],
                [
                    'tension' => 0.2,
                    'radius' => 5,
                    'borderWidth' => 3,
                    'label' => 'Devis',
                    'backgroundColor' => 'rgba(153, 102, 255)',
                    'borderColor' => 'rgb(153, 102, 255)',
                    'data' => $amountDevis 
                ],
                [
                    'tension' => 0.2,
                    'radius' => 5,
                    'borderWidth' => 3,
                    'label' => 'Paiement',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $amountPaiements 
                ],
            ]
        ]);

        $chartUtilisateurs->setOptions([
            'maintainAspectRatio' => false,
        ]);


        $chartBlog = $chartBuilder->createChart(Chart::TYPE_LINE);

        $chartBlog->setData([
            'labels' => ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
            'datasets' => [
                [
                    'label' => 'Articles',
                    'tension' => 0.2,
                    'radius' => 5,
                    'borderWidth' => 3,
                    'backgroundColor' => 'rgb(54, 162, 235)',
                    'borderColor' => 'rgb(54, 162, 235)',
                    'data' => $articles 
                ],
                [
                    'tension' => 0.2,
                    'radius' => 5,
                    'borderWidth' => 3,
                    'label' => 'Commentaires',
                    'backgroundColor' => 'rgb(75, 192, 192)',
                    'borderColor' => 'rgb(75, 192, 192)',
                    'data' => $commentaires 
                ],
            ]
        ]);

        $chartBlog->setOptions([
            'maintainAspectRatio' => false,
        ]);
        
        return $this->render('admin/dashboard/index.html.twig', [
            'clients' => $usersRepository->count([]),
            'factures' => $facturesRepository->count([]),
            'projets' => $projetsRepository->count([]),
            'temoignages' => $temoignagesRepository->count([]),
            'chart_utilisateurs' => $chartUtilisateurs,
            'chart_blog' => $chartBlog

        ]);
    }
}
