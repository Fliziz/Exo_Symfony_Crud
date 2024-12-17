<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Articles;
use App\Form\CommentaireType;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/articles')]
final class CommentaireController extends AbstractController
{
    #[Route(name: 'commentaire_index', methods: ['GET'])]
    public function index(CommentaireRepository $commentaireRepository): Response
    {
        return $this->render('commentaire/index.html.twig', [
            'commentaires' => $commentaireRepository->findAll(),
        ]);
    }

    #[Route( '{id}/commentaire/new',name: 'commentaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, Articles $article): Response
    {   
        
        if($request->isMethod('POST')){

            
            $commentaire = new Commentaire(); // On crée une nouvelle instance de l'entité commentaire

            // On récupère les données soumises dans le formulaire et on les attribue à l'entité $user
            // $commentaire->setIdArticle($request->request->get('article'));

            $commentaire->setIdUser($this->getUser()); // Attribue le titre depuis la requête

            $commentaire->setIdArticle($article);
            $commentaire->setContenu($request->request->get('contenu')); // Attribue le contunu depuis la requête        
            
            $em->persist($commentaire); // Prépare l'entité $user à être sauvegardée dans la base de données
            $em->flush(); // Sauvegarde réellement les données dans la base de données
            
            return $this->redirectToRoute('article_show', [
                'id'=> $article->getId()
            ]);
        }
        
        return $this->render('commentaire/new.html.twig', [
            'article' => $article,
        ]);
    }


    #[Route('{id}/edit/{com}', name: 'commentaire_edit', methods: ['GET', 'POST'])]
    public function edit($com,Request $request,CommentaireRepository $commentaireRepository, EntityManagerInterface $em,Articles $article): Response
    {
       
        // $commentaires = $commentaireRepository -> findAll();
        $commentaire = $commentaireRepository->find($com);
        
        if($request->isMethod('POST')){

            // On récupère les données soumises dans le formulaire et on les attribue à l'entité $user
            // $commentaire->setIdArticle($request->request->get('article'));

            $commentaire->setContenu($request->request->get('contenu')); // Attribue le contunu depuis la requête        
            
            $em->persist($commentaire); // Prépare l'entité $user à être sauvegardée dans la base de données
            $em->flush(); // Sauvegarde réellement les données dans la base de données
            
            return $this->redirectToRoute('article_show', [
                'id'=> $article->getId()
            ]);
        }

        return $this->render('commentaire/edit.html.twig', [
            'article' => $article,
            'commentaire' => $commentaire
        ]);
    }

    #[Route('{article}/commentaire/delete/{id}',name: 'commentaire_delete', methods: ['POST'])]
    public function delete($article,Request $request,Commentaire $commentaire , EntityManagerInterface $entityManager, CommentaireRepository $commentaireRepository): Response
    {       
            $entityManager->remove($commentaire);
            $entityManager->flush();   

        return $this->redirectToRoute('article_show', [
            'id'=> $article
        ], Response::HTTP_SEE_OTHER);
    }
}
