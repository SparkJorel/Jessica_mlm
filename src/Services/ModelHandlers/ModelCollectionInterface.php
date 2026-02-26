<?php

namespace App\Services\ModelHandlers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

interface ModelCollectionInterface
{
    public function saveCollection(Request $request): Response;
}
