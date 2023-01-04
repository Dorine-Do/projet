<?php

namespace App\Helpers;

use App\Repository\LogRepository;
use App\Entity\Main\Log;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\HttpFoundation\Request;

class LogHelper
{
    private array $levels = [
        'info' => 1,
        'warning' => 0,
        'error' => -1,
    ];

    private EntityManagerInterface $manager;
    private Request $request;

    public function __construct( EntityManagerInterface $manager, Request $request )
    {
        $this->manager = $manager;
        $this->request = $request;
    }

    public function saveLog( $msg, $lvl = 'info' )
    {
        $log = new Log();
        $log->setLog($msg);
        $log->setLevel($this->levels[$lvl]);
        $log->setPath( $this->request->server->get('REQUEST_URI') );
        $now = new \DateTime();
        $log->setLatency( $now->getTimestamp() - $this->request->server->get('REQUEST_TIME') . 'ms' );

        $this->manager->persist($log);
        $this->manager->flush();
    }
}