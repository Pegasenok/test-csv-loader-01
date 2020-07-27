<?php


namespace App\Util;


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
     */
    protected function get(string $serviceName)
    {
        return $this->configuration[$serviceName];
    }
}