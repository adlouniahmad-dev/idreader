<?php

namespace App\Repository;

use App\Entity\Visitor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Doctrine\RegistryInterface;

class VisitorRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Visitor::class);
    }

    /**
     * @param int $currentPage
     * @param string $string
     * @return Paginator
     */
    public function getAllVisitors($currentPage = 1, $string = '')
    {
        $em = $this->getEntityManager();
        $qb = new QueryBuilder($em);

        $query = $qb->select('v')
            ->from('App:Visitor', 'v')
            ->orderBy('v.id', 'DESC')
            ->where(
                'v.id LIKE :string
                OR v.firstName LIKE :string
                OR v.middleName LIKE :string
                OR v.lastName LIKE :string
                OR v.dateCreated LIKE :string
                OR v.documentType LIKE :string
                OR v.nationality LIKE :string
                OR v.ssn LIKE :string'
            )
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
