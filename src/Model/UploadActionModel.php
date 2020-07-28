<?php


namespace App\Model;


use App\Command\CommandDeployerInterface;
use App\Command\CommandPoolException;
use App\Command\UploadCsvCommand;
use App\Dto\CsvFileRequesetDto;
use App\Dto\CsvFileResponseDto;
use App\Exception\BadFileException;
use App\Model\FileConductor\FileConductorInterface;

class UploadActionModel
{
    private CommandDeployerInterface $commandDeployer;
    /**
     * @var FileConductorInterface
     */
    private FileConductorInterface $fileConductor;

    /**
     * UploadCsvFilesModel constructor.
     * @param CommandDeployerInterface $commandDeployer
     * @param FileConductorInterface $fileConductor
     */
    public function __construct(
        CommandDeployerInterface $commandDeployer,
        FileConductorInterface $fileConductor
    ) {
        $this->commandDeployer = $commandDeployer;
        $this->fileConductor = $fileConductor;
    }

    /**
     * @param CommandDeployerInterface $commandDeployer
     */
    public function setCommandDeployer(CommandDeployerInterface $commandDeployer): void
    {
        $this->commandDeployer = $commandDeployer;
    }

    /**
     * @param CsvFileRequesetDto $requesetDto
     * @return CsvFileResponseDto
     * @throws BadFileException
     */
    public function upload(CsvFileRequesetDto $requesetDto): CsvFileResponseDto
    {
        $command = new UploadCsvCommand();
        $files = [];
        foreach ($requesetDto->getFiles() as $key => $file) {
            if (!$this->fileConductor->verifyFileUpload($file)) {
                throw new BadFileException('Corrupted files. Please, try again.');
            }
            $destination = $this->fileConductor->transferFileFurther($file);
            $files[$key] = $destination;
        }
        $command->setFiles($files);
        try {
            $commandId = $this->commandDeployer->deployCommand($command);
        } catch (CommandPoolException $e) {
            return new CsvFileResponseDto(CsvFileResponseDto::FAILURE);
        }
        return new CsvFileResponseDto(CsvFileResponseDto::SUCCESS, $commandId->getId());
    }
}