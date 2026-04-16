<?php

namespace App\Controller;

use App\Entity\Matiere;
use App\Entity\Inscription;
use App\Entity\Question;
use App\Entity\QuizResult;
use App\Form\MatiereType;
use App\Form\QuestionType;
use App\Form\QuizType;
use App\Repository\MatiereRepository;
use App\Repository\InscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_accueil')]
    public function accueil(MatiereRepository $matiereRepository, InscriptionRepository $inscriptionRepository): Response
    {
        $matieres = $matiereRepository->findAll();
        $inscription = $inscriptionRepository->findAll();

        usort($matieres, function ($a, $b) {
            return strcasecmp($a->getNameles(), $b->getNameles());
        });

        usort($inscription, function ($a, $b) {
            return strcasecmp($a->getEmail(), $b->getEmail());
        });

        return $this->render('accueil/accueil.html.twig', [
            'matieres' => $matieres,
            'inscription' => $inscription,


        ]);
    }

    #[Route('/matiere/ajouter', name: 'app_matiere_ajouter')]
    public function ajouter(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $matiere = new Matiere();

        for ($i = 0; $i < 5; $i++) {
            $matiere->addQuestion(new Question());
        }

        // Ajouter une question par défaut pour tester l'affichage

        $form = $this->createForm(MatiereType::class, $matiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getUser();
            $matiere->setCreatedBy($user);



            $fichier = $form->get('fichier')->getData();
            if ($fichier) {
                $originalFilename = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $fichier->guessExtension();

                try {
                    $fichier->move(
                        $this->getParameter('fichiers_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }

                $matiere->setFichier($newFilename);
            }

            //video
            $video = $form->get('video')->getData();
            if ($video) {
                $originalVideoFilename = pathinfo($video->getClientOriginalName(), PATHINFO_FILENAME);
                $safeVideoFilename = $slugger->slug($originalVideoFilename);
                $newVideoFilename = $safeVideoFilename . '-' . uniqid() . '.' . $video->guessExtension();

                try {
                    $video->move(
                        $this->getParameter('videos_directory'),
                        $newVideoFilename
                    );
                } catch (FileException $e) {
                }
                $matiere->setVideo($newVideoFilename);
            }
            // Persist the data
            foreach ($matiere->getQuestions() as $question) {
                $question->setMatiere($matiere); // Assurez-vous de lier chaque question à la matière
                $entityManager->persist($question);
            }

            $entityManager->persist($matiere);
            $entityManager->flush();

            $this->addFlash('success', 'Matière ajoutée avec succès.');

            return $this->redirectToRoute('app_cours');
        }

        return $this->render('matiere/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/cours', name: 'app_cours')]
    public function cours(MatiereRepository $matiereRepository): Response
    {
        $matieres = $matiereRepository->findAll();

        // Tri des matières par nameles
        usort($matieres, function ($a, $b) {
            return strcasecmp($a->getNameles(), $b->getNameles());
        });


        return $this->render('matiere/cours.html.twig', [
            'matieres' => $matieres,
        ]);
    }

    #[Route('/afficher-pdf/{id}', name: 'afficher_pdf')]
    public function afficher(MatiereRepository $matiereRepository, EntityManagerInterface $entityManager, $id): Response
    {
        $matiere = $matiereRepository->find($id);
        $user = $this->getUser();

        if (!$matiere) {
            throw $this->createNotFoundException('La matière n\'existe pas.');
        }

        $quizCompleted = false;
        if ($user) {
            $quizResult = $entityManager->getRepository(QuizResult::class)->findOneBy(['user' => $user, 'matiere' => $matiere]);
            $quizCompleted = $quizResult !== null;
        }


        $pdfurl = 'uploads/fichiers/' . $matiere->getFichier();
        $videourl = 'uploads/videos/' . $matiere->getVideo();

        $questionsForJS = [];
        foreach ($matiere->getQuestions() as $question) {
            $questionsForJS[] = [
                'question' => $question->getQuestion(),
                'optionA' => $question->getOptionA(),
                'optionB' => $question->getOptionB(),
                'optionC' => $question->getOptionC(),
                'optionD' => $question->getOptionD(),
                'correctOption' => $question->getCorrectOption()
            ];
        }



        return $this->render('matiere/affichage.html.twig', [
            'pdfurl' => $pdfurl,
            'videourl' => $videourl,
            'matiere' => $matiere,
            'questionsJson' => json_encode($questionsForJS),
            'quizCompleted' => $quizCompleted
        ]);
    }

    #[Route('/matiere/modifier/{id}', name: 'app_matiere_modifier')]
    public function modifier(Request $request, MatiereRepository $matiereRepository, EntityManagerInterface $entityManager, SluggerInterface $slugger, $id): Response
    {
        $matiere = $matiereRepository->find($id);

        if (!$matiere) {
            throw $this->createNotFoundException('La matière n\'existe pas.');
        }

        $user = $this->getUser();
        if ($matiere->getCreatedBy() !== $user) {
            throw new AccessDeniedException('Vous n\'avez pas les droits pour modifier cette matière.');
        }



        //recuperation de ficher et de video 
        $prevfichier = $matiere->getFichier();
        $prevvideo = $matiere->getVideo();

        $form = $this->createForm(MatiereType::class, $matiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            dump($prevfichier, $prevvideo);
            // Gestion du fichier PDF
            $fichier = $form->get('fichier')->getData();

            if ($fichier) {
                $originalFilename = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $fichier->guessExtension();

                try {
                    $fichier->move(
                        $this->getParameter('fichiers_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gestion des erreurs
                }

                $matiere->setFichier($newFilename);
            } else {
                dump('No new file uploaded, using previous:', $prevfichier);
                $matiere->setFichier($prevfichier);
            }

            // Gestion du fichier vidéo
            $video = $form->get('video')->getData();

            //soumission de la video si l'util insère une autre video
            if ($video) {
                $originalVideoFilename = pathinfo($video->getClientOriginalName(), PATHINFO_FILENAME);
                $safeVideoFilename = $slugger->slug($originalVideoFilename);
                $newVideoFilename = $safeVideoFilename . '-' . uniqid() . '.' . $video->guessExtension();

                try {
                    $video->move(
                        $this->getParameter('videos_directory'),
                        $newVideoFilename
                    );
                } catch (FileException $e) {
                    // Gestion des erreurs
                }

                $matiere->setVideo($newVideoFilename);
            } else {
                dump('No new video uploaded, using previous:', $prevvideo);
                //sinon la video uploadé reste la même
                $matiere->setVideo($prevvideo);
            }

            // Persist the data
            foreach ($matiere->getQuestions() as $question) {
                $question->setMatiere($matiere);
                $entityManager->persist($question);
            }


            $entityManager->persist($matiere);
            //execution et maj de la BD
            $entityManager->flush();

            $this->addFlash('success', 'Modification réussi.');

            return $this->redirectToRoute('app_cours');
        }

        return $this->render('matiere/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }

   #[Route('/matiere/delete/{id}', name: 'app_delete', methods: ['POST'])]
public function delete(Request $request, Matiere $matiere, EntityManagerInterface $entityManager): Response
{
    $user = $this->getUser();
    if ($matiere->getCreatedBy() !== $user) {
        throw new AccessDeniedException('Vous n\'avez pas les droits pour supprimer cette matière.');
    }
    
    if ($this->isCsrfTokenValid('delete' . $matiere->getId(), $request->request->get('_token'))) {
        // Supprimer les fichiers physiques
        $fichier = $matiere->getFichier();
        if ($fichier) {
            $filepath = $this->getParameter('fichiers_directory') . '/' . $fichier;
            if (file_exists($filepath)) {
                unlink($filepath);
            }
        }

        $video = $matiere->getVideo();
        if ($video) {
            $videopath = $this->getParameter('videos_directory') . '/' . $video;
            if (file_exists($videopath)) {
                unlink($videopath);
            }
        }

        // Doctrine s'occupe de supprimer les relations grâce au cascade
        $entityManager->remove($matiere);
        $entityManager->flush();

        $this->addFlash('success', 'La matière a été supprimée avec succès.');
    }

    return $this->redirectToRoute('app_cours');
}


    #[Route('/eleve', name: 'app_eleve')]
    public function eleve(InscriptionRepository $inscriptionRepository): Response
    {
        $inscription = $inscriptionRepository->findAll();

        // Tri des matières par nameles
        usort($inscription, function ($a, $b) {
            return strcasecmp($a->getEmail(), $b->getEmail());
        });


        return $this->render('accueil/eleve.html.twig', [
            'inscription' => $inscription,
        ]);
    }
    #[Route('/matiere/submit/{matiereId}', name: 'app_matiere_submit', methods: ['POST'])]
public function submitQuiz(Request $request, EntityManagerInterface $entityManager, int $matiereId): JsonResponse
{
    $matiere = $entityManager->getRepository(Matiere::class)->find($matiereId);
    $data = json_decode($request->getContent(), true);
    $score = $data['score'] ?? null;
    
    if ($score === null) {
        return new JsonResponse([
            'success' => false, 
            'error' => 'Score manquant'
        ], 400);
    }

    $quizResult = new QuizResult();
    $quizResult->setUser($this->getUser())
               ->setMatiere($matiere)
               ->setScore((int)$score);

    $entityManager->persist($quizResult);
    $entityManager->flush();

    return new JsonResponse(['success' => true]);
}
}
