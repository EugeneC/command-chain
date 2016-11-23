<?php
namespace EugeneC\CommandChainBundle\DataTransformer;

/**
 * Data transformer from chain configuration to input parameters
 */
class ChainToInputParametersDataTransformer
{
    /**
     * Compose input parameters for child Command
     *
     * @param array $chain Chain configuration
     * @return array
     * @throws \DomainException
     */
    public function compose(array $chain)
    {
        if (!array_key_exists('child', $chain)) {
            throw new \DomainException('Child command name must be set.');
        }
        $inputParameters = ['command' => $chain['child']];
        if (array_key_exists('parameters', $chain)) {
            foreach ($chain['parameters'] as $parameter) {
                $inputParameters[$parameter['name']] = $parameter['value'];
            }
        }

        return $inputParameters;
    }
}