<?php

namespace App\Controller\Public;

use App\Repository\CategoryRepository;
use App\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        CategoryRepository $categoryRepository,
        NewsRepository $newsRepository
    ): Response {
        $categories = $categoryRepository->findAll();
        $categoriesWithNews = [];

        foreach ($categories as $category) {
            $latestNews = $newsRepository->findLatestByCategory($category, 3);
            if (!empty($latestNews)) {
                $categoriesWithNews[] = [
                    'category' => $category,
                    'news' => $latestNews
                ];
            }
        }

        return $this->render('public/home.html.twig', [
            'categoriesWithNews' => $categoriesWithNews,
        ]);
    }
}

?>