<?php

namespace App\EventListener;

use App\Entity\Main\Log;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;

class DoctrineSubscriber implements EventSubscriber
{
    public function __construct(LoggerInterface $dbLogger)
    {
        $this->logger = $dbLogger;
    }

    /**
     * @inheritDoc
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove
        ];
    }

    public function postPersist (LifecycleEventArgs $args)
    {
        $this->log('Added', $args);
    }

    public function postUpdate (LifecycleEventArgs $args)
    {
        $this->log("Updated", $args);
    }

    public function postRemove (LifecycleEventArgs $args)
    {
        $this->log("Deleted", $args);
    }

    public function log ($message, $args)
    {
        if(!$args->getObject() instanceof Log)
        {
            $className = explode('\\', get_class($args->getObject())) ;
            $this->logger->info( end($className).' '.$message);
        }
    }
}