<?php

namespace App\Repository;

use App\Components\SpamChecker;
use App\Entity\Comment;
use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use RuntimeException;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * @extends ServiceEntityRepository<Comment>
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public const COMMENT_PAGINATOR_PER_PAGE = 4;
    private SpamChecker $spamChecker;

    public function __construct(ManagerRegistry $registry, SpamChecker $spamChecker,)
    {
        $this->spamChecker = $spamChecker;
        parent::__construct($registry, Comment::class);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function save(Comment $entity, Request $request, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        // spam checker
        $context = [
            'user_ip' => $request->getClientIp(),
            'user_agent' => $request->headers->get('user-agent'),
            'referrer' => $request->headers->get('referer'),
            'permalink' => $request->getUri(),
        ];
        if (2 === $this->spamChecker->getSpamScore($entity, $context)) {
            throw new RuntimeException('Blatant spam, go away!');
        }

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Comment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getCommentPaginator(Question $question, int $offset): Paginator
    {
        $query = $this->createQueryBuilder('c')
            ->andWhere('c.question = :question')
            ->andWhere('c.status = :status')
            ->setParameter('question', $question)
            ->setParameter('status', 'published')
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults(self::COMMENT_PAGINATOR_PER_PAGE)
            ->setFirstResult($offset)
            ->getQuery();

        return new Paginator($query);
    }

//    /**
//     * @return Comment[] Returns an array of Comment objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Comment
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
