<?php


namespace App\Command;


use App\Builder\UserBuilder;
use App\Database\DatabaseStorage;
use App\Model\BatchLoadingModel;
use App\Model\UserLoadingModel;
use App\Parser\CsvFileParser;
use App\Repository\UserRepository;
use App\Validation\UserValidation;

class CommandRegistry
{
    public function getCommandFromArray($array): CommandInterface
    {
        if ($array['name'] == UploadCsvCommand::UPLOAD_CSV_COMMAND_NAME) {
            $model = new UserLoadingModel(
                new CsvFileParser(new UserBuilder(new UserValidation())), new BatchLoadingModel(new UserRepository(new DatabaseStorage($_ENV['DATABASE_URL'])))
            );
            $command = new UploadCsvCommand($model);
            $command->setCommandId(new CommandId($array['id']));
            $command->setPayload($array['payload']);

            return $command;
        }

        throw new \Exception(sprintf("Command %s not found.", $array['name']??'-'));
    }
}