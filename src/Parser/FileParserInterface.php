<?php

namespace App\Parser;

use App\Dto\EntityHolder;

interface FileParserInterface
{
    /**
     * @param \SplFileObject $file
     * @return iterable|EntityHolder[]
     */
    public function streamParseFile(\SplFileObject $file): iterable;
}