<?php

namespace App\Repository;

use App\Entity\Building;
use App\Entity\Office;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Doctrine\RegistryInterface;

class OfficeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Office::class);
    }

    /**
     * @param int $currentPage
     * @param Building $building
     * @return Paginator
     */
    public function getAllOfficesBuilding($currentPage = 1, Building $building)
    {
        $em = $this->getEntityManager();
        $qb = new QueryBuilder($em);

        $query = $qb->select('o')
            ->from('App:Office', 'o')
            ->orderBy('o.id', 'ASC')
            ->where('o.building = :building')
            ->setParameter('building', $building)
            ->getQuery();

        $paginator = $this->paginate($query, $currentPage);

        return $paginator;
    }

    /**
     * @param int $currentPage
     * @param string $string
     * @param Building $building
     * @return Paginator
     */
    public function getAllOffices($currentPage = 1, $string = '', Building $building = null)
    {
        $em = $this->getEntityManager();
        $qb = new QueryBuilder($em);

        $query = $qb->select('o')
            ->from('App:Office', 'o')
            ->orderBy('o.id', 'ASC')
            ->where(
                'o.dateCreated LIKE :string
                OR o.officeNb LIKE :string
                OR o.floorNb LIKE :string
                OR o.building = :building'
            )
            ->setParameter('string', '%' . $string . '%')
            ->setParameter('building', $building === null ? '%%' : $building)
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
