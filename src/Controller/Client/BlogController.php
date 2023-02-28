<?php

namespace App\Controller\Client;

use DateTime;
use App\Entity\Articles;
use App\Entity\Comments;
use App\Repository\ArticlesRepository;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\Client\Articles\CommentType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/articles')]
class BlogController extends AbstractController
{


    
    /**
     * Permet de lister les articles
     *
     * @param Request $request
     * @param ArticlesRepository $articlesRepository
     * @param CategoriesRepository $categoriesRepository
     * @return Response
     */
    #[Route('/', name: 'app_blog_index', methods: ['GET', 'POST'])]
    public function index(): Response
    {
        return $this->render('client/blog/index.html.twig');
    }

    /**
     * Permet de lister les articles d'une catégorie
     */
    #[Route('/{slug}', name: 'app_blog_categories', methods: ['GET', 'POST'])]
    public function categoriesAnnonce(): Response
    {
        
        return $this->render('client/blog/index.html.twig');
    }


    /**
     * Permet d'afficher un article en particulier
     */
    #[Route('/details/{id}', name: 'app_blog_show', methods: ['GET', 'POST'])]
    public function show($id, CategoriesRepository $categoriesRepository, ArticlesRepository $articlesRepository, Request $request, Articles $article, EntityManagerInterface $manager): Response
    {

        

        $commentaires = new Comments();
        $form = $this->createForm(CommentType::class, $commentaires);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $commentaires->setCreatedAt(new DateTime());
            $commentaires->setArticles($article);
            $commentaires->setAuteur($this->getUser());


            // On récupère le contenu du champ parentid
            $parentid = $form->get("parentid")->getData();

            // On va chercher le commentaire correspondant
            if($parentid != null){
                $parent = $manager->getRepository(Comments::class)->find($parentid);
            }

            // On définit le parent
            $commentaires->setParent($parent ?? null);

            $manager->persist($commentaires);
            $manager->flush();

            $this->addFlash('primary', 'Votre commentaire a bien été publié');

            $form = $this->createForm(CommentType::class);


        }

        return $this->render('client/blog/show.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

}
