<?php

namespace App\Utility;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class DbProcessor
{
    private $request;

    public function __construct(RequestStack $request, Security $security)
    {
        $this->request = $request->getCurrentRequest();
        $this->security = $security;
    }

    public function __invoke(array $record) :array
    {
        try {
            $record['extra']['path'] = $this->request->getPathInfo();
            $record['extra']['method'] = $this->request->getMethod();
            $now = new \DateTime();
            $record['extra']['latency'] = $now->getTimestamp() - $this->request->server->get('REQUEST_TIME') . 'ms';
            if ( $this->security->getUser() )
            {
                $record['extra']['user'] = $this->security->getUser()->getId();
            }
            else
            {
                $record['extra']['user'] = null;
            }
        }catch ( \Error $e )
        {
            dd($e);
        }
        return $record;
    }

}