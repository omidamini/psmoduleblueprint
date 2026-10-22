<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
declare(strict_types=1);

namespace Blueprint\Module\Psmoduleblueprint\Service;

use Tools;

/**
 * Sanitizes HTML with the PrestaShop purifier configuration.
 */
final class HtmlSanitizer
{
    public function purify(string $input): string
    {
        return Tools::purifyHTML($input);
    }
}
