<?php
namespace EugeneC\CommandChainBundle\Composite;

/**
 * Class CommandChainListInterface
 */
interface CommandChainListInterface
{
    /**
     * @param string $commandName Current command name
     * @return array
     */
    public function getChildren($commandName);

    /**
     * @param string $commandName Current command name
     * @return array
     */
    public function getParents($commandName);

    /**
     * @param string $commandName Current command name
     * @return bool
     */
    public function isMaster($commandName);

    /**
     * @param string $commandName Current command name
     * @return bool
     */
    public function isChild($commandName);
}