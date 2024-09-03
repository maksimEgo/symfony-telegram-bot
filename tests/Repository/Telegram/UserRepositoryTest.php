<?php

namespace Repository\Telegram;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Telegram\User;
use App\Repository\Telegram\UserRepository;

class UserRepositoryTest extends TestCase
{
    private $registry;
    private $entityManager;
    private $userRepository;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->registry      = $this->createMock(ManagerRegistry::class);

        $classMetadata = $this->createMock(ClassMetadata::class);
        $classMetadata->name = User::class;

        $this->registry->method('getManagerForClass')->willReturn($this->entityManager);
        $this->entityManager->method('getClassMetadata')->willReturn($classMetadata);
        $this->entityManager->method('getRepository')->willReturn($this->createMock(UserRepository::class));

        $this->userRepository = new UserRepository($this->registry);
    }

    public function testGetAll(): void
    {
        $this->userRepository = $this->getMockBuilder(UserRepository::class)
            ->setConstructorArgs([$this->registry])
            ->onlyMethods(['findAll'])
            ->getMock();

        $this->userRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $result = $this->userRepository->getAll();
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testFindByFieldReturnsUser(): void
    {
        $this->userRepository = $this->getMockBuilder(UserRepository::class)
            ->setConstructorArgs([$this->registry])
            ->onlyMethods(['findOneBy'])
            ->getMock();

        $user = new User();
        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['userName' => 'testUser'])
            ->willReturn($user);

        $result = $this->userRepository->findByField('userName', 'testUser');
        $this->assertInstanceOf(User::class, $result);
    }

    public function testSaveUserSuccessfully(): void
    {
        $user = new User();

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($user);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->userRepository->save($user, true);
    }
}
