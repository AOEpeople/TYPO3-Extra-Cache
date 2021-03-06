<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2009 AOE media GmbH <dev@aoemedia.de>
 * All rights reserved
 *
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * test case for Tx_Extracache_Configuration_ExtensionManager
 * @package extracache_tests
 * @subpackage Configuration
 */
class Tx_Extracache_Configuration_ExtensionManagerTest extends Tx_Extracache_Tests_AbstractTestcase
{
    /**
     *
     * @var Tx_Extracache_Configuration_ExtensionManager
     */
    private $extensionManager;
    /**
     * original config of the extracache-Extension
     */
    private $originalExtConfig;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $this->originalExtConfig = $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['extracache'];
        $modifiedExtConfig = unserialize($this->originalExtConfig);
        $modifiedExtConfig['path_StaticFileCache'] = '/test_dir/dir2/';
        $modifiedExtConfig['developmentContext'] = 0;
        $modifiedExtConfig['supportFeUsergroups'] = 1;
        $modifiedExtConfig['enableContentProcessors'] = 1;
        $modifiedExtConfig['enableStaticCacheManager'] = 'StaticFileCacheManager';
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['extracache'] = serialize($modifiedExtConfig);

        /** @var Tx_Extracache_Configuration_TypoScriptConfiguration $typoScriptConfiguration */
        $typoScriptConfiguration = $this->getMockBuilder(Tx_Extracache_Configuration_TypoScriptConfiguration::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->extensionManager = new Tx_Extracache_Configuration_ExtensionManager();
        $this->extensionManager->injectTypoScriptConfiguration($typoScriptConfiguration);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['extracache'] = $this->originalExtConfig;
        unset($this->extensionManager);
    }

    /**
     * Test method areContentProcessorsEnabled
     * @test
     */
    public function areContentProcessorsEnabled()
    {
        $this->assertEquals($this->extensionManager->areContentProcessorsEnabled(), true);
    }

    /**
     * Test method get
     * @test
     */
    public function get()
    {
        $this->assertEquals($this->extensionManager->get('path_StaticFileCache'), '/test_dir/dir2/');
    }

    /**
     * Test method isDevelopmentContextSet
     * @test
     */
    public function isDevelopmentContextSet()
    {
        $this->assertEquals($this->extensionManager->isDevelopmentContextSet(), false);
    }

    /**
     * Test method isStaticCacheEnabled
     * @test
     */
    public function isStaticCacheEnabled()
    {
        $this->assertEquals($this->extensionManager->isStaticCacheEnabled(), true);
    }

    /**
     * Test method isSupportForFeUsergroupsSet
     * @test
     */
    public function isSupportForFeUsergroupsSet()
    {
        $this->assertEquals($this->extensionManager->isSupportForFeUsergroupsSet(), true);
    }
}