<?php
namespace EugeneC\CommandChainBundle\Composite;

/**
 * Class CommandChainList container for chains configuration
 */
class CommandChainList implements CommandChainListInterface
{
    /**
     * @var array Chains configuration
     */
    protected $chainList = [];

    /**
     * CommandChainList constructor.
     * @param array $chains Chains configuration
     */
    public function __construct($chains)
    {
        $this->chainList = $chains;
    }

    /**
     * Is current command parent
     * @param string $commandName Current command name
     * @return bool
     */
    public function isMaster($commandName)
    {
        return 0 !== count($this->getChildren($commandName));
    }

    /**
     * Is current command child
     * @param string $commandName Current command name
     * @return bool
     */
    public function isChild($commandName)
    {
        return 0 !== count($this->getParents($commandName));
    }

    /**
     * Get parent commands for current command
     * @param string $commandName Current command name
     * @return array
     */
    public function getParents($commandName)
    {
        $parents = [];
        foreach ($this->chainList as $chain) {
            if ($commandName === $chain['child']) {
                $parents[] = $chain['main'];
            }
        }

        return $parents;
    }

    /**
     * Get children commands with parameters for current command
     * @param string $commandName Current command name
     * @return array
     */
    public function getChildren($commandName)
    {
        $children = [];
        foreach ($this->chainList as $chain) {
            if ($commandName === $chain['main']) {
                $inputParameters = ['command' => $chain['child']];
                if (array_key_exists('parameters', $chain)) {
                    foreach ($chain['parameters'] as $parameter) {
                        $inputParameters[$parameter['name']] = $parameter['value'];
                    }
                }
                $children[] = $inputParameters;
            }
        }

        return $children;
    }
}