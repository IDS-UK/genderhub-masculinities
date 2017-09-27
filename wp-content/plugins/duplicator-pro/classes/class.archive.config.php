<?php
/*
  Duplicator Pro Plugin
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

//require_once(dirname(__FILE__) . '/../utilities/class.u.php');
//require_once(dirname(__FILE__) . '/../utilities/class.utility.php');
//require_once(DUP_PRO_U::$PLUGIN_DIRECTORY . '/classes/entities/class.json.entity.base.php');
//require_once(DUP_PRO_U::$PLUGIN_DIRECTORY . '/classes/entities/class.package.template.entity.php');
//require_once(DUP_PRO_U::$PLUGIN_DIRECTORY . '/classes/entities/class.system.global.entity.php');
//require_once(DUP_PRO_U::$PLUGIN_DIRECTORY . '/lib/cron/csd_parser.php');

if (!class_exists('DUP_PRO_Archive_Config'))
{
    /**
     * @copyright 2016 Snap Creek LLC
     */
    class DUP_PRO_Archive_Config
    {        
		public $created;
		public $version_dup;
		public $version_wp;
		public $version_db;
		public $version_php;
		public $version_os;
		public $secure_on;
		public $secure_pass;
		public $skipscan;
		public $wp_tableprefix;
		public $blogname;
		public $dbhost;
		public $dbname;
		public $dbuser;
		public $dbpass;
		public $cpnl_dbname;
		public $cpnl_host;
		public $cpnl_user;
		public $cpnl_pass;
		public $cpnl_enable;		
		public $cpnl_connect;
		public $cpnl_dbaction;
		public $cpnl_dbhost;
		public $cpnl_dbuser;
		public $ssl_admin;
		public $ssl_login;
		public $cache_wp;
		public $cache_path;
		public $wproot;
		public $url_old;
		public $url_new;
		public $mu_mode;
		public $opts_delete;
    }
}
?>
