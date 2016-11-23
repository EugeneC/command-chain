<?php
namespace EugeneC\CommandChainBundle\Event;

/**
 * Class ChainEvents describes exist types of chain events
 */
class ChainEvents
{
    /**
     * Fires when master command was detected
     */
    const MASTER_DETECTED = 'eugene_c_command_chain.logger.master_command_detected';

    /**
     * Fires when command executing was finished
     */
    const FINISH_COMMAND_EXECUTING = 'eugene_c_command_chain.logger.finish_command_executing';

    /**
     * Fires when child command was detected
     */
    const CHILD_DETECTED = 'eugene_c_command_chain.logger.child_command_detected';

    /**
     * Fires when executing of the master command was started
     */
    const START_MASTER_EXECUTING = 'eugene_c_command_chain.logger.start_master_command_executing';

    /**
     * Fires when children commands executing was started
     */
    const START_CHILDREN_EXECUTING = 'eugene_c_command_chain.logger.start_children_executing';

    /**
     * Fires when chain executing was finished
     */
    const FINISH_CHAIN_EXECUTING = 'eugene_c_command_chain.logger.finish_chain_executing';
}