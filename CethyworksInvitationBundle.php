<?php

namespace Cethyworks\InvitationBundle;

use Cethyworks\InvitationBundle\Security\Provider\EntityProviderFactory;
use Cethyworks\InvitationBundle\Security\Provider\InMemoryProviderFactory;
use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CethyworksInvitationBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $this->addRegisterMappingsPass($container);

        if ($container->hasExtension('security')) {
            $this->addUserProviderFactories($container);
        }
    }

    private function addUserProviderFactories(ContainerBuilder $container)
    {

        $container->getExtension('security')
            ->addUserProviderFactory(new InMemoryProviderFactory(
                'invitation_memory',
                'cethyworks_invitation.in_memory.provider'
            ));

        $container->getExtension('security')
            ->addUserProviderFactory(new EntityProviderFactory(
                'invitation_entity',
                'cethyworks_invitation.entity.provider'
            ));
    }

    /**
     * @param ContainerBuilder $container
     */
    private function addRegisterMappingsPass(ContainerBuilder $container)
    {
        $mappings = array(
            realpath(__DIR__ .'/Resources/config/doctrine-mapping') => 'Cethyworks\InvitationBundle\Model',
        );
        if (class_exists('Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass')) {
            $container->addCompilerPass(DoctrineOrmMappingsPass::createYamlMappingDriver($mappings));
        }
        /*if (class_exists('Doctrine\Bundle\MongoDBBundle\DependencyInjection\Compiler\DoctrineMongoDBMappingsPass')) {
            $container->addCompilerPass(DoctrineMongoDBMappingsPass::createXmlMappingDriver($mappings, array('fos_user.model_manager_name'), 'fos_user.backend_type_mongodb'));
        }
        if (class_exists('Doctrine\Bundle\CouchDBBundle\DependencyInjection\Compiler\DoctrineCouchDBMappingsPass')) {
            $container->addCompilerPass(DoctrineCouchDBMappingsPass::createXmlMappingDriver($mappings, array('fos_user.model_manager_name'), 'fos_user.backend_type_couchdb'));
        }*/
    }
}
