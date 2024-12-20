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
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/articles')]
final class CommentaireController extends AbstractController
{
    #[Route(name: 'commentaire_index', methods: ['GET'])]
    public function index(CommentaireRepository $commentaireRepository): Response
    {
        $commentaires = $commentaireRepository->findAll();

        $data = array_map(function (Commentaire $commentaire) {
            return [
                'id' => $commentaire->getId(),
                'id_user_id' => $commentaire-> getIdUser(),
                'id_article_id' => $commentaire->getIdArticle(),
                'contenu' => $commentaire->getContenu(),
            ];
            }, $commentaires);
        return new JsonResponse($data, JsonResponse::HTTP_OK);

    }

    #[Route( '{id}/commentaire/new',name: 'commentaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, Articles $article): Response
    {   
        
        if($request->isMethod('POST')){

            
            $commentaire = new Commentaire(); // On crée une nouvelle instance de l'entité commentaire

            // On récupère les données soumises dans le formulaire et on les attribue à l'entité $commentaire
            // $commentaire->setIdArticle($request->request->get('article'));

            $commentaire->setIdUser($this->getUser()); // Attribue le titre depuis la requête

            $commentaire->setIdArticle($article);
            $commentaire->setContenu($request->request->get('contenu')); // Attribue le contunu depuis la requête        
            
            $em->persist($commentaire); // Prépare l'entité $commentaire à être sauvegardée dans la base de données
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

            // On récupère les données soumises dans le formulaire et on les attribue à l'entité $commentaire
            // $commentaire->setIdArticle($request->request->get('article'));

            $commentaire->setContenu($request->request->get('contenu')); // Attribue le contunu depuis la requête        
            
            $em->persist($commentaire); // Prépare l'entité $commentaire à être sauvegardée dans la base de données
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

    #[Route('/{article}/commentaire/delete/{id}',name: 'commentaire_delete', methods: ['POST'])]
    public function delete($article,Commentaire $commentaire , EntityManagerInterface $entityManager): Response 
    {       
            $entityManager->remove($commentaire);
            $entityManager->flush();   

            return $this->redirectToRoute('article_show', [
                'id'=> $article
            ], Response::HTTP_SEE_OTHER);

    }


    #[Route(name: 'api_commentaire_delete', methods: ['DELETE'])]
    public function delete_api(Commentaire $commentaire , EntityManagerInterface $entityManager): Response 
    {       
            $entityManager->remove($commentaire);
            $entityManager->flush();   

            return new JsonResponse(['status' => 'User deleted'], JsonResponse::HTTP_OK);
    }
}
