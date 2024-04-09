<?php
declare(strict_types=1);

namespace App\Tests\Repository;

use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MessageRepositoryTest extends KernelTestCase
{
    /** @var  EntityManagerInterface $entityManager */
    protected EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        /** @var EntityManagerInterface $entityManger */
        $entityManger = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        $this->entityManager = $entityManger;

        //In case leftover entries exist
        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();

        // Drop and recreate tables for all entities
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    public function test_it_has_connection(): void
    {
        /** @var MessageRepository $messages */
        $messages = self::getContainer()->get(MessageRepository::class);
        
        $this->assertSame([], $messages->findAll());
    }
}
