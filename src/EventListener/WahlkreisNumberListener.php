<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\EventListener;

use App\Entity\WahlkreisNumber;
use App\Entity\WahlkreisNumberLog;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;

#[AsEntityListener(event: Events::postUpdate, method: 'logChanges', entity: WahlkreisNumber::class)]
final readonly class WahlkreisNumberListener
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security,
    ) {
    }

    public function logChanges(WahlkreisNumber $number, PostUpdateEventArgs $event): void
    {
        $unitOfWork = $event->getObjectManager()->getUnitOfWork();
        $changeSet = $unitOfWork->getEntityChangeSet($number);
        $log = new WahlkreisNumberLog();
        $log->setChanges(array_map(static fn ($value) => ['old' => $value[0], 'new' => $value[1]], $changeSet));
        $log->setWahlkreisNumber($number);
        $log->setChangedAt($number->getUpdatedAt());
        $log->setChangedBy($this->security->getUser());
        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }
}
