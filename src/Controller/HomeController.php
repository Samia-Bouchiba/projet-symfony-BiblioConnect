<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(BookRepository $bookRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'recent_books' => $bookRepository->findBy([], ['id' => 'DESC'], 8),
            'categories'   => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/books', name: 'app_books')]
    public function books(
        Request $request,
        BookRepository $bookRepository,
        CategoryRepository $categoryRepository,
        PaginatorInterface $paginator
    ): Response {
        $query      = $request->query->get('q');
        $categoryId = $request->query->getInt('category') ?: null;

        $qb = $bookRepository->createFilteredQueryBuilder($query, $categoryId);

        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            12
        );

        return $this->render('home/books.html.twig', [
            'pagination'        => $pagination,
            'categories'        => $categoryRepository->findAll(),
            'query'             => $query,
            'selected_category' => $categoryId,
        ]);
    }

    #[Route('/books/{id}', name: 'app_book_show')]
    public function show(int $id, BookRepository $bookRepository): Response
    {
        $book = $bookRepository->find($id);
        if (!$book) {
            throw $this->createNotFoundException('Livre non trouvé.');
        }

        return $this->render('home/book_show.html.twig', [
            'book' => $book,
        ]);
    }
}
