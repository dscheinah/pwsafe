<?php
namespace App;

use Psr\Http\Server\MiddlewareInterface;
use Sx\Message\Response\HelperInterface;

/**
 * Base class for all app specific handlers and actions.
 *
 * @package App
 */
abstract class MiddlewareAbstract implements MiddlewareInterface
{
    /**
     * Each middleware needs to create responses. The helper is a request builder for the expected content type.
     *
     * @var HelperInterface
     */
    protected $helper;

    /**
     * Requires the response helper to given.
     *
     * @param HelperInterface $helper
     */
    public function __construct(HelperInterface $helper)
    {
        $this->helper = $helper;
    }
}
