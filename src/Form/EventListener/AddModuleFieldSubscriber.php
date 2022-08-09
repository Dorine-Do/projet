<?php

namespace App\Form\EventListener;

use App\Entity\Module;
use App\Repository\LinkInstructorSessionModuleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AddModuleFieldSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [ FormEvents::PRE_SET_DATA => 'selectSessionData' ];
    }

    public function selectSessionData( FormEvent $event )
    {
        $session = $event->getData()->getSession();
        $form = $event->getForm();

        if( !$session || $session->getId() === null )
        {
            $form->add('module', EntityType::class, [
                'class'     => Module::class,
                'choices'   => function( LinkInstructorSessionModuleRepository $linkInstructorSessionModuleRepo ) {
                    $linksInstructorSessionModule = $linkInstructorSessionModuleRepo->findBy([]);
                    $modules = [];
                    foreach($linksInstructorSessionModule as $linkInstructorSessionModule)
                    {
                        $modules[] = $linkInstructorSessionModule->getModule();
                    }
                    return $modules;
                }
            ]);
        }
    }
}