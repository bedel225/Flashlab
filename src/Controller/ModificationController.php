<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Form\ModificationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ModificationController extends AbstractController
{
    

    
    #[Route('/post/modif/{id}', name: 'app_modification')]
    public function index($id): Response
    {       
        $post = new Posts();
        $posts=$this->getPosts($post[id]);
        $form = $this->createForm(ModificationType::class, $posts);
        return $this->render('modification/modification.html.twig', [
            'form'=>$form->createView()
        ]);
    }
}

