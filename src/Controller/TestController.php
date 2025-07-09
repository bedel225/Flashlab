<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/create-user', name: 'create_user')]
    public function createUser(EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        $repo = $em->getRepository(User::class);
        $existingUser = $repo->findOneBy(['email' => 'test@example.com']);

        if ($existingUser) {
            return new Response('⚠️ L\'utilisateur "test@example.com" existe déjà !');
        }

        $user = new User();
        $user->setEmail('test@example.com');
        $user->setRoles(['ROLE_USER']);
        $hashedPassword = $hasher->hashPassword($user, 'password123');
        $user->setPassword($hashedPassword);

        $em->persist($user);
        $em->flush();

        return new Response('✅ Utilisateur "test@example.com" créé avec succès !');
    }
}
