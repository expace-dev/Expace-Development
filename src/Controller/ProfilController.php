<?php

namespace App\Controller;

use App\Form\ProfilType;
use App\Form\CredentialsType;
use App\Repository\UsersRepository;
use App\Services\UploadService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/profile/profil')]
class ProfilController extends AbstractController
{
    /**
     * Permet de modifier le profil
     *
     * @param Request $request
     * @param UsersRepository $usersRepository
     * @return Response
     */
    #[Route('/edit', name: 'app_profil_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UsersRepository $usersRepository, UploadService $uploadService): Response
    {
        $user = $this->getUser();

        $profilForm = $this->createForm(ProfilType::class, $user);
        $profilForm->handleRequest($request);

        if ($profilForm->isSubmitted() && $profilForm->isValid()) {

            if ($profilForm->get('avatar')->getData()) {

                
                // On récupère l'image
                $fichier = $profilForm->get('avatar')->getData();
                if ($user->getAvatar()) {
                    
                    unlink(substr($user->getAvatar(),1));
                }
                // On récupère le dossier de destination
                $directory = 'avatar_directory';
                /**
                * On ajoute l'image et l'utilisateur connecté à l'article
                * et ont upload l'image via UploadService
                */
                $user->setAvatar('/images/avatar/' .$uploadService->send($fichier, $directory));
            }

            $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Votre profil a été enregistré avec succès');

            $usersRepository->save($user, true);

            return $this->redirectToRoute('app_profil_edit', [], Response::HTTP_SEE_OTHER);
        }

        $credentialsForm = $this->createForm(CredentialsType::class, $user);
        $credentialsForm->handleRequest($request);

        if ($credentialsForm->isSubmitted() && $credentialsForm->isValid()) {
            
            $usersRepository->save($user, true);

            $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Vos identifiants a été enregistré avec succès');

            return $this->redirectToRoute('app_profil_edit', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('users/edit.html.twig', [
            'user' => $user,
            'credentialsForm' => $credentialsForm,
            'profilForm' => $profilForm
        ]);
    }
}
