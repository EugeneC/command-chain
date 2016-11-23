<?php
namespace EugeneC\CommandChainBundle\Tests\DependencyInjection;

use EugeneC\CommandChainBundle\DependencyInjection\EugeneCCommandChainExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class EugeneCCommandChainExtensionTest
 */
class EugeneCCommandChainExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * @var EugeneCCommandChainExtension
     */
    protected $extension;

    /**
     * Set up container and extension
     */
    public function setUp()
    {
        $this->container = new ContainerBuilder();
        $this->extension = new EugeneCCommandChainExtension();
    }

    /**
     * @test
     */
    public function checkInstance()
    {
        $this->extension->load([], $this->container);
        static::assertInstanceOf(ContainerBuilder::class, $this->container);
    }

    /**
     * @test
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidTypeException
     */
    public function invalidChainsParameterTypeConfiguration()
    {
        $this->extension->load([['chains' => ['wrong_parameter_name' => 'value']]], $this->container);
    }

    /**
     * @test
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function unrecognizedOptionConfiguration()
    {
        $this->extension->load([['wrong_parameter_name' => 'value']], $this->container);
    }

    /**
     * @test
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function unrecognizedOptionChainConfiguration()
    {
        $this->extension->load([['chains' => ['chain' => ['test_parameter' => 'value']]]], $this->container);
    }

    /**
     * @test
     */
    public function emptyChainConfiguration()
    {
        $this->extension->load([['chains' => []]], $this->container);
        static::assertEquals([], $this->container->getParameter('eugene_c_command_chain.config.chains'));
    }

    /**
     * @test
     */
    public function chainConfigurationWithoutParameters()
    {
        $chains = ['chain_1' => ['main' => 'bar:hi', 'child' => 'foo:hello']];
        $this->extension->load([['chains' => $chains]], $this->container);
        /** Empty parameters array will be configured */
        $chains['chain_1']['parameters'] = [];
        static::assertEquals($chains, $this->container->getParameter('eugene_c_command_chain.config.chains'));
    }

    /**
     * @test
     */
    public function chainConfigurationWithParameters()
    {
        $chains = [
            'chain_1' => [
                'main' => 'bar:hi',
                'child' => 'foo:hello',
                'parameters' => [
                    'arg1' => [
                        'name' => 'parameterName',
                        'value' => 'parameter value'
                    ]
                ]
            ]
        ];
        $this->extension->load([['chains' => $chains]], $this->container);
        static::assertEquals($chains, $this->container->getParameter('eugene_c_command_chain.config.chains'));
    }
}
