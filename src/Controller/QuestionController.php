<?php

namespace App\Controller;

use App\Repository\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends AbstractController
{
    private QuestionRepository $questionRepository;

    public function __construct(QuestionRepository $questionRepository)
    {
        $this->questionRepository = $questionRepository;
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
        return $this->render('question/question.html.twig', [
            'question' => $this->questionRepository->findOneBy(['id' => $id])
        ]);
    }
}
