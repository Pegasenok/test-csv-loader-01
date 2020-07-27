<?php


namespace App\Command;


use App\Model\UserLoadingModel;

class UploadCsvCommand implements CommandInterface
{
    CONST UPLOAD_CSV_COMMAND_NAME = 'uploadCsv';
    public array $payload;

    /**
     * @var CommandId
     */
    private CommandId $id;
    private UserLoadingModel $executionModel;

    /**
     * UploadCsvCommand constructor.
     * @param UserLoadingModel|null $executionModel // null in case we use this class as a message
     */
    public function __construct(?UserLoadingModel $executionModel = null)
    {
        if ($executionModel) {
            $this->executionModel = $executionModel;
        }
    }

    /**
     * @return bool
     */
    public function execute(): bool
    {
        foreach ($this->payload['files'] as $name => $path) {
            $file = new \SplFileObject($path);
            $this->executionModel->uploadFile($file);
            if (count($this->executionModel->getErrors())) {
                $this->payload['errors'] = $this->executionModel->getErrors();
            }
            unlink($file->getRealPath());
        }
        return true;
    }

    /**
     * @param string[] $files
     */
    public function setFiles(array $files)
    {
        $this->payload = ['files' => $files];
    }

    /**
     * @param array $payload
     */
    public function setPayload(array $payload): void
    {
        $this->payload = $payload;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::UPLOAD_CSV_COMMAND_NAME;
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