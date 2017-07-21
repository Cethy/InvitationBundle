<?php

namespace Cethyworks\InvitationBundle\Tests\Security\Provider;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\UserProvider\UserProviderFactoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

abstract class AbstractProviderFactoryTest extends TestCase
{
    /**
     * @var string
     */
    protected $factoryClass;

    abstract public function testCreate();

    abstract public function dataTestAddValidConfiguration();
    abstract public function dataTestAddInvalidConfiguration();

    /**
     * @dataProvider dataTestAddValidConfiguration
     */
    public function testAddValidConfiguration(array $inputConfig, array $expectedConfig)
    {
        $providerId = 'provider_id';

        $nodeDefinition = new ArrayNodeDefinition($providerId);

        /** @var UserProviderFactoryInterface $factory */
        $factory = new $this->factoryClass('key', $providerId);

        $factory->addConfiguration($nodeDefinition);

        $node = $nodeDefinition->getNode();
        $normalizedConfig = $node->normalize($inputConfig);
        $finalizedConfig = $node->finalize($normalizedConfig);

        $this->assertEquals($expectedConfig, $finalizedConfig);
    }

    /**
     * @dataProvider dataTestAddInvalidConfiguration
     */
    public function testAddInvalidConfiguration(array $inputConfig)
    {
        $this->setExpectedException(InvalidConfigurationException::class);


        $providerId = 'provider_id';

        $nodeDefinition = new ArrayNodeDefinition($providerId);

        /** @var UserProviderFactoryInterface $factory */
        $factory = new $this->factoryClass('key', $providerId);

        $factory->addConfiguration($nodeDefinition);

        $node = $nodeDefinition->getNode();
        $normalizedConfig = $node->normalize($inputConfig);

        // will throw InvalidConfigurationException
        $node->finalize($normalizedConfig);
    }
}
