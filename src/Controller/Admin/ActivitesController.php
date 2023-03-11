<?php

namespace App\Controller\Admin;

use App\Entity\Activites;
use App\Form\Admin\ActivitesType;
use App\Repository\ActivitesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/activites')]
class ActivitesController extends AbstractController
{
    #[Route('/new', name: 'app_admin_activites_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ActivitesRepository $activitesRepository): Response
    {
        $activite = new Activites();
        $form = $this->createForm(ActivitesType::class, $activite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $debut = strtotime(date_format($activite->getDebut(),"H:i:s"));
            $fin = strtotime(date_format($activite->getFin(),"H:i:s"));

            $total = ($fin-$debut)/60/60;

            $activite->setTotal($total);

            $activitesRepository->save($activite, true);

            $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Les données ont été enregistré avec succès');

            return $this->redirectToRoute('app_admin_activites_new', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/activites/new.html.twig', [
            'activite' => $activite,
            'form' => $form,
        ]);
    }

}
