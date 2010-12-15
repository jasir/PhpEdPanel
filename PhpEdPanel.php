<?php
/**
 * PhpEd panel.
 * Allows switch off/on a PhpEd debugger
 *
 * @author jasir
 * @license Do what you want
  */
namespace Extras\Debug;

use \Nette\Object;
use \Nette\IDebugPanel;
use \Nette\Environment;
use \Nette\Debug;
use \Nette\Web\Html;
use \Nette\Application\RenderResponse;

class PhpEdPanel extends Object implements IDebugPanel {

	static public $defaultSESSID = 1;

	static private $instance;

	public static function register() {

		//register panel only once
		if (!self::$instance) {
			self::$instance = new static;
			\Nette\Debug::addPanel(self::$instance);
		}
	}

	/**
	 * Renders HTML code for custom tab.
	 * @return string
	 * @see IDebugPanel::getTab()
	 */
	public function getTab() {

		$link = $_SERVER["REQUEST_URI"];

		$dbgIsOn = FALSE;
		if (preg_match('/DBGSESSID=(?P<SESSID>-{0,1}[\d]+)/s', $link, $matches)) {
			if ($matches['SESSID'] !== '-1') {
				$dbgIsOn = TRUE;
			}
		} else {
			if (strpos($link, '?') > 0) {
				$link .= "&DBGSESSID=". self::$defaultSESSID;
			} else {
				$link .= "?DBGSESSID=". self::$defaultSESSID;
			}
		}

		if ($dbgIsOn === FALSE) {
			$title = 'PhpEd Debugger is inactive. Click to switch it on.';
			$text  = 'Off';
			$style = 'color:gray;';
			$sessId = self::$defaultSESSID;
			$script = '';
		} else {
			$title = 'PhpEd Debugger is active. Click to switch it off.';
			$text  = 'On';
			$style = 'color:green;font-weight:bold;';
			$sessId = -1;

		}
		$script = "document.cookie = 'DBGSESSID=$sessId; expires=Thu, 2 Aug 2050 20:47:11 UTC; path=/'";
		$link = preg_replace("/DBGSESSID=(-{0,1}[\d]+)/s", "DBGSESSID=$sessId", $link);

		$s = "<span style=\"cursor:pointer;{$style}\"onclick=\"$script;window.location='{$link}';\"title=\"{$title}\"}><img src=\"data:image/gif;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAMAAAAoLQ9TAAAArlBMVEX///8dYnkdY3kdY3gfZXkfZHggZXkiZnohZ3kkaHonansna3oqbXwqbXsrbnwsb3stcHw4VlMxTkogOTItTkU4Vk4xVkooQTkxTkUoSD0oRTktSD0jPjIjQTItTj0uaiI6fCtTjTySwX9zp1iWw3mVwnmfx4GmyYeuzpGvzpG10pnb68y20pm40py40p3G3K7V58Lb6szn89vH3K/G267H26/L37TV5sLH266xrrEE7PJGAAAAAXRSTlMAQObYZgAAAJBJREFUeNptT4sOgjAQO3zjczCcMBV8gjoVRUX7/z8mamYm8ZIm1+Z6aYn+zrAsuAUcg2MMIl8z4XoBY5AwDlh2Y4wRH2mlf4nUPR0AoXg9cwi9c6byY1e7fNk+qcV1n3S0ZWKn+WO5jW0efgSJ1mG3ma+aeCci8lBrJPF6VtdJCnCrWrHM8FPgt01Qaie+2xNk0Qw09mh70AAAAABJRU5ErkJggg==\">$text</span>";
		return $s;
	}

	/**
	 * Renders HTML code for custom panel.
	 * @return string
	 * @see IDebugPanel::getPanel()
	 */
	public function getPanel() {}

	/**
	 * Returns panel ID.
	 * @return string
	 * @see IDebugPanel::getId()
	 */
	public function getId() {
		return __CLASS__;
	}
}