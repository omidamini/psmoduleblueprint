<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
declare(strict_types=1);
namespace Blueprint\Module\Psmoduleblueprint\Service;
use Psr\Log\LoggerInterface;
class LoggerService {
    private $logger;
    private $prefix = '[Psmoduleblueprint] ';
    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }
    public function info($message) {
        $this->logger->info($this->prefix.$message);
    }
    public function error($message) {
        $this->logger->error($this->prefix.$message);
    }
    public function warning($message) {
        $this->logger->warning($this->prefix.$message);
    }
    public function debug($message) {
        $this->logger->debug($this->prefix.$message);
    }
    public function critical($message) {
        $this->logger->critical($this->prefix.$message);
    }
}