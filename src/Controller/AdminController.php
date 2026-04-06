<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\User;
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

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('', name: 'app_admin_dashboard')]
    public function dashboard(
        UserRepository $userRepo,
        BookRepository $bookRepo,
        ReservationRepository $resRepo,
        CommentRepository $commentRepo
    ): Response {
        return $this->render('admin/dashboard.html.twig', [
            'total_users' => count($userRepo->findAll()),
            'total_books' => count($bookRepo->findAll()),
            'pending_reservations' => count($resRepo->findPending()),
            'pending_comments' => count($commentRepo->findPendingModeration()),
        ]);
    }

    // ─── Users Management ─────────────────────────────────────────────────────

    #[Route('/users', name: 'app_admin_users')]
    public function users(UserRepository $userRepo): Response
    {
        return $this->render('admin/users/index.html.twig', [
            'users' => $userRepo->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/users/{id}/role', name: 'app_admin_user_role', methods: ['POST'])]
    public function updateRole(int $id, Request $request, UserRepository $userRepo, EntityManagerInterface $em): Response
    {
        $user = $userRepo->find($id);
        if (!$user) throw $this->createNotFoundException();

        $role = $request->request->get('role');
        $allowedRoles = ['ROLE_USER', 'ROLE_LIBRARIAN', 'ROLE_ADMIN'];

        if ($user === $this->getUser() && $role !== 'ROLE_ADMIN') {
            $this->addFlash('danger', 'Vous ne pouvez pas modifier votre propre rôle administrateur.');
            return $this->redirectToRoute('app_admin_users');
        }

        if (in_array($role, $allowedRoles)) {
            $user->setRoles([$role]);
            $em->flush();
            $this->addFlash('success', 'Rôle mis à jour.');
        }

        return $this->redirectToRoute('app_admin_users');
    }

    #[Route('/users/{id}/toggle', name: 'app_admin_user_toggle', methods: ['POST'])]
    public function toggleUser(int $id, UserRepository $userRepo, EntityManagerInterface $em): Response
    {
        $user = $userRepo->find($id);
        if (!$user) throw $this->createNotFoundException();

        if ($user === $this->getUser()) {
            $this->addFlash('danger', 'Vous ne pouvez pas désactiver votre propre compte.');
            return $this->redirectToRoute('app_admin_users');
        }

        $user->setIsActive(!$user->isActive());
        $em->flush();
        $this->addFlash('success', 'Statut de l\'utilisateur mis à jour.');

        return $this->redirectToRoute('app_admin_users');
    }

    // ─── Reservations Management ──────────────────────────────────────────────

    #[Route('/reservations', name: 'app_admin_reservations')]
    public function reservations(ReservationRepository $resRepo): Response
    {
        return $this->render('admin/reservations/index.html.twig', [
            'reservations' => $resRepo->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/reservations/{id}/status', name: 'app_admin_reservation_status', methods: ['POST'])]
    public function updateReservationStatus(
        int $id,
        Request $request,
        ReservationRepository $resRepo,
        EntityManagerInterface $em
    ): Response {
        $reservation = $resRepo->find($id);
        if (!$reservation) throw $this->createNotFoundException();

        $status = $request->request->get('status');
        $allowed = [Reservation::STATUS_APPROVED, Reservation::STATUS_RETURNED, Reservation::STATUS_CANCELLED];

        if (in_array($status, $allowed)) {
            $reservation->setStatus($status);
            $em->flush();
            $this->addFlash('success', 'Statut de la réservation mis à jour.');
        }

        return $this->redirectToRoute('app_admin_reservations');
    }

    // ─── Comments Moderation ──────────────────────────────────────────────────

    #[Route('/comments', name: 'app_admin_comments')]
    public function comments(CommentRepository $commentRepo): Response
    {
        return $this->render('admin/comments/index.html.twig', [
            'pending' => $commentRepo->findPendingModeration(),
            'all' => $commentRepo->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/comments/{id}/approve', name: 'app_admin_comment_approve', methods: ['POST'])]
    public function approveComment(int $id, CommentRepository $commentRepo, EntityManagerInterface $em): Response
    {
        $comment = $commentRepo->find($id);
        if ($comment) {
            $comment->setIsApproved(true);
            $em->flush();
            $this->addFlash('success', 'Commentaire approuvé.');
        }
        return $this->redirectToRoute('app_admin_comments');
    }

    #[Route('/comments/{id}/delete', name: 'app_admin_comment_delete', methods: ['POST'])]
    public function deleteComment(int $id, CommentRepository $commentRepo, EntityManagerInterface $em): Response
    {
        $comment = $commentRepo->find($id);
        if ($comment) {
            $em->remove($comment);
            $em->flush();
            $this->addFlash('success', 'Commentaire supprimé.');
        }
        return $this->redirectToRoute('app_admin_comments');
    }
}
