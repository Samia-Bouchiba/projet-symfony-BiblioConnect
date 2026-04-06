<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[Vich\Uploadable]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le titre est obligatoire.')]
    #[Assert\Length(max: 255)]
    private ?string $title = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $isbn = null;

    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero(message: 'Le stock doit être positif ou nul.')]
    private ?int $stock = 1;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $publishedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $coverImage = null;

    #[Vich\UploadableField(mapping: 'book_covers', fileNameProperty: 'coverImage')]
    private ?File $coverImageFile = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: Language::class, inversedBy: 'books')]
    private ?Language $language = null;

    #[ORM\ManyToMany(targetEntity: Author::class, inversedBy: 'books')]
    private Collection $authors;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'books')]
    private Collection $categories;

    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'book', orphanRemoval: true)]
    private Collection $reservations;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'favorites')]
    private Collection $favoritedBy;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'book', orphanRemoval: true)]
    private Collection $comments;

    public function __construct()
    {
        $this->authors = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->favoritedBy = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $title): static { $this->title = $title; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }

    public function getIsbn(): ?string { return $this->isbn; }
    public function setIsbn(?string $isbn): static { $this->isbn = $isbn; return $this; }

    public function getStock(): ?int { return $this->stock; }
    public function setStock(?int $stock): static { $this->stock = $stock; return $this; }

    public function getPublishedAt(): ?\DateTimeInterface { return $this->publishedAt; }
    public function setPublishedAt(?\DateTimeInterface $publishedAt): static { $this->publishedAt = $publishedAt; return $this; }

    public function getCoverImage(): ?string { return $this->coverImage; }
    public function setCoverImage(?string $coverImage): static { $this->coverImage = $coverImage; return $this; }

    public function setCoverImageFile(?File $file = null): void
    {
        $this->coverImageFile = $file;
        if (null !== $file) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getCoverImageFile(): ?File { return $this->coverImageFile; }

    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static { $this->updatedAt = $updatedAt; return $this; }

    public function getLanguage(): ?Language { return $this->language; }
    public function setLanguage(?Language $language): static { $this->language = $language; return $this; }

    public function getAuthors(): Collection { return $this->authors; }

    public function addAuthor(Author $author): static
    {
        if (!$this->authors->contains($author)) {
            $this->authors->add($author);
        }
        return $this;
    }

    public function removeAuthor(Author $author): static
    {
        $this->authors->removeElement($author);
        return $this;
    }

    public function getCategories(): Collection { return $this->categories; }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }
        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->categories->removeElement($category);
        return $this;
    }

    public function getReservations(): Collection { return $this->reservations; }

    public function getFavoritedBy(): Collection { return $this->favoritedBy; }

    public function getComments(): Collection { return $this->comments; }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setBook($this);
        }
        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            if ($comment->getBook() === $this) {
                $comment->setBook(null);
            }
        }
        return $this;
    }

    public function getAverageRating(): ?float
    {
        $approved = $this->comments->filter(fn(Comment $c) => $c->isApproved());
        if ($approved->isEmpty()) {
            return null;
        }
        $sum = array_sum($approved->map(fn(Comment $c) => $c->getRating())->toArray());
        return round($sum / $approved->count(), 1);
    }

    public function isAvailable(): bool
    {
        return $this->stock > 0;
    }

    public function __toString(): string { return $this->title ?? ''; }
}
