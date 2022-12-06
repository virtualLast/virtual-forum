<?php

namespace App\DataFixtures;

use App\Entity\Question;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class QuestionFixture extends Fixture implements DependentFixtureInterface
{
    public const QUESTION_MAX_COUNT  = 100;
    public const QUESTION_REFERENCE  = 'question_';

    public function load(ObjectManager $manager): void
    {
        $now = new \DateTimeImmutable();
        $faker = Factory::create();
        for($i = 0; $i <= self::QUESTION_MAX_COUNT; $i++) {
            $question = new Question();
            $question->setTitle($faker->text());
            $question->setContent($faker->paragraphs(rand(1,3), true));
            $question->setEdited($faker->optional()->boolean());
            $question->setVoteDown($faker->optional()->randomDigitNotZero());
            $question->setVoteUp($faker->optional()->randomDigitNotZero());
            $question->setCreatedAt($now);
            $question->setUpdatedAt($now);
            $question->setStatus('published');
            $question->setCreatedBy($this->getReference(UserFixture::USER_REFERENCE . rand(0, UserFixture::USER_MAX_COUNT)));
            $manager->persist($question);
            $this->addReference(self::QUESTION_REFERENCE . $i, $question);
        }

        $manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @psalm-return array{0: UserFixture::class}
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            UserFixture::class
        ];
    }
}
