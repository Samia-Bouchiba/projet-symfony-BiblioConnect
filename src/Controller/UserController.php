<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Reservation;
use App\Form\CommentFormType;
use App\Form\ReservationFormType;
use App\Repository\BookRepository;
use App\Repository\CommentRepository;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/profile')]
class UserController extends AbstractController
{
    #[Route('', name: 'app_user_profile')]
    public function profile(
        ReservationRepository $reservationRepo,
        CommentRepository $commentRepo,
        BookRepository $bookRepo,
        UserRepository $userRepo
    ): Response {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $reservations = $reservationRepo->findByUserOrderedByDate($user);

        $extraStats = [];

        if ($this->isGranted('ROLE_ADMIN')) {
            $extraStats = [
                'total_users'        => count($userRepo->findAll()),
                'total_books'        => count($bookRepo->findAll()),
                'pending_reservations' => count($reservationRepo->findPending()),
                'pending_comments'   => count($commentRepo->findPendingModeration()),
            ];
        } elseif ($this->isGranted('ROLE_LIBRARIAN')) {
            $extraStats = [
                'total_books'          => count($bookRepo->findAll()),
                'pending_reservations' => count($reservationRepo->findPending()),
                'all_reservations'     => count($reservationRepo->findAll()),
            ];
        }

        return $this->render('user/profile.html.twig', [
            'user'         => $user,
            'reservations' => $reservations,
            'favorites'    => $user->getFavorites(),
            'my_comments'  => $commentRepo->findBy(['user' => $user], ['createdAt' => 'DESC']),
            'extra_stats'  => $extraStats,
        ]);
    }

    #[Route('/reserve/{id}', name: 'app_book_reserve', methods: ['GET', 'POST'])]
    public function reserve(int $id, Request $request, BookRepository $bookRepo, EntityManagerInterface $em): Response
    {
        $book = $bookRepo->find($id);
        if (!$book) {
            throw $this->createNotFoundException('Livre non trouvé.');
        }

        if (!$book->isAvailable()) {
            $this->addFlash('warning', 'Ce livre n\'est pas disponible actuellement.');
            return $this->redirectToRoute('app_book_show', ['id' => $id]);
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $reservation = new Reservation();
        $reservation->setBook($book);
        $reservation->setUser($user);

        $form = $this->createForm(ReservationFormType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($reservation);
            $em->flush();

            $this->addFlash('success', 'Réservation effectuée avec succès ! En attente de confirmation.');
            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('user/reserve.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/favorite/{id}', name: 'app_book_favorite', methods: ['POST'])]
    public function toggleFavorite(int $id, BookRepository $bookRepo, EntityManagerInterface $em): Response
    {
        $book = $bookRepo->find($id);
        if (!$book) {
            throw $this->createNotFoundException('Livre non trouvé.');
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if ($user->getFavorites()->contains($book)) {
            $user->removeFavorite($book);
            $this->addFlash('info', 'Retiré des favoris.');
        } else {
            $user->addFavorite($book);
            $this->addFlash('success', 'Ajouté aux favoris !');
        }

        $em->flush();
        return $this->redirectToRoute('app_book_show', ['id' => $id]);
    }

    #[Route('/comment/{id}', name: 'app_book_comment', methods: ['GET', 'POST'])]
    public function addComment(int $id, Request $request, BookRepository $bookRepo, EntityManagerInterface $em): Response
    {
        $book = $bookRepo->find($id);
        if (!$book) {
            throw $this->createNotFoundException('Livre non trouvé.');
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($user);
            $comment->setBook($book);
            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Commentaire soumis, en attente de modération.');
            return $this->redirectToRoute('app_book_show', ['id' => $id]);
        }

        return $this->render('user/comment.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }
}
