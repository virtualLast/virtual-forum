<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentFormType;
use App\Repository\CommentRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends AbstractController
{
    private QuestionRepository $questionRepository;
    private CommentRepository $commentRepository;

    public function __construct(QuestionRepository $questionRepository, CommentRepository $commentRepository)
    {
        $this->questionRepository   = $questionRepository;
        $this->commentRepository    = $commentRepository;
    }

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('question/index.html.twig', [
            'questions' => $this->questionRepository->findAll(),
            'controller_name' => 'QuestionController',
        ]);
    }

    #[Route('/question/{id}', name: 'question')]
    public function question(Request $request, int $id): Response
    {

        $question = $this->questionRepository->findOneBy(['id' => $id]);
        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedBy($this->getUser());
            $comment->setQuestion($question);

            $this->commentRepository->save($comment, true);
            // what now, redirect somewhere else or refresh the page or pop in the comment
        }

        return $this->render('question/question.html.twig', [
            'question'      => $question,
            'comment_form'  => $this->getUser() ? $form->createView() : null
        ]);
    }
}
