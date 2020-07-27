<?php


namespace App\Model\FileConductor;


class FileConductor implements FileConductorInterface
{
    public function verifyFileUpload(\SplFileObject $file)
    {
        return is_uploaded_file($file->getRealPath());
    }

    /**
     * @param \SplFileObject $file
     * @return string
     */
    public function transferFileFurther(\SplFileObject $file): string
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
        chmod($destination, 0777);
    }

    /**
     * @param \SplFileObject $file
     * @param string $destination
     */
    private function moveFile(\SplFileObject $file, string $destination): void
    {
        move_uploaded_file($file->getRealPath(), $destination);
    }

    /**
     * @return string
     */
    private function getDestinationPath(): string
    {
        return '/var/uploads/' . uniqid('file_');
    }
}