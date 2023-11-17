<?php

namespace App\Repository;

use App\Entity\Product;
use App\Model\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry,
    private PaginatorInterface $paginatorInterface)
    {
        parent::__construct($registry, Product::class);
    }

    //    /**
    //     * @return Product[] Returns an array of Product objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Product
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    /**
     * Get published posts thanks to Search Data value
     *
     * @param SearchData $searchData
     * @return PaginationInterface
     */

    // public function findBySearch(SearchData $searchData): PaginationInterface
    // {
    //     $data = $this->createQueryBuilder('p')
    //         ->where('p.online LIKE :online')
    //         ->setParameter('online', '%1%')
    //         ->addOrderBy('p.created_at', 'DESC');

    //     if (!empty($searchData->q)) {
    //         $data = $data
    //             ->andWhere('p.name LIKE :q')
    //             ->setParameter('q', "%{$searchData->q}%");
    //     }

    //     // if (!empty($searchData->categories)) {
    //     //     $data = $data
    //     //         ->join('p.categories', 'c')
    //     //         ->andWhere('c.id IN (:categories)')
    //     //         ->setParameter('categories', $searchData->categories);
    //     // }

    //     $data = $data
    //         ->getQuery()
    //         ->getResult();

    //     $posts = $this->paginatorInterface->paginate($data, $searchData->page, 9);

    //     return $posts;
    // }
}
