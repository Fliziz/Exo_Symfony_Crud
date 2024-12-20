<?php

namespace App\Repository;

use App\Entity\Articles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Articles>
 */
class ArticlesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Articles::class);
    }

    public function findByTitleAndCategory(?string $recherche, ?int $Categorie): array
    {
        $data = $this->createQueryBuilder('articles'); //l'entité Articles    

        // Si un mot-clé est présent, ajoute une condition LIKE pour le titre
        if ($recherche) {
            $data->andWhere('articles.titre LIKE :recherche')
               ->setParameter('recherche', '%' . $recherche . '%');
        }

        // Si un filtre de catégorie est présent, ajoute une condition pour l'ID de catégorie
        if ($Categorie) {
            $data->andWhere('articles.id_categorie = :categorie')
               ->setParameter('categorie', $Categorie);
        }

        return $data->getQuery()->getResult();
    }
    //    /**
    //     * @return Articles[] Returns an array of Articles objects
    //     */
    //    public function findByTitle($value): array
    //    {
    //        return $this->createQueryBuilder('articles')
    //            ->andWhere('articles.titre LIKE :val')
    //            ->setParameter('val', '%'.$value.'%')
    //            ->getQuery()
    //            ->getResult()
    //            ;
    //    }

    //    public function findOneBySomeField($value): ?Articles
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
