<?php
namespace EugeneC\CommandChainBundle\Tests\Composite;

use EugeneC\CommandChainBundle\Composite\CommandChainList;

/**
 * Test for CommandChainList
 */
class CommandChainListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CommandChainList
     */
    private $list;

    /**
     * Set up CommandChainList
     */
    public function setUp()
    {
        $chains = [
            'chain_1' => [
                'main' => 'bar:hi',
                'child' => 'foo:hello',
                'parameters' => [
                    'arg1' => [
                        'name' => 'firstName',
                        'value' => 'Foo'
                    ]
                ]
            ],
            'chain_2' => [
                'main' => 'bar:hello',
                'child' => 'foo:hi'
            ]
        ];
        $this->list = new CommandChainList($chains);
    }

    /**
     * @test
     */
    public function getEmptyParents()
    {
        static::assertEquals([], $this->list->getParents('bar:hi'));
    }

    /**
     * @test
     */
    public function getBarHiAsParent()
    {
        static::assertEquals(['bar:hi'], $this->list->getParents('foo:hello'));
    }

    /**
     * @test
     */
    public function isBarHiMasterCommand()
    {
        static::assertTrue($this->list->isMaster('bar:hi'));
    }

    /**
     * @test
     */
    public function isFooHelloMasterCommand()
    {
        static::assertFalse($this->list->isMaster('foo:hello'));
    }

    /**
     * @test
     */
    public function getEmptyChildren()
    {
        static::assertEquals([], $this->list->getChildren('foo:hello'));
    }

    /**
     * @test
     */
    public function getChildrenForBarHi()
    {
        static::assertEquals([['command' => 'foo:hello', 'firstName' => 'Foo']], $this->list->getChildren('bar:hi'));
    }

    /**
     * @test
     */
    public function isBarHiChildCommand()
    {
        static::assertFalse($this->list->isChild('bar:hi'));
    }

    /**
     * @test
     */
    public function isFooHelloChildCommand()
    {
        static::assertTrue($this->list->isChild('foo:hello'));
    }

    /**
     * @test
     */
    public function getMasterCommandsForNonconfiguredCommand()
    {
        static::assertEquals([], $this->list->getParents('cache:clear'));
    }

    /**
     * @test
     */
    public function isNonconfiguredCommandMaster()
    {
        static::assertFalse($this->list->isMaster('cache:clear'));
    }

    /**
     * @test
     */
    public function getChildrenCommandsForNonconfiguredCommand()
    {
        static::assertEquals([], $this->list->getChildren('cache:clear'));
    }

    /**
     * @test
     */
    public function isNonconfiguredCommandChild()
    {
        static::assertFalse($this->list->isChild('cache:clear'));
    }

    /**
     * @test
     */
    public function getChildrenWithputParametersForBarHello()
    {
        static::assertEquals([['command' => 'foo:hi']], $this->list->getChildren('bar:hello'));
    }
}
