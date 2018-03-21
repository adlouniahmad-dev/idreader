<?php

namespace App\Repository;

use App\Entity\Building;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class UserRepository extends ServiceEntityRepository
{
    private $session;

    public function __construct(RegistryInterface $registry, SessionInterface $session)
    {
        parent::__construct($registry, User::class);
        $this->session = $session;
    }

    /**
     * @param $user_id
     * @return array
     * @throws DBALException
     */
    public function getUserRoles($user_id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT role_name from role, users_roles where users_roles.user_id = :user_id and role.id = users_roles.role_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['user_id' => $user_id]);

        return $stmt->fetchAll();
    }

    /**
     * @param int $currentPage
     * @param Building $building
     * @return Paginator
     */
    public function getAllUsersFromSpecificBuilding($currentPage = 1, Building $building)
    {
        $em = $this->getEntityManager();
        $qb = new QueryBuilder($em);

        $query = $qb->select('u')
            ->from('App:User', 'u')
            ->innerJoin('u.buildings', 'b')
            ->where('b.id = :building')
            ->setParameter('building', $building->getId())
            ->getQuery();

        $paginator = $this->paginate($query, $currentPage);

        return $paginator;
    }

    /**
     * @param int $currentPage
     * @param string $string
     * @return Paginator
     */
    public function getAllUsers($currentPage = 1, $string = '')
    {
        $em = $this->getEntityManager();
        $qb = new QueryBuilder($em);

        $query = $qb->select('u')
            ->from('App:User', 'u')
            ->orderBy('u.id', 'ASC')
            ->where(
                'u.givenName LIKE :string
                 OR u.familyName LIKE :string
                 OR u.gmail LIKE :string
                 OR u.dob LIKE :string
                 OR u.phoneNb LIKE :string
                 OR u.dateCreated LIKE :string'
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
