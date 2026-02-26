<?php

namespace App\Services\ModelHandlers;

use App\AbstractModel\EntityWithImageToUploadInterface;
use App\Services\FileUploader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Common features needed in some Handlers
 *
 * @author Patrick GUENGANG <patrickguengang@gmail.com>
 *
 * @property ParameterBagInterface $parameterBag
 * @property FileUploader $fileUploader
 *
 * Trait TraitHandlers
 * @package App\Services\ModelHandlers
 */
trait TraitHandlers
{
    /**
     * @param EntityWithImageToUploadInterface $entity
     * @param string $type
     * @return bool|string
     */
    protected function getFileName(EntityWithImageToUploadInterface &$entity, string $type = "image")
    {
        if (null !== $file = $entity->getFile($type)) {
            if ($type === 'image') {
                $directory = $entity
                    ->getSpecificDirectory(
                        $this
                            ->parameterBag
                            ->get('upload_document_base_directory')
                    );
            } else {
                $directory = $entity
                    ->getSpecificDirectory(
                        $this
                            ->parameterBag
                            ->get('upload_document_base_directory'),
                        'video'
                    );
            }

            return  $this->fileUploader->upload($file, $directory);
        }

        return false;
    }
}
