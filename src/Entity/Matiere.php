<?php

namespace App\Entity;


use App\Repository\MatiereRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Inscription;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: MatiereRepository::class)]
class Matiere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nameles = null;

    #[ORM\Column(length: 255)]
    private ?string $titleles = null;

    #[ORM\Column(length: 255)]
    private ?string $fichier = null;

    #[ORM\Column(length: 255, nullable:true)]
    private ?string $video = null;

    #[ORM\ManyToOne(targetEntity: Inscription::class)]
    private ?Inscription $createdBy = null;

    #[ORM\OneToMany(mappedBy: 'matiere', targetEntity: Question::class, cascade: ['remove'], orphanRemoval: true)]
    private $questions;


    public function __construct()
    {
        $this->questions = new ArrayCollection();
    }

    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->setMatiere($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->removeElement($question)) {
            // Set the owning side to null (unless already changed)
            if ($question->getMatiere() === $this) {
                $question->setMatiere(null);
            }
        }

        return $this;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameles(): ?string
    {
        return $this->nameles;
    }

    public function setNameles(string $nameles): static
    {
        $this->nameles = $nameles;

        return $this;
    }

    public function getTitleles(): ?string
    {
        return $this->titleles;
    }

    public function setTitleles(string $titleles): static
    {
        $this->titleles = $titleles;

        return $this;
    }

    public function getFichier(): ?string
    {
        return $this->fichier;
    }

    public function setFichier(string $fichier): static
    {
        $this->fichier = $fichier;

        return $this;
    }

    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setVideo(?string $video): self
    {
        $this->video = $video;

        return $this;
    }
    public function setCreatedBy(Inscription $user): self
    {
    $this->createdBy = $user;
    return $this;
    }

    public function getCreatedBy(): ?Inscription
    {
    return $this->createdBy;
    }
}
