<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Form\ArticleType;



class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="app_blog")
     */
    public function index(ArticleRepository $repo)
    {
        $articles = $repo->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles
        ]);
    }


    /**
     * @Route("/", name="home")
     */
    //fonction qui renvoit a la page d'accueil du site
    public function home()
    {

        //mettre les variables qui fera le lien avec twig, on doit le mettre sous forme de tab
        return $this->render('blog/home.html.twig', [
            'title' => "bienvenu ici les amis", 'age' => 31
        ]);
    }


    /**
     * @Route("/blog/new" , name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */
    public function form(Article $article = null, Request $request, EntityManagerInterface $manager)
    {
        if (!$article) {

            $article = new Article();
        }

        $form = $this->createForm(ArticleType::class,$article);
       

        

        //la synthaxe pour créer un formulaire est createformbuilder
        // $form = $this->createFormBuilder($article)

        //     //configuation de mon formulaire, on rajoute des champs
        //     ->add('title')
        //     ->add('content')
        //     ->add('image')
        //     //résultat final
        //     ->getForm();
            

        //il va passer la requete http que j'ai mis en param
        $form->handleRequest($request);

        //mettre une condition if si le formulaire a été soumis
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$article->getId()) {
                $article->setCreateAt(new \DateTime());
            }
            //on donne le new date time (date de création)
           

            //demander au manager de préparer à faire persister l'article
            $manager->persist($article);
            //et quand tout est ok on peut demander au manager de balancer la requete
            $manager->flush();

            //redirection une fois que le form est submitted
            return $this->redirectToRoute('blog_show',['id' => $article->getId()]);


        }

        //fonction qui consiste a afficher le formulaire dans le twig
        return $this->render('blog/create.html.twig', ['formArticle' => $form->createView(), 'editMode'=>$article->getId()!== null]);
    }



    /**
     *@Route("/blog/{id}", name="blog_show")
     */
    public function show(Article $article)
    {



        return $this->render('blog/show.html.twig', ['article' => $article]);
    }
}
