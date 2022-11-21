<?php

namespace App\MessageHandler;

use App\Components\SpamChecker;
use App\Message\CommentMessage;
use App\Repository\CommentRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

final class CommentMessageHandler implements MessageHandlerInterface
{
    private CommentRepository $commentRepository;
    private SpamChecker $spamChecker;

    public function __construct(CommentRepository $commentRepository, SpamChecker $spamChecker)
    {
        $this->commentRepository = $commentRepository;
        $this->spamChecker       = $spamChecker;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function __invoke(CommentMessage $message)
    {
        $comment = $this->commentRepository->find($message->getId());
        if (!$comment) {
            return;
        }

        if (2 === $this->spamChecker->getSpamScore($comment, $message->getContext())) {
            $comment->setState('spam');
        } else {
            $comment->setState('published');
        }

        $this->commentRepository->save($comment, true);
    }
}
