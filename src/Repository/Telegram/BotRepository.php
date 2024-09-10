<?php

declare(strict_types=1);

namespace App\Repository\Telegram;

use App\Entity\Telegram\Bot;
use App\Enum\Telegram\BotType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bot::class);
    }

    public function getAll(): array
    {
        return $this->findAll();
    }

    public function getById(int $id): ?Bot
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function getByToken(string $token): ?Bot
    {
        return $this->findOneBy(['token' => $token]);
    }

    /**
     * @param string $name
     * @return Bot
     */
    public function getByName(string $name): Bot
    {
        $bot = $this->findOneBy(['name' => $name]);

        if (!$bot instanceof Bot) {
            throw new \RuntimeException('Expected instance of Bot, got '
                . (is_object($bot)
                    ? get_class($bot)
                    : gettype($bot))
            );
        }

        return $bot;
    }

    public function getByBotType(BotType $botType): ?Bot
    {
        return $this->findOneBy(['botType' => $botType]);
    }

    public function findActiveBots(): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.isActive = :isActive')
            ->setParameter('isActive', true)
            ->getQuery()
            ->getResult();
    }

    public function findByDateRange(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.createdAt BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getResult();
    }

    public function findByNameLike(string $name): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.name LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->getQuery()
            ->getResult();
    }

    public function findActiveByType(BotType $botType): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.isActive = :isActive')
            ->andWhere('b.botType = :botType')
            ->setParameter('isActive', true)
            ->setParameter('botType', $botType)
            ->getQuery()
            ->getResult();
    }

    public function save(Bot $bot, bool $flush = false): void
    {
        $this->_em->persist($bot);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function delete(Bot $bot, bool $flush = false): void
    {
        $this->_em->remove($bot);
        if ($flush) {
            $this->_em->flush();
        }
    }
}
