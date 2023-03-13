<?php

namespace App\Controller;

use App\Form\ProfilType;
use App\Form\CredentialsType;
use App\Repository\UsersRepository;
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
    public function edit(Request $request, UsersRepository $usersRepository): Response
    {
        $user = $this->getUser();

        $profilForm = $this->createForm(ProfilType::class, $user);
        $profilForm->handleRequest($request);

        if ($profilForm->isSubmitted() && $profilForm->isValid()) {
            $usersRepository->save($user, true);

            return $this->redirectToRoute('app_profil_edit', [], Response::HTTP_SEE_OTHER);
        }

        $credentialsForm = $this->createForm(CredentialsType::class, $user);
        $credentialsForm->handleRequest($request);

        if ($credentialsForm->isSubmitted() && $credentialsForm->isValid()) {
            $usersRepository->save($user, true);

            return $this->redirectToRoute('app_profil_edit', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('users/edit.html.twig', [
            'user' => $user,
            'credentialsForm' => $credentialsForm,
            'profilForm' => $profilForm
        ]);
    }
}
