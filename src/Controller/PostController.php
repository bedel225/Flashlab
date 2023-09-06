<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Form\ModificationType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
       $posts= $this->entityManager->getRepository(Posts::class)->findAll();
       $pagination = $paginator->paginate(
        $posts,
        $request->query->getInt('page',1),
        4
       );
        return $this->render('post/index.html.twig', [
            'posts' => $pagination,
        ]);
    }

    #[Route('/post/{id}', name: 'app_supprime')]
    public function delete($id): Response
    {
        $post = $this->entityManager->getRepository(Posts::class)->find($id);
        $comments = $post->getComments();
        foreach ($comments as $comment) 
	{
            $comment->setPost(null);
            $this->entityManager->flush();
        }
        $this->entityManager->remove($post);
        $this->entityManager->flush();
        return $this->render('post/delete-post.html.twig', []);
    }
    
    #[Route('/post/modif/{id}', name: 'app_modification')]
    public function modif(Request $request, $id): Response
    {       
        $posts=$this->entityManager->getRepository(Posts::class)->find($id);
        $form = $this->createForm(ModificationType::class, $posts);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $this->entityManager->persist($post);
            $this->entityManager->flush();
        }
        return $this->render('modification/modification.html.twig', [
            'form'=>$form->createView()
        ]);
    }
}
