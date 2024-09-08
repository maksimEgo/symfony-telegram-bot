<?php

namespace App\Repository\Telegram;

use App\Entity\Telegram\InterfaceEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class InterfaceEntityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InterfaceEntity::class);
    }

    public function getByName(string $name): ?InterfaceEntity
    {
        return $this->findOneBy(['name' => $name]);
    }
}
