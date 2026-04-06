<?php

namespace App\Tests\Unit;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Language;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class BookTest extends TestCase
{
    public function testBookCreation(): void
    {
        $book = new Book();
        $book->setTitle('Test Livre');
        $book->setDescription('Une description de test.');
        $book->setIsbn('978-3-16-148410-0');
        $book->setStock(5);

        $this->assertSame('Test Livre', $book->getTitle());
        $this->assertSame('Une description de test.', $book->getDescription());
        $this->assertSame('978-3-16-148410-0', $book->getIsbn());
        $this->assertSame(5, $book->getStock());
        $this->assertTrue($book->isAvailable());
    }

    public function testBookNotAvailableWhenStockZero(): void
    {
        $book = new Book();
        $book->setStock(0);
        $this->assertFalse($book->isAvailable());
    }

    public function testAddAuthor(): void
    {
        $book = new Book();
        $author = new Author();
        $author->setFirstName('Victor')->setLastName('Hugo');

        $book->addAuthor($author);
        $this->assertTrue($book->getAuthors()->contains($author));
        $this->assertCount(1, $book->getAuthors());
    }

    public function testAddCategory(): void
    {
        $book = new Book();
        $category = new Category();
        $category->setName('Roman');

        $book->addCategory($category);
        $this->assertTrue($book->getCategories()->contains($category));
    }

    public function testLanguage(): void
    {
        $book = new Book();
        $lang = new Language();
        $lang->setName('Français')->setCode('fr');

        $book->setLanguage($lang);
        $this->assertSame('Français', $book->getLanguage()->getName());
    }

    public function testAverageRatingWithNoComments(): void
    {
        $book = new Book();
        $this->assertNull($book->getAverageRating());
    }

    public function testAverageRatingWithApprovedComments(): void
    {
        $book = new Book();
        $user = new User();
        $user->setFirstName('Test')->setLastName('User')->setEmail('test@test.fr');

        $comment1 = new Comment();
        $comment1->setUser($user)->setContent('Très bon livre !')->setRating(5)->setIsApproved(true);
        $book->addComment($comment1);

        $comment2 = new Comment();
        $comment2->setUser($user)->setContent('Bien mais un peu long.')->setRating(3)->setIsApproved(true);
        $book->addComment($comment2);

        $this->assertSame(4.0, $book->getAverageRating());
    }

    public function testAverageRatingIgnoresUnapprovedComments(): void
    {
        $book = new Book();
        $user = new User();
        $user->setFirstName('Test')->setLastName('User')->setEmail('test@test.fr');

        $comment = new Comment();
        $comment->setUser($user)->setContent('Commentaire en attente.')->setRating(1)->setIsApproved(false);
        $book->addComment($comment);

        $this->assertNull($book->getAverageRating());
    }

    public function testToString(): void
    {
        $book = new Book();
        $book->setTitle('Mon Livre');
        $this->assertSame('Mon Livre', (string) $book);
    }
}
