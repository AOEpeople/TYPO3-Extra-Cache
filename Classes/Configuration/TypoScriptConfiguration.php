<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 AOE GmbH <dev@aoe.com>
 *  All rights reserved
 *
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * @package extracache
 */
class Tx_Extracache_Configuration_TypoScriptConfiguration
{
    /**
     * @var array
     */
    private $configuration = array();

    /**
     * @param \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $typoScriptFrontendController
     */
    public function __construct(
        \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $typoScriptFrontendController
    ) {
        if (isset($typoScriptFrontendController->tmpl->setup['config.']) &&
            isset($typoScriptFrontendController->tmpl->setup['config.']['tx_extracache.'])
        ) {
            $this->configuration = $typoScriptFrontendController->tmpl->setup['config.']['tx_extracache.'];
        }
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return isset($this->configuration[$key]);

    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        if ($this->has($key)) {
            return $this->configuration[$key];
        }
        throw new \InvalidArgumentException(sprintf('configuration "%s" not exists', $key), 1469792900);
    }
}
