<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $question = null;

    #[ORM\Column(length: 255)]
    private ?string $optionA = null;

    #[ORM\Column(length: 255)]
    private ?string $optionB = null;

    #[ORM\Column(length: 255)]
    private ?string $optionC = null;

    #[ORM\Column(length: 255)]
    private ?string $optionD = null;

    #[ORM\Column(length: 255)]
    private ?string $correctOption = null;

    #[ORM\ManyToOne(inversedBy: 'questions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Matiere $matiere = null;

    // Getters et setters...
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;
        return $this;
    }

    // Ajoutez les autres getters et setters...

    public function getMatiere(): ?Matiere
    {
        return $this->matiere;
    }

    public function setMatiere(?Matiere $matiere): self
    {
        $this->matiere = $matiere;
        return $this;
    }

    public function getOptionA(): ?string
    {
         return $this->optionA;
    }   
    public function setOptionA(string $optionA): self
    {
        $this->optionA = $optionA;
        return $this;
    }

    public function getOptionB(): ?string
    {
        return $this->optionB;
    }   

    public function setOptionB(string $optionB): self
    {   
         $this->optionB = $optionB;
        return $this;
    }

    public function getOptionC(): ?string
    {
        return $this->optionC;
    }  
     
    public function setOptionC(string $optionC): self
    {
        $this->optionC= $optionC;
        return $this;
    }

     public function getOptionD(): ?string
    {
        return $this->optionD;
    }   

        public function setOptionD(string $optionD): self
    {
        $this->optionD = $optionD;
        return $this;
    }

    public function getCorrectOption(): ?string
    {
        return $this->correctOption;
        }   
        public function setCorrectOption(string $correctOption): self
    {
        $this->correctOption = $correctOption;
        return $this;
    }
}