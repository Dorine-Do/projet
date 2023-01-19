<?php

namespace App\Utility;

use App\Entity\Main\Log;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class DbHandler extends AbstractProcessingHandler
{

    private $manager;
    //$level = Logger::DEBUG, bool $bubble = true
    public function __construct(EntityManagerInterface $manager)
    {
        //$level, $bubble
        parent::__construct();
        $this->manager = $manager;
    }

    /**
     * @inheritDoc
     */
    protected function write(array $record): void
    {
        $log = new Log();

        $log->setContext($record['context']);
        $log->setLevel($record['level']);
        $log->setLevelName($record['level_name']);
        $log->setMessage($record['message']);

        $this->manager->persist($log);
        $this->manager->flush();
    }
}