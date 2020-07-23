<?php


namespace App\Model;


use App\Command\CommandDeployerInterface;
use App\Command\CommandPoolException;
use App\Command\UploadCsvCommand;
use App\Dto\CsvFileRequesetDto;
use App\Dto\CsvFileResponseDto;

class UploadActionModel
{
    private CommandDeployerInterface $commandDeployer;

    /**
     * UploadCsvFilesModel constructor.
     * @param CommandDeployerInterface $commandDeployer
     */
    public function __construct(CommandDeployerInterface $commandDeployer)
    {
        $this->commandDeployer = $commandDeployer;
    }

    /**
     * @param CsvFileRequesetDto $requesetDto
     * @return CsvFileResponseDto
     */
    public function upload(CsvFileRequesetDto $requesetDto): CsvFileResponseDto
    {
        $command = new UploadCsvCommand();
        $command->setFiles($requesetDto->getFiles());
        try {
            $commandId = $this->commandDeployer->deployCommand($command);
        } catch (CommandPoolException $e) {
            return new CsvFileResponseDto(CsvFileResponseDto::FAILURE);
        }
        return new CsvFileResponseDto(CsvFileResponseDto::SUCCESS, $commandId->getId());
    }
}