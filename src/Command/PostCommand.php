<?php

namespace App\Command;
use App\Entity\Posts;
use App\Entity\Comments;
use App\Entity\Users;
use App\Entity\Adress;
use App\Entity\Company;
use App\Entity\Geo;
use App\Service\PostService;
use App\Service\CommentService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'Post',
    description: 'la commande pour recuperer les comments, les posts et les users',
)]
class PostCommand extends Command
{
    private $postService;
    private $commentService;
    private $userService;
    private $entityManager;

    public function __construct(PostService $postService,CommentService $commentService, UserService $userService,
    EntityManagerInterface $entityManager)
    {
        parent::__construct();
       $this->postService=$postService;
       $this->commentService=$commentService;
       $this->userService=$userService;
       $this->entityManager=$entityManager;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dataPost=$this->postService->fetchPostInformation();
        $dataComment=$this->commentService->fetchCommentInformation();
        $dataUser=$this->userService->fetchUserInformation();      

        foreach($dataUser as $data)
        { 
          //json vers entity geo
          $geo = new Geo();
          $geo->setLat($data["address"]["geo"]["lat"]);
          $geo->setLng($data["address"]["geo"]["lng"]);
          $this->entityManager->persist($geo);
          
          //json vers entity adress
          $adress = new Adress();
          $adress->setStreet($data["address"]["street"]);
          $adress->setSuite($data["address"]["suite"]);
          $adress->setCity($data["address"]["city"]);
          $adress->setZipcode($data["address"]["zipcode"]);
          $adress->setGeo($geo);
          $this->entityManager->persist($adress);

          //json vers entity company
          $company = new Company();
          $company->setName($data["company"]["name"]);
          $company->setCatchPhrase($data["company"]["catchPhrase"]);
          $company->setBs($data["company"]["bs"]);
          $this->entityManager->persist($company);
          
          //json vers entity users
          $user = new Users();
          $user->setName($data["name"]);
          $user->setUsername($data["username"]);
          $user->setEmail($data["email"]);
          $user->setPhone($data["phone"]);
          $user->setWebsite($data["website"]); 
          $user->setAdress($adress);
          $user->setCompany($company);
          $this->entityManager->persist($user);
          // actually executes the queries (i.e. the INSERT query)
          $this->entityManager->flush(); 
          
        }

        //json vers entity posts
        foreach($dataPost as $data)
        {
            $post = new Posts();
            $user = $this->entityManager->getRepository(Users::class)->find($data["userId"]);
            if (!$user) {
                $user = new Users();
            }
            $post->setUser($user);
            $post->setTitle($data["title"]);
            $post->setBody($data["body"]);
            $this->entityManager->persist($post);                    
            $this->entityManager->flush(); 
        }

        //json vers entity comments
        foreach($dataComment as $data)
        { 
            $coment = new Comments();
            $post= $this->entityManager->getRepository(Posts::class)->find($data["postId"]);
            if (!$post) {
                $post = new Posts();
            }
            $coment->setPost($post);
            $coment->setName($data["name"]);
            $coment->setEmail($data["email"]);
            $coment->setBody($data["body"]);
            $this->entityManager->persist($coment);
            $this->entityManager->flush(); 
        }
        return Command::SUCCESS;
    }
}