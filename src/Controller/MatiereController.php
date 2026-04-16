<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MatiereController extends AbstractController
{
    #[Route('/matiere', name: 'app_matiere')]
    public function matiere(MatiereRepository $matiereRepository): Response
    {
        $matieres = $matiereRepository->findAll();
        return $this->render('matiere/matiere.html.twig', [
            'controller_name' => 'MatiereController',
        ]);
    }
}
