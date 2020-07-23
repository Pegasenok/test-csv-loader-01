<?php


namespace App\Command;


use App\Builder\UserBuilder;
use App\Database\DatabaseStorage;
use App\Model\UserLoadingModel;
use App\Parser\CsvFileParser;
use App\Repository\UserRepository;
use App\Validation\UserValidation;

class CommandRegistry
{
    public function getCommandFromArray($array)
    {
        if ($array['name'] == UploadCsvCommand::UPLOAD_CSV_COMMAND_NAME) {
            $model = new UserLoadingModel(
                new UserRepository(new DatabaseStorage($_ENV['DATABASE_URL'])),
                new CsvFileParser(new UserBuilder(new UserValidation()))
            );
            $command = new UploadCsvCommand($model);
            $command->setCommandId(new CommandId($array['id']));
            $command->setPayload($array['payload']);

            return $command;
        }

        throw new \Exception(sprintf("Command %s not found.", $array['name']??'-'));
    }
}