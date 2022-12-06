<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UserPostPersist
{
    public function postPersist(User $user, LifecycleEventArgs $event): void
    {
        $user->eraseCredentials();
    }
}
