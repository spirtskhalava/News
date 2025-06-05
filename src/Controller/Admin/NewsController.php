<?php

namespace App\Controller\Admin;

use App\Entity\News;
use App\Form\NewsType;
use App\Repository\NewsRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/news')]
class NewsController extends AbstractController
{
    #[Route('/', name: 'app_admin_news_index')]
    public function index(NewsRepository $newsRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $paginator = $newsRepository->findAllPaginated($page);

        $totalItems = count($paginator);
        $itemsPerPage = 10;
        $totalPages = ceil($totalItems / $itemsPerPage);

        return $this->render('admin/news/index.html.twig', [
            'news' => $paginator,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    #[Route('/new', name: 'app_admin_news_new')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        $news = new News();
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFile = $form->get('pictureFile')->getData();

            if ($pictureFile) {
                $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pictureFile->guessExtension();

                try {
                    $pictureFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                    $news->setPicture($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Error uploading file: ' . $e->getMessage());
                }
            }

            $entityManager->persist($news);
            $entityManager->flush();

            $this->addFlash('success', 'News created successfully!');
            return $this->redirectToRoute('app_admin_news_index');
        }

        return $this->render('admin/news/new.html.twig', [
            'news' => $news,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_news_edit')]
    public function edit(
        Request $request,
        News $news,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFile = $form->get('pictureFile')->getData();

            if ($pictureFile) {
                $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pictureFile->guessExtension();

                try {
                    $pictureFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                    $news->setPicture($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Error uploading file: ' . $e->getMessage());
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'News updated successfully!');
            return $this->redirectToRoute('app_admin_news_index');
        }

        return $this->render('admin/news/edit.html.twig', [
            'news' => $news,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_news_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        News $news,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$news->getId(), $request->request->get('_token'))) {
            $entityManager->remove($news);
            $entityManager->flush();
            $this->addFlash('success', 'News deleted successfully!');
        }

        return $this->redirectToRoute('app_admin_news_index');
    }

    #[Route('/{id}/comments', name: 'app_admin_news_comments')]
    public function comments(News $news, CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->findByNews($news);

        return $this->render('admin/news/comments.html.twig', [
            'news' => $news,
            'comments' => $comments,
        ]);
    }
}

?>