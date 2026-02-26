<?php

namespace App\AbstractModel;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface EntityWithImageToUploadInterface
{
    public function getSpecificDirectory(string $baseDirectory, string $type = null): string;

    public function getFile(string $type = null): ?UploadedFile;
}
