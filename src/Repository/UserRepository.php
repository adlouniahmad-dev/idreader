<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\DBALException;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
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
}
