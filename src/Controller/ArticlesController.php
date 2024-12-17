<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ArticlesRepository;
use App\Repository\CategorieRepository;
use App\Repository\CommentaireRepository;
use App\Entity\Articles;
use App\Entity\Categorie;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/articles')]
class ArticlesController extends AbstractController
{

    #[Route('/', name: 'articles_index', methods: ['GET','POST'])] // Cette annotation définit la route pour afficher la liste des utilisateurs. 'GET' signifie que cette route répondra aux requêtes HTTP GET (les requêtes pour obtenir des données)
    public function index(ArticlesRepository $articlesRepository,Request $request,CategorieRepository $categoriesRepository): Response // La méthode index() récupère tous les utilisateurs de la base de données via UserRepository et les affiche
    {   
        $categories = $categoriesRepository->findAll();
        if($request->isMethod('POST')){
            $articles = $articlesRepository->findBy(['id_categorie' => $request->request->get('filtre')]);//findBy permet de retourner un tableau d'objet d'articles, en fonction des critaire ['id_categorie' définie l'attribut a récupérer => ou c'est egale a la requete qui retourne le filtre donc soit 1 (donc jeux video) ou 2 (donc anime)]
            dd($articles);
        }else{
            $articles = $articlesRepository->findAll(); // Appelle la méthode findAll() du UserRepository pour récupérer tous les utilisateurs dans la base de données
        }
       
        return $this->render('articles/index.html.twig', ['articles' => $articles , 'categories' => $categories]); // Rendu de la vue 'user/index.html.twig', avec la liste des utilisateurs passée à la vue
    }

    #[Route('/new', name: 'articles_new', methods: ['GET', 'POST'])] // La route '/new' pour afficher le formulaire de création et traiter l'envoi du formulaire

    public function new(Request $request, EntityManagerInterface $em , CategorieRepository $categoriesRepository): Response // La méthode new() gère l'affichage et la création de nouveaux articles/ on met categorie en tant qu'attribut car on veut recupérer toute les catégories existante
    // ca revient au meme que faire un categories => categorieRepositorie -> findAll() (en ayant au prealable import categoriesRepositorie)
    {

        $categories = $categoriesRepository->findAll();

        if ($request->isMethod('POST')) { // Si la méthode de la requête est POST (c'est-à-dire que le formulaire a été soumis)
            $article = new Articles(); // On crée une nouvelle instance de l'entité User

            // On récupère les données soumises dans le formulaire et on les attribue à l'entité $user
            $article->setTitre($request->request->get('titre')); // Attribue le titre depuis la requête
            $article->setContenu($request->request->get('contenu')); // Attribue le contunu depuis la requête

            // Récupérer l'ID de la catégorie depuis le formulaire
            $categorieId = $request->request->get('id_categorie');

            // Rechercher l'entité Categorie correspondante
            $categorie = $categoriesRepository->find($categorieId);
           
            // Attribue l'entité categorie a la variable article
            $article->setIdCategorie($categorie); 
            
            $em->persist($article); // Prépare l'entité $article à être sauvegardée dans la base de données
            $em->flush(); // Sauvegarde réellement les données dans la base de données

            return $this->redirectToRoute('articles_index'); // Redirige l'administrateur vers la page de la liste des articles après l'ajout
        }

        return $this->render('articles/new.html.twig',['categories' => $categories]); // Si la méthode est GET (formulaire de création), on affiche le formulaire
    }
    
    #[Route('/edit/{id}', name: 'articles_edit', methods: ['GET', 'POST'])] // La route '/{id}/edit' permet de modifier un utilisateur existant
    public function edit(Articles $article, Request $request, EntityManagerInterface $em , CategorieRepository $categoriesRepository ): Response // La méthode edit() permet de modifier les informations d'un utilisateur existant
    {

        $categories = $categoriesRepository->findAll();

        if ($request->isMethod('POST')) { // Si la requête est de type POST (formulaire soumis)
            
            $article->setTitre($request->request->get('titre')); // Attribue le nom de l'utilisateur depuis la requête
            $article->setContenu($request->request->get('contenu')); // Attribue l'email depuis la requête
            
            // Récupérer l'ID de la catégorie depuis le formulaire
            $categorieId = $request->request->get('id_categorie');

            // Rechercher l'entité Categorie correspondante
            $categorie = $categoriesRepository->find($categorieId);
           
            // Attribue l'entité categorie a la variable article
            $article->setIdCategorie($categorie); 

            $em->persist($article);
            $em->flush(); // Sauvegarde les modifications apportées à l'utilisateur dans la base de données

            return $this->redirectToRoute('articles_index'); // Redirige vers la page de la liste des utilisateurs après modification
        }

        return $this->render('articles/edit.html.twig', ['article' => $article , 'categories' => $categories]); // Affiche le formulaire avec les données de l'utilisateur à modifier
    }

    #[Route('/{id}/delete', name:  'articles_delete', methods: ['POST'])] // La route '/{id}/delete' permet de supprimer un article
    public function delete(Articles $article, EntityManagerInterface $em): Response // La méthode delete() permet de supprimer un article existant
    {
        $em->remove($article); // Supprime l'utilisateur de la base de données
        $em->flush(); // Sauvegarde la suppression dans la base de données

        return $this->redirectToRoute('articles_index'); // Redirige vers la liste des utilisateurs après suppression
    }

    
    #[Route('/{id}', name: 'article_show', methods: ['GET'])]
    public function show(Articles $article, CommentaireRepository $commentaireRepository): Response
    {
        
         // Récupérer l'ID de la catégorie depuis le formulaire
         $articleId = $article->getid();

         // Rechercher l'entité Categorie correspondante
         $commentaires = $commentaireRepository->findBy(['id_article' =>$articleId]);

        return $this->render('articles/show.html.twig', [
            'article' => $article,
            'commentaires' => $commentaires
        ]);
    }
}
