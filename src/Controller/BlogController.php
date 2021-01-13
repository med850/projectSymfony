<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\Security\Core\Security;


class BlogController extends AbstractController
{


    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @Route("/", name="blog")
     */
    public function index(ArticleRepository $articleRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $articles = $paginator->paginate($articleRepository->findAll(), 
        $request->query->getInt('page', 1), 
         5
    );

        return $this->render('blog/index.html.twig', [
            'articles' => $articles
        ]);
    }

     /**
     * @Route("article/new", name="newArticle")
     */
    public function newArticle(Request $request, FlashyNotifier $flashyNotifier) {

        $article = new Article();
        $form = $this -> createForm(ArticleType :: class, $article);
        $form -> handleRequest($request);

        if($form -> isSubmitted() && $form -> isValid()){
            $article -> setCreatedDate(new DateTime());
            $article -> setImage("https://picsum.photos/id/237/200/300");
            $article->setUser($this->security->getUser());
            $entityMannager = $this -> getDoctrine() -> getManager();
            $entityMannager -> persist($article);
            $entityMannager -> flush();
            $flashyNotifier->info('Post Created Successfully!');


            return $this -> redirectToRoute("articleShow", ['id' => $article -> getId()]);
        }


        return $this->render('blog/new.html.twig', [
            'form' => $form ->createView()
        ]);
        

    }   



     /**
     * @Route("article/{id}/edit", name="editArticle")
     */
    public function editArticle(Request $request, Article $article, FlashyNotifier $flashyNotifier): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form -> handleRequest($request);

        if($form -> isSubmitted() && $form -> isValid()){
            $entityMannager = $this -> getDoctrine() -> getManager();
            $entityMannager -> persist($article);
            $entityMannager -> flush();
            $flashyNotifier->warning('Post Modified Successfully!');


            return $this -> redirectToRoute("articleShow", ['id' => $article -> getId()]);

        }
        return $this->render('blog/edit.html.twig', [
            'editForm' => $form ->createView()
        ]);
    }





 /**
     * @Route("article/{id}", name="articleShow", methods={"GET", "POST"})
     */
    public function showArticle(Article $article, Request $request, FlashyNotifier $flashyNotifier): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form -> handleRequest($request);
        if($form -> isSubmitted() && $form -> isValid()){
            $comment -> setCreatedDate(new DateTime());
            $comment -> setArticle($article);
            $entityMannager = $this->getDoctrine() -> getManager();
            $entityMannager -> persist($comment);
            $entityMannager -> flush();
            $flashyNotifier->success('Comment Added Successfully!');


            return $this->redirectToRoute("articleShow", ['id' => $article -> getId()]);


        }
        return $this->render('blog/show.html.twig', [
            'article' => $article,
            'commentForm' => $form -> createView()
        ]);
    }

   

 




}


