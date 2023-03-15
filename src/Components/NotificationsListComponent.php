<?php

namespace App\Components;

use App\Repository\DevisRepository;
use App\Repository\NotificationsRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('notifications_list')]
class NotificationsListComponent {

    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public ?string $query = null;




    public function __construct(private NotificationsRepository $notificationsRepository, private RequestStack $requestStack, private Security $security)
    {
    }

    public function getAllNotifications(): array {

        $request = $this->requestStack->getCurrentRequest();
        
        $page = $request->query->getInt('page', 1);

        $user = $this->security->getUser();

        return $this->notificationsRepository->findNotificationsClient($page, 15, $user);
    }
    
}