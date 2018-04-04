<?php

namespace App\Repository;

use App\Entity\Blacklist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Doctrine\RegistryInterface;

class BlacklistRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Blacklist::class);
    }

    /**
     * @param int $currentPage
     * @param string $string
     * @return mixed
     */
    public function getAllBlacklistedVisitors($currentPage = 1, $string = '')
    {
        $em = $this->getEntityManager();
        $qb = new QueryBuilder($em);

        $query = $qb->select('b')
            ->from('App:Blacklist', 'b')
            ->innerJoin('b.visitor', 'v', 'WITH', 'b.visitor = v')
            ->where(
                'b.dateAdded LIKE :string
                OR b.id LIKE :string
                OR v.firstName LIKE :string
                OR v.middleName LIKE :string
                OR v.lastName LIKE :string
                OR v.nationality LIKE :string'
            )
            ->orderBy('b.dateAdded', 'DESC')
            ->setParameter('string', '%' . $string . '%')
            ->getQuery();

        $paginator = $this->paginate($query, $currentPage);

        return $paginator;
    }

    /**
     * @param $dql
     * @param int $page
     * @param int $limit
     * @return Paginator
     */
    private function paginate($dql, $page = 1, $limit = 10)
    {
        $paginator = new Paginator($dql);
        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);

        return $paginator;
    }

}
