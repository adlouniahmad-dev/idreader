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
            ->orderBy('o.dateCreated', 'DESC')
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

        if (!$building) {
            $query = $qb->select('o')
                ->from('App:Office', 'o')
                ->leftJoin('o.user', 'u')
                ->innerJoin('o.building', 'b')
                ->orderBy('o.id', 'ASC')
                ->where(
                    'o.dateCreated LIKE :string
                OR o.officeNb LIKE :string
                OR o.floorNb LIKE :string
                OR u.givenName LIKE :string
                OR u.familyName LIKE :string
                OR b.name LIKE :string'
                )
                ->setParameter('string', '%' . $string . '%')
                ->orderBy('o.dateCreated', 'DESC')
                ->getQuery();
        } else {

            $query = $qb->select('o')
                ->from('App:Office', 'o')
                ->innerJoin('App:Building', 'b', 'WITH', 'o.building = :building')
                ->leftJoin('o.user', 'u')
                ->orderBy('o.id', 'ASC')
                ->where(
                    'o.dateCreated LIKE :string
                OR o.officeNb LIKE :string
                OR o.floorNb LIKE :string
                OR u.givenName LIKE :string
                OR u.familyName LIKE :string'
                )
                ->setParameter('string', '%' . $string . '%')
                ->setParameter('building', $building)
                ->orderBy('o.dateCreated', 'DESC')
                ->getQuery();
        }

        $paginator = $this->paginate($query, $currentPage);

        return $paginator;
    }

    /**
     * @param $officeId
     * @param $dateCreated
     * @param $officeNb
     * @param $memberName
     * @param $buildingId
     * @param $floorNb
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function advancedSearch($officeId, $dateCreated, $officeNb, $memberName, $buildingId, $floorNb)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT office.id, office.date_created, office.office_nb, CONCAT(user.given_name, ' ', user.family_name) AS member, building.name, office.floor_nb
                FROM office, building, user
                WHERE office.building_id = building.id AND office.building_id LIKE :buildingId AND office.user_id = user.id 
                AND office.id LIKE :officeId
                AND office.office_nb LIKE :officeNb
                AND office.date_created LIKE :dateCreated
                AND office.floor_nb LIKE :floorNb
                AND CONCAT(user.given_name, ' ', user.family_name) LIKE :memberName";

        $stmt = $conn->prepare($sql);
        $stmt->execute(array(
            'officeId' => '%' . $officeId . '%',
            'officeNb' => '%' . $officeNb . '%',
            'dateCreated' => '%' . $dateCreated . '%',
            'memberName' => '%' . $memberName . '%',
            'buildingId' => '%' . $buildingId . '%',
            'floorNb' => $floorNb === '' ? '%%' : $floorNb,
        ));

        return $stmt->fetchAll();
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
