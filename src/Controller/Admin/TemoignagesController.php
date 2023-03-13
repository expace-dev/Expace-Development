<?php

namespace App\Controller\Admin;

use App\Entity\Temoignages;
use App\Form\Admin\TemoignagesType;
use App\Repository\TemoignagesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/temoignages')]
class TemoignagesController extends AbstractController
{
    /**
     * Permet de lister les témoignages client
     *
     * @return Response
     */
    #[Route('/', name: 'app_admin_temoignages_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/temoignages/index.html.twig');
    }

    /**
     * Permet d'éditer un témoignage client
     * 
     * @param Request $request
     * @param Temoignages $temoignage
     * @param TemoignagesRepository $temoignagesRepository
     * @return Response
     */
    #[Route('/{id}/edit', name: 'app_admin_temoignages_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Temoignages $temoignage, TemoignagesRepository $temoignagesRepository): Response
    {
        $form = $this->createForm(TemoignagesType::class, $temoignage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $temoignagesRepository->save($temoignage, true);

            $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Votre témoignage a été enregistré avec succès');

            return $this->redirectToRoute('app_admin_temoignages_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/temoignages/edit.html.twig', [
            'temoignage' => $temoignage,
            'form' => $form,
        ]);
    }

    /**
     * Permet de supprimer un témoignage client
     * 
     * @param Temoignages $temoignage
     * @param TemoignagesRepository $temoignagesRepository
     * @return Response
     */
    #[Route('/delete/{id}', name: 'app_admin_temoignages_delete', methods: ['GET'])]
    public function delete(Temoignages $temoignage, TemoignagesRepository $temoignagesRepository): Response
    {
        $temoignagesRepository->remove($temoignage, true);

        $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Votre témoignage a été supprimmé avec succès');

        return $this->redirectToRoute('app_admin_temoignages_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Permet d'activer ou désactiver un témoignage client
     * 
     * @param Temoignages $temoignage
     * @param TemoignagesRepository $temoignagesRepository
     * @return Response
     */
    #[Route('/update/{id}', name: 'app_admin_temoignages_update', methods: ['GET'])]
    public function update(Temoignages $temoignage, TemoignagesRepository $temoignagesRepository): Response
    {
        if ($temoignage->isActif() === true) {

            $temoignage->setActif(false);

            $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Votre témoignage a été désactivé avec succès');
        
        }
        else {
            
            $temoignage->setActif(true);

            $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Votre témoignage a été activé avec succès');
        
        }

        $temoignagesRepository->save($temoignage, true);


        return $this->redirectToRoute('app_admin_temoignages_index', [], Response::HTTP_SEE_OTHER);
    }
}
