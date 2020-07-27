<?php

namespace App\Model\FileConductor;

interface FileConductorInterface
{
    public function verifyFileUpload(\SplFileObject $file);

    public function transferFileFurther(\SplFileObject $file);
}