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
 * Controller for Cache Management
 * @package extracache
 */
class Tx_Extracache_View_View {
	/**
	 * @var string
	 */
	private $templatePath;
	
	/**
	 * @var array
	 */
	private $viewData = array ();
	/**
	 * @param string $key
	 * @param mixed $value
	 */
	public function assign($key, $value) {
		$this->viewData [$key] = $value;
	}
	/**
	 * @param string $template
	 * @return string
	 */
	public function render($template) {
		ob_start ();
		global $view_data;
		$view_data = $this->viewData;
		include $this->getTemplatePath () . $template . '.php';
		$content = ob_get_contents ();
		ob_end_clean ();
		return $content;
	}
	/**
	 * @return string
	 */
	public function getTemplatePath() {
		return $this->templatePath;
	}
	
	/**
	 * @param string $templatePath
	 */
	public function setTemplatePath($templatePath) {
		$this->templatePath = $templatePath;
	}

}