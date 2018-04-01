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
     * @param string $string
     * @return mixed
     */
    public function getAllBlacklistedVisitors($string = '')
    {
        $em = $this->getEntityManager();
        $qb = new QueryBuilder($em);

        $query = $qb->select('b')
            ->from('App:Blacklist', 'b')
            ->innerJoin('App:Visitor', 'v', 'WITH', 'b.visitor = v')
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

        return $query->getResult();
    }

}
