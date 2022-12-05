<?php

namespace App\MessageHandler;

use App\Components\SpamChecker;
use App\Message\QuestionMessage;
use App\Repository\QuestionRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

final class QuestionMessageHandler implements MessageHandlerInterface
{
    private QuestionRepository $questionRepository;
    private SpamChecker $spamChecker;
    private MessageBusInterface $bus;
    private WorkflowInterface $workflow;

    public function __construct(QuestionRepository $questionRepository, SpamChecker $spamChecker, MessageBusInterface $bus, WorkflowInterface $questionStateMachine)
    {
        $this->questionRepository    = $questionRepository;
        $this->spamChecker          = $spamChecker;
        $this->workflow             = $questionStateMachine;
        $this->bus                  = $bus;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function __invoke(QuestionMessage $message)
    {
        $question = $this->questionRepository->find($message->getId());
        if (!$question) {
            return;
        }


        if($this->workflow->can($question, 'accept')) {
            $score = $this->spamChecker->getSpamScore($question, $message->getContext());
            $transition = 'accept';

            if($score === 2) {
                $transition = 'reject_spam';
            } elseif ($score === 1) {
                $transition = 'might_be_spam';
            }

            $this->workflow->apply($question, $transition);
            $this->questionRepository->save($question, true);
            $this->bus->dispatch($message);
        } elseif ($this->workflow->can($question, 'publish') || $this->workflow->can($question, 'publish_ham')) {
            $this->workflow->apply($question, $this->workflow->can($question, 'publish') ? 'publish' : 'publish_ham');
            $this->questionRepository->save($question, true);
        }

    }
}
