<?php
namespace App\Handler;

use App\MiddlewareAbstract;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\HelperInterface;
use Sx\Utility\LogInterface;

/**
 * The error handler to create error responses out of exceptions.
 *
 * @package App\Handler
 */
class ErrorHandler extends MiddlewareAbstract
{
    /**
     * The logger to write errors to.
     *
     * @var LogInterface
     */
    private $logger;

    /**
     * The env from config to check if to output the original message and trace.
     *
     * @var string
     */
    private $env;

    /**
     * Create the error handler with helper and env from config.
     *
     * @param HelperInterface $helper
     * @param LogInterface    $logger
     * @param string          $env
     */
    public function __construct(HelperInterface $helper, LogInterface $logger, string $env)
    {
        parent::__construct($helper);
        $this->logger = $logger;
        $this->env = $env;
    }

    /**
     * Wraps the call to the next handler in a try/ catch block and creates error responses.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (\Exception $e) {
            $this->logger->log($e->getMessage());
            // Do not output any internal information in production environment.
            if ($this->env === 'production') {
                return $this->helper->create(500);
            }
            return $this->helper->create(
                500,
                [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'trace' => $e->getTrace(),
                ]
            );
        }
    }
}
