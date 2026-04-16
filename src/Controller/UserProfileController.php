<?php

namespace App\Controller;

use App\Entity\Matiere;
use App\Entity\Inscription;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\profileType;
use App\Repository\InscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\SecurityBundle\Security;

class UserProfileController extends AbstractController
{
    
    #[Route('/user/profile', name: 'app_profile')]
    public function profile(UserPasswordHasherInterface $userPasswordHasher,Security $security, Request $request,InscriptionRepository $inscriptionRepository, EntityManagerInterface $entityManager, SluggerInterface $slugger,): Response
    {
        $user = $security->getUser();
        if (!$user instanceof Inscription) {
            throw new \LogicException('L\'utilisateur n\'est pas connecté ou l\'entité utilisateur n\'est pas correcte.');
        }
        $form = $this->createForm(profileType::class, $user);
        $form->handleRequest($request);

        $plainPassword = $form->get('plainPassword')->getData();

        try{
            if ($form->isSubmitted() && $form->isValid()) {
             
                if ($plainPassword) {
                    $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
                }

            $entityManager->persist($user);
            //execution et maj de la BD
            $entityManager->flush();
            
            $this->addFlash('success', 'Votre profil a été mis à jour avec succès !');
    
            return $this->redirectToRoute('app_profile');
        }
    }catch(Exception $e){
        $this->addFlash('error', 'Votre profil n\'a pas été mis à jour avec succès !');
    }
        

        return $this->render('user_profile/profile.html.twig', [
            'profileform' => $form->createView(),
            'user' => $user,
        ]);
    }

    
}
