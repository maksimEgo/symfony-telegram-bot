<?php

declare(strict_types=1);

namespace App\Repository\Telegram;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Telegram\User;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionProperty;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getAll(): array
    {
        return $this->findAll();
    }

    /**
     * Find a User by a specified field.
     *
     * This method allows searching for a user any property field.
     *
     * @param string $fieldName The field name to search by.
     * @param string|int $value The value of the field to search for.
     * @return User|null The user found, or null if no user is found.
     * @throws InvalidArgumentException If an unsupported field is provided.
     */
    public function findByField(string $fieldName, string|int $value): ?User
    {
        $entityReflection = new ReflectionClass(objectOrClass: User::class);
        $properties       = $entityReflection->getProperties(filter: ReflectionProperty::IS_PRIVATE);
        $allowedFields    = array_map(static fn($property) => $property->getName(), $properties);

        if (!in_array($fieldName, $allowedFields, true)) {
            throw new InvalidArgumentException('Unsupported field name: ' . $fieldName);
        }

        return $this->findOneBy([$fieldName => $value]);
    }

    public function findActiveUsers(): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.isActive = :isActive')
            ->setParameter('isActive', true)
            ->getQuery()
            ->getResult();
    }

    public function findByDateRange(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.createdAt BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getResult();
    }

    public function findByNameLike(string $name): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.userName LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->getQuery()
            ->getResult();
    }

    public function save(User $user, bool $flush = false): void
    {
        $this->getEntityManager()->persist($user);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function delete(User $user, bool $flush = false): void
    {
        $this->getEntityManager()->remove($user);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllPaginated(int $limit, int $offset): array
    {
        return $this->createQueryBuilder('u')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }
}
