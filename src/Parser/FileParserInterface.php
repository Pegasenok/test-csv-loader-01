<?php

namespace App\Parser;

use App\Dto\EntityHolder;

interface FileParserInterface
{
    /**
     * @param \SplFileObject $file
     * @return \Generator|EntityHolder[]
     */
    public function streamParseFile(\SplFileObject $file): \Generator;
}