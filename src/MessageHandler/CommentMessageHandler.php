<?php

namespace App\MessageHandler;

use App\Components\SpamChecker;
use App\Message\CommentMessage;
use App\Repository\CommentRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

final class CommentMessageHandler implements MessageHandlerInterface
{
    private CommentRepository $commentRepository;
    private SpamChecker $spamChecker;
    private MessageBusInterface $bus;
    private WorkflowInterface $workflow;
    private LoggerInterface $logger;

    public function __construct(CommentRepository $commentRepository, SpamChecker $spamChecker, MessageBusInterface $bus, WorkflowInterface $commentStateMachine, LoggerInterface $logger = null)
    {
        $this->commentRepository    = $commentRepository;
        $this->spamChecker          = $spamChecker;
        $this->workflow             = $commentStateMachine;
        $this->logger               = $logger;
        $this->bus                  = $bus;
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


        if($this->workflow->can($comment, 'accept')) {
            $score = $this->spamChecker->getSpamScore($comment, $message->getContext());
            $transition = 'accept';
            
            if($score === 2) {
                $transition = 'reject_spam';
            } elseif ($score === 1) {
                $transition = 'might_be_spam';
            }

            $this->workflow->apply($comment, $transition);
            $this->commentRepository->save($comment, true);
            $this->bus->dispatch($message);
        } elseif ($this->workflow->can($comment, 'publish') || $this->workflow->can($comment, 'publish_ham')) {
            $this->workflow->apply($comment, $this->workflow->can($comment, 'publish') ? 'publish' : 'publish_ham');
            $this->commentRepository->save($comment, true);
        }
        elseif ($this->logger) {
            $this->logger->debug('Dropping comment message', ['comment' => $comment->getId(), 'status' => $comment->getStatus()]);
        }

    }
}
