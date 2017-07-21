<?php

namespace Cethyworks\InvitationBundle\Security\Provider;

use Cethyworks\InvitationBundle\Model\InvitationInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;

class EntityProvider extends AbstractUserInvitedProvider
{
    /**
     * @var string
     */
    protected $invitationClass;

    /**
     * @var ManagerRegistry
     */
    protected $registry;

    /**
     * @var string
     */
    protected  $repositoryMethod;

    /**
     * @var string
     */
    protected  $entityProperty;

    /**
     * @var null|string
     */
    protected  $managerName;

    /**
     * EntityProvider constructor.
     *
     * @param string          $invitationClass
     * @param ManagerRegistry $registry
     * @param string          $repositoryMethod
     * @param string          $entityProperty
     * @param null|string     $managerName
     */
    function __construct($invitationClass, ManagerRegistry $registry, $repositoryMethod, $entityProperty, $managerName = null)
    {
        $this->checkInvitationClass($invitationClass);

        $this->invitationClass = $invitationClass;

        $this->registry         = $registry;

        $this->repositoryMethod = $repositoryMethod;
        $this->entityProperty   = $entityProperty;
        $this->managerName      = $managerName;
    }

    /**
     * Return an Invitation or null if not found
     * @return null|InvitationInterface
     */
    protected function getInvitation($code)
    {
        return $this->getRepository()->{$this->repositoryMethod}([
            $this->entityProperty => $code
        ]);
    }

    /**
     * @return ObjectManager
     */
    private function getObjectManager()
    {
        return $this->registry->getManager($this->managerName);
    }

    /**
     * @return ObjectRepository
     */
    private function getRepository()
    {
        return $this->getObjectManager()->getRepository($this->invitationClass);
    }
}
