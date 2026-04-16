<?php

namespace App\Entity;

use App\Repository\QuizResultRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Inscription;
use App\Entity\Matiere;

#[ORM\Entity(repositoryClass: QuizResultRepository::class)]
class QuizResult
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Inscription::class)]
    private ?Inscription $user = null;

    #[ORM\ManyToOne(targetEntity: Matiere::class)]
    private ?Matiere $matiere = null;

    #[ORM\Column(nullable: true)]
    private ?int $score = null;


    #[ORM\Column]
    private ?\DateTimeImmutable $completedAt = null;

    public function __construct(){
        $this->completedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getUser(): ?Inscription
    {
        return $this->user;
    }
    public function setUser(?Inscription $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getMatiere(): ?Matiere
    {
        return $this->matiere;
    }
    public function setMatiere(?Matiere $matiere): self
    {
        $this->matiere = $matiere;
        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }
    public function setScore(?int $score): self
    {
        $this->score = $score;
        return $this;
    }

    public function getCompletedAt(): ?DateTimeImmutable
    {
        return $this->completedAt;
    }
   
}
