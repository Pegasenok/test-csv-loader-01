<?php


namespace App\Util;


use App\Exception\BadLogicException;

trait ConfigurationAwareTrait
{
    private array $configuration;

    /**
     * @param array $configuration
     */
    public function setConfiguration(array $configuration): void
    {
        $this->configuration = $configuration;
    }

    /**
     * @param string $serviceName
     * @return mixed
     * @throws BadLogicException
     */
    protected function get(string $serviceName)
    {
        if (!isset($this->configuration[$serviceName])) {
            throw new BadLogicException('Services misconfiguration.');
        }
        return $this->configuration[$serviceName];
    }
}