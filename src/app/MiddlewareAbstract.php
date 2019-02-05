<?php
namespace App;

use Psr\Http\Server\MiddlewareInterface;
use Sx\Message\Response\HelperInterface;

abstract class MiddlewareAbstract implements MiddlewareInterface
{

    protected $helper;

    public function __construct(HelperInterface $helper)
    {
        $this->helper = $helper;
    }
}
