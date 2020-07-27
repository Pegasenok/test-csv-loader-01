<?php

namespace App\Parser;

use App\Dto\EntityHolder;
use Traversable;

interface FileParserInterface
{
    /**
     * @param \SplFileObject $file
     * @return Traversable|EntityHolder[]
     */
    public function streamParseFile(\SplFileObject $file): Traversable;
}