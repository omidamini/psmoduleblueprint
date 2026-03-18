<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
declare(strict_types=1);
namespace Blueprint\Module\Psmoduleblueprint\Hook;
use Blueprint\Module\Psmoduleblueprint\Hook\Hook;
class DisplayCustomerAccountHook extends Hook
{
    /**
     * Execute the hook.
     *
     * @param array $params
     * @return bool
     */
    public function run()
    {
        return $this->display('my-account.tpl');
    }
}