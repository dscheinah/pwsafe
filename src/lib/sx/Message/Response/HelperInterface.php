<?php
namespace Sx\Message\Response;

use Psr\Http\Message\ResponseInterface;

interface HelperInterface
{

    public function create(int $code, $response): ResponseInterface;
}
