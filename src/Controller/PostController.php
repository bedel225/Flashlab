<?php

namespace App\Controller;

use App\Entity\Posts;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
{
    $this->entityManager = $entityManager;
}

    #[Route('/', name: 'app_post')]
    public function index(): Response
    {
       $posts= $this->entityManager->getRepository(Posts::class)->findAll();
        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/post/{id}', name: 'app_supprime')]
    public function delete($id): Response
    {
        $post = $this->entityManager->getRepository(Posts::class)->find($id);
        $this->entityManager->remove($post);
        $this->entityManager->flush();
        return $this->render('post/delete-post.html.twig', []);
    }
}
