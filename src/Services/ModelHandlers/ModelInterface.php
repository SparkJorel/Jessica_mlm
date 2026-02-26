<?php

namespace App\Services\ModelHandlers;

use Symfony\Component\HttpFoundation\Request;
use App\AbstractModel\EntityInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

interface ModelInterface
{
    public function save(Request $request, ?bool $mode = false);

    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ?bool $mode = false);

    public function show();

    public function list();
}
