<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Question;
use App\Form\CommentFormType;
use App\Form\QuestionFormType;
use App\Repository\CommentRepository;
use App\Repository\QuestionRepository;
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
            'questions' => $this->questionRepository->findAllPublished(),
            'controller_name' => 'QuestionController',
        ]);
    }

    #[Route('/question/create', name: 'question_create')]
    public function createQuestion(Request $request): Response
    {
        $question = new Question();
        $form = $this->createForm(QuestionFormType::class, $question);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $question->setCreatedBy($this->getUser());
            $this->questionRepository->save($question, true);

            $savedQuestion = $this->questionRepository->findOneBy(['createdBy' => $this->getUser()], ['createdAt' => 'DESC']);
            return $this->redirectToRoute('question', ['id' => $savedQuestion->getId()]);
        }

        return $this->render('question/create.html.twig', [
            'question_form'  => $this->getUser() ? $form->createView() : null
        ]);
    }

    #[Route('/question/{id}', name: 'question')]
    public function question(Request $request, int $id): Response
    {

        $question = $this->questionRepository->findOneBy(['id' => $id]);

        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $this->commentRepository->getCommentPaginator($question, $offset);

        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedBy($this->getUser());
            $comment->setQuestion($question);

            $this->commentRepository->save($comment, true);
            return $this->redirectToRoute($request->get('_route'), ['id' => $id]);
            // what now, redirect somewhere else or refresh the page or pop in the comment
        }

        return $this->render('question/question.html.twig', [
            'question'      => $question,
            'comments'      => $paginator,
            'previous'      => $offset - CommentRepository::COMMENT_PAGINATOR_PER_PAGE,
            'next'          => min(count($paginator),$offset + CommentRepository::COMMENT_PAGINATOR_PER_PAGE ),
            'last_page'     => count($paginator),
            'offset'        => $offset,
            'comment_form'  => $this->getUser() ? $form->createView() : null
        ]);
    }

}
