<?php


namespace App\Command;


class UploadCsvCommand implements CommandInterface
{
    public string $name = 'uploadCsv';
    public array $payload;
    /**
     * @var CommandId
     */
    private CommandId $id;

    /**
     * @param \SplFileObject[] $files
     */
    public function setFiles(array $files)
    {
        $this->payload = ['files' => array_map(fn($file) => $file->getRealPath(), $files)];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getCommandId()->getId(),
            'name' => $this->getName(),
            'payload' => $this->getPayload()
        ];
    }

    public function setCommandId(CommandId $commandId)
    {
        $this->id = $commandId;
    }

    public function getCommandId(): CommandId
    {
        return $this->id;
    }
}