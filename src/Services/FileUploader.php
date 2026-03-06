<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\AsciiSlugger;

class FileUploader
{
    public function upload(UploadedFile $file, string $directory)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $slugger = new AsciiSlugger();
        $safeFilename = strtolower($slugger->slug($originalFilename));
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        $file->move($directory, $fileName);

        return $fileName;
    }
}
