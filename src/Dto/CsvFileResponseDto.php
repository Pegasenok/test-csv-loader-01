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
     * @param bool $status
     */
    public function setStatus(bool $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string|null
     */
    public function getWaitId(): ?string
    {
        return $this->waitId;
    }

    /**
     * @param string|null $waitId
     */
    public function setWaitId(?string $waitId): void
    {
        $this->waitId = $waitId;
    }

    public function jsonSerialize()
    {
        return ['status' => $this->getStatus(), 'waitId' => $this->getWaitId()];
    }
}