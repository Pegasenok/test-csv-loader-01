<?php


namespace App\Model\FileConductor;


class UnsecuredFileConductor implements FileConductorInterface
{
    public function verifyFileUpload(\SplFileObject $file)
    {
        return true;
    }

    public function transferFileFurther(\SplFileObject $file)
    {
        $destination = $this->getDestinationPath();
        $this->moveFile($file, $destination);
        $this->allowAccessToWorkers($destination);
        return $destination;
    }

    /**
     * @param string $destination
     */
    private function allowAccessToWorkers(string $destination): void
    {

    }

    /**
     * @param \SplFileObject $file
     * @param string $destination
     */
    private function moveFile(\SplFileObject $file, string $destination): void
    {
        unlink($file->getRealPath());
    }

    /**
     * @return string
     */
    private function getDestinationPath(): string
    {
        return '/var/uploads/' . uniqid('file_');
    }
}