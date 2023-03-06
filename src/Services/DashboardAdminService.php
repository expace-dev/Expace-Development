<?php

namespace App\Services;

use App\Repository\ArticlesRepository;
use App\Repository\CommentsRepository;
use DateTime;
use App\Repository\DevisRepository;
use App\Repository\UsersRepository;
use Symfony\UX\Chartjs\Model\Chart;
use App\Repository\FacturesRepository;
use App\Repository\PaiementsRepository;
use App\Repository\ProjetsRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;

class DashboardAdminService {

    public function __construct(
        private UsersRepository $usersRepository,
        private FacturesRepository $facturesRepository,
        private DevisRepository $devisRepository,
        private PaiementsRepository $paiementsRepository,
        private ChartBuilderInterface $chartBuilder,
        private ArticlesRepository $articlesRepository,
        private CommentsRepository $commentsRepository,
        private ProjetsRepository $projetsRepository,
        private Security $security
    )
    {
    }

    public function createChartUser($annee) {

        $inscriptions = [];
        $amountFactures = [];
        $amountDevis = [];
        $amountPaiements = [];

        for ($i=1; $i < 13; $i++) {

            $date = new DateTime($annee . '-' . $i . '-01');

            $inscriptions[] = $this->usersRepository->nombreInscriptionMensuel($date->format('Y'), $date->format('m'));
            
            if ($this->facturesRepository->amountFacturesMensuel($date->format('Y'), $date->format('m')) === null) {
                $amountFactures[] = "0";
            }
            else {
                $amountFactures[] = $this->facturesRepository->amountFacturesMensuel($date->format('Y'), $date->format('m'));
            }

            if ($this->devisRepository->amountDevisMensuel($date->format('Y'), $date->format('m')) === null) {
                $amountDevis[] = "0";
            }
            else {
                $amountDevis[] = $this->devisRepository->amountDevisMensuel($date->format('Y'), $date->format('m'));
            }

            if ($this->paiementsRepository->amountPaiementsMensuel($date->format('Y'), $date->format('m')) === null) {
                $amountPaiements[] = "0";
            }
            else {
                $amountPaiements[] = $this->paiementsRepository->amountPaiementsMensuel($date->format('Y'), $date->format('m'));
            }
            
        }

        $chartUtilisateurs = $this->chartBuilder->createChart(Chart::TYPE_LINE);

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

        return $chartUtilisateurs;


    }

    public function createChartBlog($annee) {

        $articles = [];
        $commentaires = [];

        for ($i=1; $i < 13; $i++) {
            $date = new DateTime($annee . '-' . $i . '-01');

            $articles[] = $this->articlesRepository->nombreArticlesMensuel($date->format('Y'), $date->format('m'));
            $commentaires[] = $this->commentsRepository->nombreCommentairesMensuel($date->format('Y'), $date->format('m'));
        }

        $chartBlog = $this->chartBuilder->createChart(Chart::TYPE_LINE);

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

        return $chartBlog;
    }

    public function chartClientDevis($annee) {
        
        $amountDevis = [];

        

        for ($i=1; $i < 13; $i++) {

            $date = new DateTime($annee . '-' . $i . '-01');

            if ($this->devisRepository->amountDevisMensuelClient($date->format('Y'), $date->format('m'), $this->security->getUser()) === null) {
                $amountDevis[] = "0";
            }
            else {
                $amountDevis[] = $this->devisRepository->amountDevisMensuelClient($date->format('Y'), $date->format('m'), $this->security->getUser());
            }
        }

        $chartDevis = $this->chartBuilder->createChart(Chart::TYPE_BAR);

        $chartDevis->setData([
            'labels' => ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
            'datasets' => [
                [
                    'label' => 'Devis',
                    'tension' => 0.2,
                    'radius' => 5,
                    'borderWidth' => 3,
                    'backgroundColor' => 'rgb(54, 162, 235)',
                    'borderColor' => 'rgb(54, 162, 235)',
                    'data' => $amountDevis 
                ],
            ]
        ]);

        $chartDevis->setOptions([
            'maintainAspectRatio' => false,
        ]);

        return $chartDevis;
    }

    public function chartClientFactures($annee) {
        
        $amountFactures = [];

        

        for ($i=1; $i < 13; $i++) {

            $date = new DateTime($annee . '-' . $i . '-01');

            if ($this->facturesRepository->amountFacturesMensuelClient($date->format('Y'), $date->format('m'), $this->security->getUser()) === null) {
                $amountFactures[] = "0";
            }
            else {
                $amountFactures[] = $this->facturesRepository->amountFacturesMensuelClient($date->format('Y'), $date->format('m'), $this->security->getUser());
            }
        }

        $chartFactures = $this->chartBuilder->createChart(Chart::TYPE_BAR);

        $chartFactures->setData([
            'labels' => ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
            'datasets' => [
                [
                    'label' => 'Factures',
                    'tension' => 0.2,
                    'radius' => 5,
                    'borderWidth' => 3,
                    'backgroundColor' => 'rgb(54, 162, 235)',
                    'borderColor' => 'rgb(54, 162, 235)',
                    'data' => $amountFactures 
                ],
            ]
        ]);

        $chartFactures->setOptions([
            'maintainAspectRatio' => false,
        ]);

        return $chartFactures;
    }

    public function chartClientProjets($annee) {
        
        $projets = [];

        

        for ($i=1; $i < 13; $i++) {

            $date = new DateTime($annee . '-' . $i . '-01');

            if ($this->projetsRepository->nombreProjetsMensuelClient($date->format('Y'), $date->format('m'), $this->security->getUser()) === null) {
                $projets[] = "0";
            }
            else {
                $projets[] = $this->projetsRepository->nombreProjetsMensuelClient($date->format('Y'), $date->format('m'), $this->security->getUser());
            }
        }

        $chartProjets = $this->chartBuilder->createChart(Chart::TYPE_BAR);

        $chartProjets->setData([
            'labels' => ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
            'datasets' => [
                [
                    'label' => 'Projets',
                    'tension' => 0.2,
                    'radius' => 5,
                    'borderWidth' => 3,
                    'backgroundColor' => 'rgb(54, 162, 235)',
                    'borderColor' => 'rgb(54, 162, 235)',
                    'data' => $projets 
                ],
            ]
        ]);

        $chartProjets->setOptions([
            'maintainAspectRatio' => false,
        ]);

        return $chartProjets;
    }
}