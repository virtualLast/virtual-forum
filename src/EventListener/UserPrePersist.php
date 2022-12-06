<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPrePersist
{
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    // the entity listener methods receive two arguments:
    // the entity instance and the lifecycle event
    public function prePersist(User $user, LifecycleEventArgs $event): void
    {
        if (!empty($user->getPlainPassword())) {
            $user->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $user,
                    $user->getPlainPassword()
                )
            );
        }
    }
}
