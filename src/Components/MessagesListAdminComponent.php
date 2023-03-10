<?php

namespace App\Components;

use App\Repository\DevisRepository;
use App\Repository\UsersRepository;
use App\Repository\ProjetsRepository;
use App\Repository\MessagesRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsLiveComponent('messages_list_admin')]
class MessagesListAdminComponent {

    use DefaultActionTrait;


    public function __construct(private MessagesRepository $messagesRepository, private RequestStack $requestStack, private Security $security)
    {
    }

    public function getAllMessages(): array {

        $request = $this->requestStack->getCurrentRequest();
        
        $page = $request->query->getInt('page', 1);

        $user = $this->security->getUser();

        return $this->messagesRepository->findMessages($page, 15, $user);
    }
    
}