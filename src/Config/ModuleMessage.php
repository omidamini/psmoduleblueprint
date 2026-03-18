<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
namespace Blueprint\Module\Psmoduleblueprint\Config;

final class ModuleMessage
{
    private function __construct()
    {
        // Empêche l'instanciation
    }
    public const SAVE_SUCCESS = 'Configuration saved successfully.';
    public const DELETE_SUCCESS = 'Configuration deleted successfully.';
    public const NOT_FOUND = 'Configuration not found.';
    public const DUPLICATE = 'Configuration already exists.';
    public const MAIL_SEND_SUCCESS = 'Email sent successfully.';
    public const MAIL_SEND_ERROR = 'Error sending email.';
    public const ERROR = 'An error occurred while processing your request.';
    public const MESSAGE_INVALID_DATE = 'Invalid date provided.';
    public const MESSAGE_START_DATE_GREATER_THAN_END_DATE = 'Start date cannot be greater than end date.';
}