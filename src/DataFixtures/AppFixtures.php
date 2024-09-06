<?php

namespace App\DataFixtures;

use App\Entity\Telegram\InterfaceEntity;
use App\Entity\Telegram\Keyboard;
use App\Entity\Telegram\Message;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $startMessage = new Message();
        $startMessage->setText('Hello! Welcome to the Main bot. Please choose an option:');
        $manager->persist($startMessage);

        $startKeyboard = new Keyboard();
        $startKeyboard->setButtons(['🚀 Create Bot', '🗝 Managing Bots']);
        $manager->persist($startKeyboard);

        $startInterface = new InterfaceEntity();
        $startInterface->setName('start');
        $startInterface->setMessage($startMessage);
        $startInterface->setKeyboard($startKeyboard);
        $manager->persist($startInterface);

        $createMessage = new Message();
        $createMessage->setText('Please send your bot name.');
        $manager->persist($createMessage);

        $createInterface = new InterfaceEntity();
        $createInterface->setName('Create Bot');
        $createInterface->setMessage($createMessage);
        $manager->persist($createInterface);

        $manager->flush();
    }
}
