<?php
/*
  Duplicator Pro Website Installer
  Copyright (C) 2016, Snap Creek LLC
  website: snapcreek.com

  Duplicator Pro Plugin is distributed under the GNU General Public License, Version 3,
  June 2007. Copyright (C) 2007 Free Software Foundation, Inc., 51 Franklin
  St, Fifth Floor, Boston, MA 02110, USA

  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
  ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
  WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
  DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
  ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
  (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
  LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
  ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
  (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
  SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
date_default_timezone_set('UTC'); // Some machines don’t have this set so just do it here.

if(!isset($_GET['archive']))
{
	// RSR TODO: Fail gracefully
	echo "Archive parameter not specified";
	exit(1);
}

if(!isset($_GET['bootloader']))
{
	// RSR TODO: Fail gracefully
	echo "Bootloader parameter not specified";
	exit(1);
}

require_once(dirname(__FILE__) . '/main.download.php');
require_once(dirname(__FILE__) . '/classes/config/class.constants.php');
require_once(dirname(__FILE__) . '/classes/config/class.archive.config.php');

if(!DUPX_Archive_Config::init_config_globals())
{
	// RSR TODO: Fail 'gracefully'
	echo "Can't initialize config globals";
	exit(1);
}

require_once(dirname(__FILE__) . '/classes/util/class.u.php');
require_once(dirname(__FILE__) . '/classes/class.logging.php');
require_once(dirname(__FILE__) . '/classes/class.http.php');
require_once(dirname(__FILE__) . '/classes/class.server.php');
require_once(dirname(__FILE__) . '/classes/class.conf.srv.php');
require_once(dirname(__FILE__) . '/classes/class.conf.wp.php');
require_once(dirname(__FILE__) . '/classes/class.engine.php');
require_once(dirname(__FILE__) . '/classes/class.db.base.php');
require_once(dirname(__FILE__) . '/classes/class.db.mysqli.php');
require_once(dirname(__FILE__) . '/classes/class.db.pdo.php');

if(!chdir($GLOBALS['CURRENT_ROOT_PATH']))
{
	// RSR TODO: Can't change directories
	echo "Can't change to directory " . $GLOBALS['CURRENT_ROOT_PATH'];
	exit(1);
}

if (isset($_POST['view_action'])) 
{
    switch ($_POST['view_action']) {
		case "deploy" : 
			require_once(dirname(__FILE__) . '/ctrls/action.step1.php');
			break;
        case "update" : 
			require_once(dirname(__FILE__) . '/ctrls/action.step2.php');
			break;
    }
    @fclose($GLOBALS["LOG_FILE_HANDLE"]);
    die("");
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="robots" content="noindex,nofollow">
	<title>WordPress Duplicator Professional</title>
	<?php
		require_once(dirname(__FILE__) . '/assets/inc.libs.css.php');
		require_once(dirname(__FILE__) . '/assets/inc.css.php');
		require_once(dirname(__FILE__) . '/assets/inc.libs.js.php');
		require_once(dirname(__FILE__) . '/assets/inc.js.php');
	?>
</head>
<body>

<div id="content">
	
<!-- HEADER TEMPLATE: Common header on all steps -->
<table cellspacing="0" class="header-wizard">
	<tr>
		<td style="width:100%;">
			<div style="font-size:22px; padding:5px 0px 0px 0px">
				<!-- !!DO NOT CHANGE/EDIT OR REMOVE PRODUCT NAME!!
				If your interested in Private Label Rights please contact us at the URL below to discuss
				customizations to product labeling: https://snapcreek.com	-->
				&nbsp; Duplicator Pro - Installer
			</div>
		</td>
		<td style="white-space:nowrap; text-align:right">
			<?php if (! $_GET['basic']) :?>
				<select id="help-lnk">
				<option value="null"> - Resources -</option>
					<option value="?view=scan&archive=<?php echo $GLOBALS['FW_PACKAGE_NAME']?>&bootloader=<?php echo $GLOBALS['BOOTLOADER_NAME']?>&basic">&raquo; System Scan</option>
					<option value="?view=help&archive=<?php echo $GLOBALS['FW_PACKAGE_NAME']?>&bootloader=<?php echo $GLOBALS['BOOTLOADER_NAME']?>&basic">&raquo; Installer Help</option>
					<option value="https://snapcreek.com/support/docs/">&raquo; Online Help</option>
				</select>
			<?php endif; ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php if (in_array($GLOBALS["VIEW"], array('help', 'secure', 'scan'))) :?>
				<div style="margin:4px 0px 10px 15px;">	</div>
			<?php else : ?>
				<?php
					$step1CSS = 'active-step';
					$step2CSS = '';
					$step3CSS = '';
					if ($GLOBALS["VIEW"] == 'update') {
						$step1CSS =  "complete-step";							
						$step2CSS =  "active-step";							
					}
					if ($GLOBALS["VIEW"] == "test") {
						$step1CSS =  "complete-step";							
						$step2CSS =  "complete-step";							
						$step3CSS =  "active-step";
					}
				?>
				<div id="wiz">
					<div id="wiz-steps">
						<div class="<?php echo $step1CSS; ?>"><a><span>1</span> Deploy</a></div>
						<div class="<?php echo $step2CSS; ?>"><a><span>2</span> Update </a></div>
						<div class="<?php echo $step3CSS; ?>"><a><span>3</span> Test </a></div>
					</div>
				</div>
			<?php endif; ?>
		</td>
		<td class="wiz-dupx-version"> 
			version:	<?php echo $GLOBALS['FW_VERSION_DUP'] ?> &raquo; 
			<a href="?view=help&archive=<?php echo $GLOBALS['FW_PACKAGE_NAME']?>&bootloader=<?php echo $GLOBALS['BOOTLOADER_NAME']?>&basic" target="_blank">help</a>
		</td>
	</tr>
</table>	

<!-- =========================================
FORM DATA: User-Interface views -->
<div id="content-inner">
	<?php
		switch ($GLOBALS["VIEW"]) {			
			case "secure" : 
				require_once(dirname(__FILE__) . '/views/view.init1.php');
				break;
			
			case "scan"   : 
				require_once(dirname(__FILE__) . '/views/view.init2.php');
				break;
			
			case "deploy" : 
				require_once(dirname(__FILE__) . '/views/view.step1.php');
				break;
			
			case "update" :
				require_once(dirname(__FILE__) . '/views/view.step2.php');
				break;
			
			case "test"   : 
				require_once(dirname(__FILE__) . '/views/view.step3.php');
				break;

			case "help"   : 
				require_once(dirname(__FILE__) . '/views/view.help.php');
				break;

			default : 
				echo "Invalid View Requested";
		}
	?>
</div>
</div>
	
<?php if ($_GET['debug']) :?>
	<form id="form-debug" method="post" action="?debug=1">
		<input id="debug-view" type="hidden" name="view" />
		DEBUG MODE ON:	<hr size="1" />
		<a href="javascript:void(0)" onclick="DUPX.debugNavigate('secure')">[Password]</a> &nbsp; 
		<a href="javascript:void(0)" onclick="DUPX.debugNavigate('scan')">[Scanner]</a> &nbsp; 
		<a href="javascript:void(0)" onclick="DUPX.debugNavigate('deploy')">[Deploy - 1]</a> &nbsp; 
		<a href="javascript:void(0)" onclick="DUPX.debugNavigate('update')">[Update - 2]</a> &nbsp; 
		<a href="javascript:void(0)" onclick="DUPX.debugNavigate('test')">[Test - 3]</a> &nbsp; 
		<br/><br/>
		<a href="javascript:void(0)"  onclick="$('#debug-vars').toggle()"><b>PAGE VARIABLES</b></a>
		<pre id="debug-vars"><?php print_r($GLOBALS); ?></pre>
	</form>
	
	<script>
		DUPX.debugNavigate = function(view) 
		{
			$('#debug-view').val(view);
			$('#form-debug').submit();
		}
	</script>
<?php endif; ?>
	
	
<!-- Used for integrity check do not remove:
DUPLICATOR_PRO_INSTALLER_EOF -->
</body>
</html>
