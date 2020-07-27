<?php


namespace App\Dto;


class CsvFileResponseDto implements \JsonSerializable
{
    const SUCCESS = true;
    const FAILURE = false;

    private bool $status;
    private ?string $waitId;

    /**
     * CsvFileResponseDto constructor.
     * @param bool $status
     * @param string $waitId
     */
    public function __construct(bool $status, string $waitId = null)
    {
        $this->status = $status;
        $this->waitId = $waitId;
    }

    /**
     * @return bool
     */
    public function getStatus(): bool
    {
        return $this->status;
    }

    /**
     * @return string|null
     */
    public function getWaitId(): ?string
    {
        return $this->waitId;
    }

    public function jsonSerialize()
    {
        return ['status' => $this->getStatus(), 'waitId' => $this->getWaitId()];
    }
}