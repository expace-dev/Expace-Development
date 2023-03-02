<?php

namespace App\Controller\Admin;

use App\Entity\Users;
use App\Form\ProfilType;
use App\Form\CredentialsType;
use App\Form\RegistrationFormType;
use App\Repository\UsersRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/admin/users')]
class UsersController extends AbstractController
{
    #[Route('/', name: 'app_admin_users_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('users/index.html.twig');
    }

    #[Route('/new', name: 'app_admin_users_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UsersRepository $usersRepository, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setFullName($user->getNom(). ' ' .$user->getPrenom());



            $usersRepository->save($user, true);

            $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Compte client enregistré avec succès');

            return $this->redirectToRoute('app_admin_users_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('users/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_users_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Users $user, UsersRepository $usersRepository): Response
    {
        $profilForm = $this->createForm(ProfilType::class, $user);
        $profilForm->handleRequest($request);

        if ($profilForm->isSubmitted() && $profilForm->isValid()) {
            $usersRepository->save($user, true);

            $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Compte client enregistré avec succès');

            return $this->redirectToRoute('app_admin_users_index', [], Response::HTTP_SEE_OTHER);
        }

        $credentialsForm = $this->createForm(CredentialsType::class, $user);
        $credentialsForm->handleRequest($request);

        if ($credentialsForm->isSubmitted() && $credentialsForm->isValid()) {
            $usersRepository->save($user, true);

            $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Compte client enregistré avec succès');

            return $this->redirectToRoute('app_admin_users_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('users/edit.html.twig', [
            'user' => $user,
            'credentialsForm' => $credentialsForm,
            'profilForm' => $profilForm
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_users_delete', methods: ['GET'])]
    public function delete(Request $request, Users $user, UsersRepository $usersRepository): Response
    {
            $usersRepository->remove($user, true);

            $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Compte client supprimmé avec succès');

        return $this->redirectToRoute('app_admin_users_index', [], Response::HTTP_SEE_OTHER);
    }
}
