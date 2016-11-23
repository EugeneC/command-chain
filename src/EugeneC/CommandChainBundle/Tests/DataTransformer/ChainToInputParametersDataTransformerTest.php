<?php
namespace EugeneC\CommandChainBundle\Tests\DataTransformer;

use EugeneC\CommandChainBundle\DataTransformer\ChainToInputParametersDataTransformer;

/**
 * Test for ChainToInputParametersDataTransformer
 */
class ChainToInputParametersDataTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ChainToInputParametersDataTransformer
     */
    private $transformer;

    /**
     * Set up ChainToInputParametersDataTransformer
     */
    public function setUp()
    {
        $this->transformer = new ChainToInputParametersDataTransformer();
    }

    /**
     * @test
     * @expectedException \TypeError
     */
    public function composeWithWrongType()
    {
        $this->transformer->compose('wrong value');
    }

    /**
     * @test
     * @expectedException \DomainException
     */
    public function composeWithoutChildCommandName()
    {
        $this->transformer->compose(['wrong_parameter' => 'foo:hi']);
    }

    /**
     * @test
     */
    public function composeWithoutParameters()
    {
        static::assertEquals(
            ['command' => 'foo:hi'],
            $this->transformer->compose(
                [
                    'main'  => 'bar:hello',
                    'child' => 'foo:hi'
                ]
            )
        );
    }

    /**
     * @test
     */
    public function composeWithParameters()
    {
        static::assertEquals(
            [
                'command'   => 'foo:hello',
                'firstName' => 'Foo'
            ],
            $this->transformer->compose(
                [
                    'main'       => 'bar:hi',
                    'child'      => 'foo:hello',
                    'parameters' => [
                        'arg1' => [
                            'name'  => 'firstName',
                            'value' => 'Foo'
                        ]
                    ]
                ]
            )
        );
    }
}
