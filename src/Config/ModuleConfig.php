<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 * @copyright 2026 ITIS COMMERCE (https://www.itis-commerce.com/)
 */
namespace Blueprint\Module\Psmoduleblueprint\Config;

final class ModuleConfig
{
    private function __construct()
    {
        // Empêche l'instanciation
    }

    public const MODULE_NAME = 'psmoduleblueprint';
    public const MODULE_NAME_SPACE = 'Blueprint\Module\Psmoduleblueprint';
    public const MODULE_DIR = _PS_MODULE_DIR_ . self::MODULE_NAME . '/';
    public const MODULE_URL = _PS_BASE_URL_ . __PS_BASE_URI__ . 'modules/' . self::MODULE_NAME . '/';
    public const TEMPLATE_DIR = self::MODULE_DIR . 'views/templates/';
    public const IMG_DIR = self::MODULE_DIR . 'views/img/';
    public const IMG_URL = self::MODULE_URL . 'views/img/';
    public const MAIL_DIR = self::MODULE_DIR .'mails/';
}