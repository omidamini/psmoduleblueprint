<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
declare(strict_types=1);

namespace Blueprint\Module\Psmoduleblueprint\Config;

final class ModuleMessage
{
    private function __construct()
    {
    }

    public const SAVE_SUCCESS = 'Configuration saved successfully.';
    public const DELETE_SUCCESS = 'Configuration deleted successfully.';
    public const NOT_FOUND = 'Configuration not found.';
    public const DUPLICATE = 'Configuration already exists.';
    public const ERROR = 'An error occurred while processing your request.';
}
