<?php

namespace App\Controller\Client;

use DateTime;
use App\Entity\Messages;
use App\Entity\DocsMessages;
use App\Services\UploadService;
use App\Form\Client\MessagesType;
use App\Repository\UsersRepository;
use App\Repository\MessagesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/panel/messagerie')]
class MessagesController extends AbstractController
{
    #[Route('/', name: 'app_client_messages_index', methods: ['GET'])]
    public function index(MessagesRepository $messagesRepository): Response
    {
        return $this->render('client/messages/index.html.twig', [
            'messages' => $messagesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_client_messages_new', methods: ['GET', 'POST'])]
    public function new(Request $request, MessagesRepository $messagesRepository, UsersRepository $usersRepository, UploadService $uploadService): Response
    {
        $message = new Messages();
        $form = $this->createForm(MessagesType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $fichiers = $form->get('docsMessages')->getData();
            $directory = 'messages_directory';


            foreach ($fichiers as $fichier) {

                $document = new DocsMessages();
                $document->setUrl('documents/messagerie/' .$uploadService->send($fichier, $directory));
                $document->setNom(substr($document->getUrl(), 21));
                $message->addDocsMessage($document);
                
            }

            $recipient = $usersRepository->findOneBy(['id' => 2]);

            $message->setSender($this->getUser())
                    ->setRecipient($recipient)
                    ->setCreatedAt(new DateTime());

            $messagesRepository->save($message, true);

            $this->addFlash('success', '<span class="me-2 fa fa-circle-check"></span>Votre message a été envoyé avec succès');

            return $this->redirectToRoute('app_client_messages_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('client/messages/new.html.twig', [
            'message' => $message,
            'form' => $form,
        ]);
    }

    #[Route('/lire/{id}', name: 'app_client_messages_show', methods: ['GET'])]
    public function show(Messages $message, MessagesRepository $messagesRepository): Response
    {
        if ($message->getRecipient() !== $this->getUser()) {
            throw new AccessDeniedException("Vous n'avez pas l'autorisation d'accéder à cette page");
        }

        $message->setLu(true);
        $messagesRepository->save($message, true);

        return $this->render('client/messages/show.html.twig', [
            'message' => $message,
        ]);
    }

    #[Route('/document/{nom}', name: 'app_client_messages_voir_doc', methods: ['GET'])]
    public function document(DocsMessages $docsMessages)
    {
/*
        if ($docsMessages->getSender() !== $this->getUser()) {
            throw new AccessDeniedException("Vous n'avez pas l'autorisation d'accéder à cette page");
        }
*/

        
        $document = '/' .$docsMessages->getUrl();
        $ext = pathinfo($document, PATHINFO_EXTENSION);

        if ($ext === 'jpg' OR $ext === 'jpeg') {
            $mime = "image/jpeg";
        }
        elseif ($ext === 'png') {
            $mime = "image/png";
        }
        else {
            $mime = "application/pdf";
        }

        $fichier = $docsMessages->getUrl();

            header('Content-type: ' . $mime);

            if ($docsMessages->getMessage()->getRecipient() !== $this->getUser()) {
                throw new AccessDeniedException("Vous n'avez pas l'autorisation d'accéder à cette page");
            }
            else {
                readfile($fichier);
            }
            
  
    }

    #[Route('/repondre/{id}', name: 'app_client_messages_reponse', methods: ['GET', 'POST'])]
    public function response(Request $request, Messages $parent, MessagesRepository $messagesRepository, UploadService $uploadService): Response
    {
        $message = new Messages();
        $message->setSujet('Re: ' .$parent->getSujet());
        $message->setSender($this->getUser());
        $message->setMessage('<br>' .$parent->getMessage());
        $message->setRecipient($parent->getSender());
        //dd($parent->getSender()->getNom());
        //$test = 'Votre réponse<br>merci';
        //$message->setMessage(nl2br("foo isn't\n bar"));

        $form = $this->createForm(MessagesType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {



            $fichiers = $form->get('docsMessages')->getData();
            $directory = 'messages_directory';


            foreach ($fichiers as $fichier) {

                $document = new DocsMessages();
                $document->setUrl('documents/messagerie/' .$uploadService->send($fichier, $directory));
                $document->setNom(substr($document->getUrl(), 21));
                $message->addDocsMessage($document);
                
            }
            $message->setSender($this->getUser())
                    ->setCreatedAt(new DateTime());


            $messagesRepository->save($message, true);

            return $this->redirectToRoute('app_client_messages_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('client/messages/new.html.twig', [
            'message' => $message,
            'form' => $form,
        ]);
    }
}