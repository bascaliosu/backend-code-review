<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 *
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * accept only $status as parameter, not full request
     * in this way, you can call this method from other places where you don't have status as parameter in request
     *
     * @return Message[]
     */
    public function by(?string $status): array
    {
        if ($status) {
            /** @var Message[] $messages */
            $messages = $this->getEntityManager()
                ->createQuery(
                    sprintf("SELECT m FROM App\Entity\Message m WHERE m.status = '%s'", $status)
                )
                ->getResult();
        } else {
            $messages = $this->findAll();
        }
        
        return $messages;
    }
}
