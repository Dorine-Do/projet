<?php

namespace App\Utility;

use App\Entity\Main\Log;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class DbHandler extends AbstractProcessingHandler
{

    private $manager;

    public function __construct(EntityManagerInterface $manager, UserRepository $userRepository, )
    {
        parent::__construct();
        $this->manager = $manager;
        $this->userRepository = $userRepository;
    }

    /**
     * @inheritDoc
     */
    protected function write(array $record): void
    {
        try {
            $log = new Log();
            $log->setContext($record['context']);
            $log->setLevel($record['level']);
            $log->setLevelName($record['level_name']);
            $log->setMessage($record['message']);
            $log->setExtra($record['extra']);
            if ( $record['extra']['user'] )
            {
                $log->setUser($this->userRepository->find($record['extra']['user']));
            }

            $this->manager->persist($log);
            $this->manager->flush();
        }catch (\Error $e)
        {
            dd($e);
        }
    }
}