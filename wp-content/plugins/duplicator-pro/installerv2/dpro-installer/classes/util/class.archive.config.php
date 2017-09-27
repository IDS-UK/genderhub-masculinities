<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if(!class_exists('DUPX_Archive_Config'))
{
	class DUPX_Archive_Config
	{
		const Config_Filename = 'archive.cfg';

		public $created;
		public $version_dup;
		public $version_wp;
		public $version_db;
		public $version_php;
		public $version_os;
		
		//GENERAL
		public $secure_on;
		public $secure_pass;
		public $skipscan;
		public $package_name;
		public $package_notes;
		public $wp_tableprefix;
		public $blogname;
		
		//STEP1
		//BASIC DB
		public $dbhost;
		public $dbname;
		public $dbuser;
		public $dbpass;
		
		//CPANEL: Login
		public $cpnl_host;
		public $cpnl_user;
		public $cpnl_pass;
		public $cpnl_enable;
		public $cpnl_connect;
		
		//CPANEL: DB
		public $cpnl_dbaction;
		public $cpnl_dbhost;
		public $cpnl_dbname;
		public $cpnl_dbuser;
		
		//ADV OPTS
		public $ssl_admin;
		public $ssl_login;
		public $cache_wp;
		public $cache_path;
		public $wproot;
		public $url_old;
		public $url_new;
		public $mu_mode;
		public $opts_delete;
		
		//put your code here
		public static function get_instance()
		{
			$instance = null;
			
			$config_filepath = dirname(__FILE__) . '/' . self::Config_Filename;
			
			if(file_exists($config_filepath))
			{
				$instance = new DUPX_Archive_Config();
				
				// RSR TODO
			}

			return $instance;
		}
		
		public static function init()
		{
			$ret = false;
			
			$ac = DUPX_Archive_Config::get_instance();

			if($ac != null)
			{
				//COMPARE VALUES
				$GLOBALS['FW_CREATED'] = $ac->created; // '%fwrite_created%';
				$GLOBALS['FW_VERSION_DUP'] = $ac->version_dup; // '%fwrite_version_dup%';
				$GLOBALS['FW_VERSION_WP'] = $ac->version_wp; // '%fwrite_version_wp%';
				$GLOBALS['FW_VERSION_DB'] = $ac->version_db; // '%fwrite_version_db%';
				$GLOBALS['FW_VERSION_PHP'] = $ac->version_php; // '%fwrite_version_php%';
				$GLOBALS['FW_VERSION_OS'] = $ac->version_os; //'%fwrite_version_os%';
				//GENERAL
				$GLOBALS['FW_SECUREON'] = $ac->secure_on;// '%fwrite_secure_on%';
				$GLOBALS['FW_SECUREPASS'] = $ac->secure_pass;// '%fwrite_secure_pass%';
				$GLOBALS['FW_SKIPSCAN'] = $ac->skipscan; // '%fwrite_skipscan%';
				$GLOBALS['FW_PACKAGE_NAME'] = $ac->package_name; // '%fwrite_package_name%';
				$GLOBALS['FW_PACKAGE_NOTES'] = $ac->package_notes; // '%fwrite_package_notes%';
				$GLOBALS['FW_TABLEPREFIX'] = $ac->wp_tableprefix; // '%fwrite_wp_tableprefix%';
				$GLOBALS['FW_BLOGNAME'] = $ac->blogname; // '%fwrite_blogname%';
				$GLOBALS['FW_USECDN'] = false;
				$GLOBALS['HOST_NAME'] = strlen($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST'];
				$GLOBALS['DBSAFE_BLOGNAME'] = preg_replace("/[^A-Za-z0-9?!]/", '', $GLOBALS['FW_BLOGNAME']);

				//STEP1
				//BASIC DB
				$GLOBALS['FW_DBHOST'] = $ac->dbhost; // '%fwrite_dbhost%';
				$GLOBALS['FW_DBHOST'] = empty($GLOBALS['FW_DBHOST']) ? 'localhost' : $GLOBALS['FW_DBHOST'];
				$GLOBALS['FW_DBNAME'] = $ac->dbname; // '%fwrite_dbname%';
				$GLOBALS['FW_DBUSER'] = $ac->dbuser; // '%fwrite_dbuser%';
				$GLOBALS['FW_DBPASS'] = $ac->dbpass; // '%fwrite_dbpass%';
				//CPANEL: Login
				$GLOBALS['FW_CPNL_HOST'] = $ac->cpnl_host;// '%fwrite_cpnl_host%';
				$GLOBALS['FW_CPNL_HOST'] = empty($GLOBALS['FW_CPNL_HOST']) ? "https://{$GLOBALS['HOST_NAME']}:2083" : $GLOBALS['FW_CPNL_HOST'];
				$GLOBALS['FW_CPNL_USER'] = $ac->cpnl_user; // '%fwrite_cpnl_user%';
				$GLOBALS['FW_CPNL_PASS'] = $ac->cpnl_pass; // '%fwrite_cpnl_pass%';
				$GLOBALS['FW_CPNL_ENABLE'] = $ac->cpnl_enable; // '%fwrite_cpnl_enable%';
				$GLOBALS['FW_CPNL_CONNECT'] = $ac->cpnl_connect; // '%fwrite_cpnl_connect%';
				//CPANEL: DB
				$GLOBALS['FW_CPNL_DBACTION'] = $ac->cpnl_dbaction; // '%fwrite_cpnl_dbaction%';
				$GLOBALS['FW_CPNL_DBHOST'] = $ac->cpnl_dbhost; // '%fwrite_cpnl_dbhost%';
				$GLOBALS['FW_CPNL_DBHOST'] = empty($GLOBALS['FW_CPNL_DBHOST']) ? 'localhost' : $GLOBALS['FW_CPNL_DBHOST'];
				$GLOBALS['FW_CPNL_DBNAME'] = strlen($ac->cpnl_dbname /*'%fwrite_cpnl_dbname%'*/) ? $ac->cpnl_dbname /*'%fwrite_cpnl_dbname%'*/ : '';
				$GLOBALS['FW_CPNL_DBUSER'] = $ac->cpnl_dbuser /* '%fwrite_cpnl_dbuser%' */;
				
				//ADV OPTS
				$GLOBALS['FW_SSL_ADMIN'] = $ac->ssl_admin; // '%fwrite_ssl_admin%';
				$GLOBALS['FW_SSL_LOGIN'] = $ac->ssl_login; // '%fwrite_ssl_login%';
				$GLOBALS['FW_CACHE_WP'] = $ac->cache_wp; // '%fwrite_cache_wp%';
				$GLOBALS['FW_CACHE_PATH'] = $ac->cache_path; // '%fwrite_cache_path%';
				$GLOBALS['FW_WPROOT'] = $ac->wproot; // '%fwrite_wproot%';
				$GLOBALS['FW_URL_OLD'] = $ac->url_old; // '%fwrite_url_old%';
				$GLOBALS['FW_URL_NEW'] = $ac->url_new; // '%fwrite_url_new%';
				$GLOBALS['MU_MODE'] = $ac->mu_mode; // '%mu_mode%';
				$GLOBALS['FW_OPTS_DELETE'] = json_decode($ac->opts_delete /*"%fwrite_opts_delete%"*/, true);
				
				$ret = true;
			}
			
			return $ret;
		}
	}
}