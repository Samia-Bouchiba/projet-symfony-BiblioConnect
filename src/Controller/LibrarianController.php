<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Category;
use App\Entity\Language;
use App\Form\AuthorFormType;
use App\Form\BookFormType;
use App\Form\CategoryFormType;
use App\Form\LanguageFormType;
use App\Repository\BookRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_LIBRARIAN')]
#[Route('/librarian')]
class LibrarianController extends AbstractController
{
    #[Route('', name: 'app_librarian_dashboard')]
    public function dashboard(BookRepository $bookRepo, ReservationRepository $resRepo): Response
    {
        return $this->render('librarian/dashboard.html.twig', [
            'total_books' => count($bookRepo->findAll()),
            'pending_reservations' => $resRepo->findPending(),
        ]);
    }

    // ─── Books ───────────────────────────────────────────────────────────────

    #[Route('/books', name: 'app_librarian_books')]
    public function books(BookRepository $bookRepo): Response
    {
        return $this->render('librarian/books/index.html.twig', [
            'books' => $bookRepo->findBy([], ['title' => 'ASC']),
        ]);
    }

    #[Route('/books/new', name: 'app_librarian_book_new', methods: ['GET', 'POST'])]
    public function bookNew(Request $request, EntityManagerInterface $em): Response
    {
        $book = new Book();
        $form = $this->createForm(BookFormType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($book);
            $em->flush();
            $this->addFlash('success', 'Livre ajouté avec succès.');
            return $this->redirectToRoute('app_librarian_books');
        }

        return $this->render('librarian/books/form.html.twig', [
            'form' => $form,
            'title' => 'Ajouter un livre',
        ]);
    }

    #[Route('/books/{id}/edit', name: 'app_librarian_book_edit', methods: ['GET', 'POST'])]
    public function bookEdit(int $id, Request $request, BookRepository $bookRepo, EntityManagerInterface $em): Response
    {
        $book = $bookRepo->find($id);
        if (!$book) throw $this->createNotFoundException();

        $form = $this->createForm(BookFormType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Livre modifié avec succès.');
            return $this->redirectToRoute('app_librarian_books');
        }

        return $this->render('librarian/books/form.html.twig', [
            'form' => $form,
            'title' => 'Modifier le livre',
            'book' => $book,
        ]);
    }

    #[Route('/books/{id}/delete', name: 'app_librarian_book_delete', methods: ['POST'])]
    public function bookDelete(int $id, Request $request, BookRepository $bookRepo, EntityManagerInterface $em): Response
    {
        $book = $bookRepo->find($id);
        if ($book && $this->isCsrfTokenValid('delete_book' . $id, $request->request->get('_token'))) {
            $em->remove($book);
            $em->flush();
            $this->addFlash('success', 'Livre supprimé.');
        }
        return $this->redirectToRoute('app_librarian_books');
    }

    // ─── Reservations ─────────────────────────────────────────────────────────

    #[Route('/reservations', name: 'app_librarian_reservations')]
    public function reservations(ReservationRepository $resRepo): Response
    {
        return $this->render('librarian/reservations.html.twig', [
            'reservations' => $resRepo->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    // ─── Authors ──────────────────────────────────────────────────────────────

    #[Route('/authors', name: 'app_librarian_authors')]
    public function authors(EntityManagerInterface $em): Response
    {
        return $this->render('librarian/authors/index.html.twig', [
            'authors' => $em->getRepository(Author::class)->findBy([], ['lastName' => 'ASC']),
        ]);
    }

    #[Route('/authors/new', name: 'app_librarian_author_new', methods: ['GET', 'POST'])]
    public function authorNew(Request $request, EntityManagerInterface $em): Response
    {
        $author = new Author();
        $form = $this->createForm(AuthorFormType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($author);
            $em->flush();
            $this->addFlash('success', 'Auteur ajouté.');
            return $this->redirectToRoute('app_librarian_authors');
        }

        return $this->render('librarian/authors/form.html.twig', ['form' => $form, 'title' => 'Ajouter un auteur']);
    }

    #[Route('/authors/{id}/edit', name: 'app_librarian_author_edit', methods: ['GET', 'POST'])]
    public function authorEdit(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $author = $em->getRepository(Author::class)->find($id);
        if (!$author) throw $this->createNotFoundException();

        $form = $this->createForm(AuthorFormType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Auteur modifié.');
            return $this->redirectToRoute('app_librarian_authors');
        }

        return $this->render('librarian/authors/form.html.twig', ['form' => $form, 'title' => 'Modifier l\'auteur']);
    }

    // ─── Categories ───────────────────────────────────────────────────────────

    #[Route('/categories', name: 'app_librarian_categories')]
    public function categories(EntityManagerInterface $em): Response
    {
        return $this->render('librarian/categories/index.html.twig', [
            'categories' => $em->getRepository(Category::class)->findBy([], ['name' => 'ASC']),
        ]);
    }

    #[Route('/categories/new', name: 'app_librarian_category_new', methods: ['GET', 'POST'])]
    public function categoryNew(Request $request, EntityManagerInterface $em): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();
            $this->addFlash('success', 'Catégorie ajoutée.');
            return $this->redirectToRoute('app_librarian_categories');
        }

        return $this->render('librarian/categories/form.html.twig', ['form' => $form, 'title' => 'Ajouter une catégorie']);
    }

    #[Route('/categories/{id}/edit', name: 'app_librarian_category_edit', methods: ['GET', 'POST'])]
    public function categoryEdit(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $category = $em->getRepository(Category::class)->find($id);
        if (!$category) throw $this->createNotFoundException();

        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Catégorie modifiée.');
            return $this->redirectToRoute('app_librarian_categories');
        }

        return $this->render('librarian/categories/form.html.twig', ['form' => $form, 'title' => 'Modifier la catégorie']);
    }

