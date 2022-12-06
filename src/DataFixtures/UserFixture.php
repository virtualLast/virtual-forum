<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Provider\en_US\Person;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    private const USER_PASSWORD = 'password_';
    public const USER_REFERENCE = 'user_';
    public const USER_MAX_COUNT = 40;

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $now = new \DateTimeImmutable();
        $faker = Factory::create();
        $faker->addProvider(new Person($faker));
        for ($i = 0; $i <= self::USER_MAX_COUNT; ++$i) {
            $user = new User();
            $user->setUsername($faker->unique()->userName());
            $user->setEmail($faker->unique()->email());
            $user->setName($faker->name());
            $user->setCreatedAt($now);
            $user->setUpdatedAt($now);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->hasher->hashPassword($user, self::USER_PASSWORD.$i));
            $manager->persist($user);
            $this->addReference(self::USER_REFERENCE.$i, $user);
        }

        $manager->flush();
    }
}
