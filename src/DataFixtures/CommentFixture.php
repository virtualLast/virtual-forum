<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CommentFixture extends Fixture implements DependentFixtureInterface
{
    private const COMMENT_MAX_COUNT  = 200;
    public const COMMENT_REFERENCE   = 'comment_';

    public function load(ObjectManager $manager): void
    {
        $now = new \DateTimeImmutable();
        $faker = Factory::create();
        for($i = 0; $i <= self::COMMENT_MAX_COUNT; $i++) {
            $comment = new Comment();
            $comment->setTitle($faker->text());
            $comment->setContent($faker->paragraphs());
            $comment->setEdited($faker->optional()->boolean());
            $comment->setVoteDown($faker->optional()->randomDigitNotZero());
            $comment->setVoteUp($faker->optional()->randomDigitNotZero());
            $comment->setCreatedAt($now);
            $comment->setUpdatedAt($now);
            $comment->setStatus($faker->randomElement(['published', 'rejected', 'submitted']));
            $comment->setCreatedBy($this->getReference(UserFixture::USER_REFERENCE . rand(0, UserFixture::USER_MAX_COUNT)));
            $comment->setQuestion($this->getReference(QuestionFixture::QUESTION_REFERENCE . rand(0, QuestionFixture::QUESTION_MAX_COUNT)));
            $manager->persist($comment);
            $this->addReference(self::COMMENT_REFERENCE . $i, $comment);
        }

        $manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @psalm-return array<class-string<FixtureInterface>>
     */
    public function getDependencies()
    {
        return [
          UserFixture::class,
          QuestionFixture::class
        ];
    }
}
