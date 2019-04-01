<?php
namespace App\Model;

use Sx\Data\RepoInterface;
use Sx\Utility\LogInterface;

/**
 * Common class for all domain repositories.
 *
 * @package App\Model
 */
abstract class RepoAbstract implements RepoInterface
{
    /**
     * The logger to log backend errors to.
     *
     * @var LogInterface
     */
    protected $logger;

    /**
     * Sets the logger.
     *
     * @param LogInterface $logger
     */
    public function __construct(LogInterface $logger)
    {
        $this->logger = $logger;
    }
}
