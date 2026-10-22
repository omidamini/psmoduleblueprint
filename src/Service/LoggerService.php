<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
declare(strict_types=1);

namespace Blueprint\Module\Psmoduleblueprint\Service;

use Psr\Log\LoggerInterface;

class LoggerService
{
    private const PREFIX = '[Psmoduleblueprint] ';

    public function __construct(private LoggerInterface $logger)
    {
    }

    public function info(string $message): void
    {
        $this->logger->info($this->format($message));
    }

    public function error(string $message): void
    {
        $this->logger->error($this->format($message));
    }

    public function warning(string $message): void
    {
        $this->logger->warning($this->format($message));
    }

    public function debug(string $message): void
    {
        $this->logger->debug($this->format($message));
    }

    public function critical(string $message): void
    {
        $this->logger->critical($this->format($message));
    }

    private function format(string $message): string
    {
        return self::PREFIX . str_replace(["\r", "\n"], ' ', $message);
    }
}
