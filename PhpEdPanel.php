<?php
/**
 * PhpEd panel.
 * Allows switch off/on a PhpEd debugger
 *
 * @author jasir
 * @license WTFPL http://en.wikipedia.org/wiki/WTFPL
  */
namespace Extras\Debug;

use \Nette\Object;
use Nette\Diagnostics\IBarPanel;
use \Nette\Environment;
use Nette\Diagnostics\Debugger;
use Nette\Utils\Html;
use Nette\Application\Responses\TextResponse;

class PhpEdPanel extends Object implements IBarPanel {

	static public $defaultSESSID = 1;

	static private $registered = FALSE;

	public static function register() {

		//register panel only once
		if (!self::$registered) {
			Debugger::addPanel(new self);
			self::$registered = TRUE;
		}
	}

	/**
	 * Renders HTML code for custom tab.
	 * @return string
	 * @see IDebugPanel::getTab()
	 */
	public function getTab() {
		$defaultSESSID = self::$defaultSESSID;
		$s = <<<EOF
<span style="cursor:pointer;" onclick="phpedpanel.switchMode();return false;">
<img src="data:image/gif;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAMAAAAoLQ9TAAAArlBMVEX///
8dYnkdY3kdY3gfZXkfZHggZXkiZnohZ3kkaHonansna3oqbXwqbXsrbnwsb3stcHw4VlMxTkogOTItTkU4Vk4xVkoo
QTkxTkUoSD0oRTktSD0jPjIjQTItTj0uaiI6fCtTjTySwX9zp1iWw3mVwnmfx4GmyYeuzpGvzpG10pnb68y20pm40p
y40p3G3K7V58Lb6szn89vH3K/G267H26/L37TV5sLH266xrrEE7PJGAAAAAXRSTlMAQObYZgAAAJBJREFUeNptT4s
OgjAQO3zjczCcMBV8gjoVRUX7/z8mamYm8ZIm1+Z6aYn+zrAsuAUcg2MMIl8z4XoBY5AwDlh2Y4wRH2mlf4nUPR0A
oXg9cwi9c6byY1e7fNk+qcV1n3S0ZWKn+WO5jW0efgSJ1mG3ma+aeCci8lBrJPF6VtdJCnCrWrHM8FPgt01Qaie+2xNk0Qw09mh70AAAAABJRU5ErkJggg=="
><span id="phpedpaneltext">Off</span>
</span>
<script type="text/javascript">
/* <![CDATA[ */
(function() {
	phpedpanel = {
		setCookie : function (name, value) {
			document.cookie = name + "=" + value + "; path=/";
		 },

		getCookie : function (name) {
			var nameEQ = name + "=";
			var ca = document.cookie.split(';');
			for(var i=0;i < ca.length;i++) {
				var c = ca[i];
				while (c.charAt(0)==' ') c = c.substring(1,c.length);
				if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
			}
			return '';
		 },

		switchMode: function () {
			if (phpedpanel.isActive()) {
				phpedpanel.setCookie('DBGSESSID_OLD', phpedpanel.getCookie('DBGSESSID'));
				phpedpanel.setCookie('DBGSESSID', '');
			} else {
				phpedpanel.setCookie('DBGSESSID', phpedpanel.getCookie('DBGSESSID_OLD') != '' ? phpedpanel.getCookie('DBGSESSID_OLD'): {$defaultSESSID})
			}
			phpedpanel.redraw();
		},

		isActive: function () {
			return phpedpanel.getCookie('DBGSESSID') != '' &&  phpedpanel.getCookie('DBGSESSID') != -1;
		},

		redraw: function() {
			var $ = Nette.Query.factory;
			d = $('#phpedpaneltext').dom();

			if (!phpedpanel.isActive()) {
				d.style.color = "#888";
				d.innerHTML = "Off";
				d.title = "PhpED Debugger is inactive. Click to activate.";
				d.style.fontWeight = "normal";
			} else {
				d.style.color = "green";
				d.style.fontWeight = "bold";
				d.innerHTML = "On";
				d.title = "PhpED Debugger is active. Successive server requests will be controled by debugger. Click to deactivate.";
			}
		}
	}
	phpedpanel.redraw();
})();
/* ]]> */
</script>
EOF;
		return $s;
	}

	/**
	 * Renders HTML code for custom panel.
	 * @return string
	 * @see IDebugPanel::getPanel()
	 */
	public function getPanel() {
		return FALSE;
	}

	/**
	 * Returns panel ID.
	 * @return string
	 * @see IDebugPanel::getId()
	 */
	public function getId() {
		return __CLASS__;
	}

	/**
	 * Determines if PhpED debugger is active
	 * see http://forum.nusphere.com/code-executed-only-when-testing-in-the-debugger-t5546.html
	 * @returns boolean
	 */
	public static function IsDebuggerActive() {
		return(
			(isset($_GET['DBGSESSID']) && (int)$_GET['DBGSESSID'] >= 0) ||
			(isset($_ENV['DBGSESSID']) && (int)$_ENV['DBGSESSID'] >= 0) ||
			(isset($_COOKIE['DBGSESSID']) && (int)$_COOKIE['DBGSESSID'] >= 0)
		);
	}
}
