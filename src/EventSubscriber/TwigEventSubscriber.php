<?php

namespace App\EventSubscriber;

use App\Service\AvatarService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class TwigEventSubscriber implements EventSubscriberInterface
{
    private Environment $twig;
    private AvatarService $avatarService;
    private Security $security;

    public function __construct(Environment $twig, AvatarService $avatarService, Security $security)
    {
        $this->twig             = $twig;
        $this->avatarService    = $avatarService;
        $this->security         = $security;
    }

    public function onControllerEvent(ControllerEvent $event): void
    {
        $this->twig->addGlobal('avatar', $this->avatarService->fetchAvatar($this->security->getUser()->getUserIdentifier()));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ControllerEvent::class => 'onControllerEvent',
        ];
    }
}
