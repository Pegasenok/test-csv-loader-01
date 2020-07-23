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

    public static function stuff(array $commandArray): self
    {
        $command = new UploadCsvCommand();
        $command->id = new CommandId($commandArray['id']);
        $command->payload = $commandArray['payload'];
        return $command;
    }

    /**
     * @param UserLoadingModel $executionModel
     * @return bool
     */
    public function execute(UserLoadingModel $executionModel): bool
    {
        foreach ($this->payload['files'] as $name => $path) {
            $file = new \SplFileObject($path);
            $executionModel->uploadFile($file);
            if (count($executionModel->getErrors())) {
                var_dump($executionModel->getErrors());
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