    // ─── Languages ────────────────────────────────────────────────────────────

    #[Route('/languages', name: 'app_librarian_languages')]
    public function languages(EntityManagerInterface $em): Response
    {
        return $this->render('librarian/languages/index.html.twig', [
            'languages' => $em->getRepository(Language::class)->findBy([], ['name' => 'ASC']),
        ]);
    }

    #[Route('/authors/quick', name: 'app_librarian_author_quick', methods: ['POST'])]
    public function authorQuick(Request $request, EntityManagerInterface $em): JsonResponse
    {
        if (!$this->isCsrfTokenValid('quick_author', $request->request->get('_token'))) {
            return $this->json(['error' => 'Token invalide'], 400);
        }
        $firstName = trim($request->request->get('firstName', ''));
        $lastName  = trim($request->request->get('lastName', ''));
        if (!$firstName && !$lastName) {
            return $this->json(['error' => 'Le prénom ou le nom est requis.'], 400);
        }
        // Détection de doublon (même prénom + nom, insensible à la casse)
        $existing = $em->getRepository(Author::class)->createQueryBuilder('a')
            ->where('LOWER(a.firstName) = LOWER(:fn) AND LOWER(a.lastName) = LOWER(:ln)')
            ->setParameters(['fn' => $firstName, 'ln' => $lastName])
            ->setMaxResults(1)->getQuery()->getOneOrNullResult();
        if ($existing) {
            return $this->json(['exists' => true, 'id' => $existing->getId(), 'name' => $existing->getFullName()]);
        }
        $author = new Author();
        $author->setFirstName($firstName)->setLastName($lastName);
        $em->persist($author);
        $em->flush();
        return $this->json(['id' => $author->getId(), 'name' => $author->getFullName()]);
    }

    #[Route('/categories/quick', name: 'app_librarian_category_quick', methods: ['POST'])]
    public function categoryQuick(Request $request, EntityManagerInterface $em): JsonResponse
    {
        if (!$this->isCsrfTokenValid('quick_category', $request->request->get('_token'))) {
            return $this->json(['error' => 'Token invalide'], 400);
        }
        $name = trim($request->request->get('name', ''));
        if (!$name) {
            return $this->json(['error' => 'Le nom est requis.'], 400);
        }
        $existing = $em->getRepository(Category::class)->createQueryBuilder('c')
            ->where('LOWER(c.name) = LOWER(:name)')->setParameter('name', $name)
            ->setMaxResults(1)->getQuery()->getOneOrNullResult();
        if ($existing) {
            return $this->json(['exists' => true, 'id' => $existing->getId(), 'name' => $existing->getName()]);
        }
        $category = new Category();
        $category->setName($name);
        $em->persist($category);
        $em->flush();
        return $this->json(['id' => $category->getId(), 'name' => $category->getName()]);
    }

    #[Route('/languages/quick', name: 'app_librarian_language_quick', methods: ['POST'])]
    public function languageQuick(Request $request, EntityManagerInterface $em): JsonResponse
    {
        if (!$this->isCsrfTokenValid('quick_language', $request->request->get('_token'))) {
            return $this->json(['error' => 'Token invalide'], 400);
        }
        $name = trim($request->request->get('name', ''));
        $code = strtolower(trim($request->request->get('code', '')));
        if (!$name) {
            return $this->json(['error' => 'Le nom est requis.'], 400);
        }
        $existing = $em->getRepository(Language::class)->createQueryBuilder('l')
            ->where('LOWER(l.name) = LOWER(:name)')->setParameter('name', $name)
            ->setMaxResults(1)->getQuery()->getOneOrNullResult();
        if ($existing) {
            return $this->json(['exists' => true, 'id' => $existing->getId(), 'name' => $existing->getName()]);
        }
        $language = new Language();
        $language->setName($name)->setCode($code ?: null);
        $em->persist($language);
        $em->flush();
        return $this->json(['id' => $language->getId(), 'name' => $language->getName()]);
    }

    #[Route('/languages/new', name: 'app_librarian_language_new', methods: ['GET', 'POST'])]
    public function languageNew(Request $request, EntityManagerInterface $em): Response
    {
        $language = new Language();
        $form = $this->createForm(LanguageFormType::class, $language);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($language);
            $em->flush();
            $this->addFlash('success', 'Langue ajoutée.');
            return $this->redirectToRoute('app_librarian_languages');
        }

        return $this->render('librarian/languages/form.html.twig', ['form' => $form, 'title' => 'Ajouter une langue']);
    }
}
