<?php

namespace App\Command;

use App\Entity\Inscription;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Crée un nouvel administrateur'
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('Cette commande vous permet de créer un administrateur')
            ->addArgument('email', InputArgument::REQUIRED, 'Email de l\'administrateur')
            ->addArgument('password', InputArgument::REQUIRED, 'Mot de passe de l\'administrateur')
            ->addArgument('name', InputArgument::REQUIRED, 'Prénom de l\'administrateur')
            ->addArgument('surname', InputArgument::REQUIRED, 'Nom de l\'administrateur');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $name = $input->getArgument('name');
        $surname = $input->getArgument('surname');

        try {
            // Créer un nouvel utilisateur
            $admin = new Inscription();
            $admin->setEmail($email);
            $admin->setName($name);
            $admin->setSurname($surname);
            $admin->setUsername($name);
            $admin->setUserType('enseignant');

            // Hasher le mot de passe
            $hashedPassword = $this->passwordHasher->hashPassword($admin, $password);
            $admin->setPassword($hashedPassword);

            // Définir les rôles admin
            $admin->setRoles(['ROLE_ADMIN', 'ROLE_USER']);

            // Persister l'utilisateur
            $this->entityManager->persist($admin);
            $this->entityManager->flush();

            $io->success('Administrateur créé avec succès !');
            $io->table(
                ['Email', 'Nom', 'Prénom', 'Rôles'],
                [[$email, $surname, $name, implode(', ', $admin->getRoles())]]
            );

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Erreur lors de la création de l\'administrateur: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}