<?php

namespace App\Controller\Public;

use App\Entity\Category;
use App\Entity\News;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CategoryRepository;
use App\Repository\NewsRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/news')]
class NewsController extends AbstractController
{
    #[Route('/category/{id}', name: 'app_news_category')]
    public function category(
        Category $category,
        NewsRepository $newsRepository,
        Request $request
    ): Response {
        $page = $request->query->getInt('page', 1);
        $paginator = $newsRepository->findByCategoryPaginated($category, $page);

        $totalItems = count($paginator);
        $itemsPerPage = 10;
        $totalPages = ceil($totalItems / $itemsPerPage);

        return $this->render('public/news/category.html.twig', [
            'category' => $category,
            'news' => $paginator,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    #[Route('/view/{id}', name: 'app_news_view')]
    public function view(
        News $news,
        CommentRepository $commentRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setNews($news);
            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Comment added successfully!');
            return $this->redirectToRoute('app_news_view', ['id' => $news->getId()]);
        }

        $comments = $commentRepository->findByNews($news);

        return $this->render('public/news/view.html.twig', [
            'news' => $news,
            'comments' => $comments,
            'commentForm' => $form->createView(),
        ]);
    }
}

?>