<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (!class_exists('DUPX_Constants'))
{
	class DUPX_Constants
	{		
		public static function init()
		{
			$GLOBALS['BOOTLOADER_NAME'] = isset($_GET['bootloader'])  ? $_GET['bootloader'] : null ;
			$GLOBALS['FW_PACKAGE_NAME'] = isset($_GET['archive'])     ? $_GET['archive']    : null; // '%fwrite_package_name%';
			$GLOBALS['FW_PACKAGE_PATH'] = dirname(__FILE__) . '/../../../' . $GLOBALS['FW_PACKAGE_NAME']; // '%fwrite_package_name%';
				//
			//DATABASE SETUP: all time in seconds	
			@ini_set('mysql.connect_timeout', '5000');
			$GLOBALS['DB_MAX_TIME'] = 5000;
			$GLOBALS['DB_MAX_PACKETS'] = 268435456;
			$GLOBALS['DBCHARSET_DEFAULT'] = 'utf8';
			$GLOBALS['DBCOLLATE_DEFAULT'] = 'utf8_general_ci';
			$GLOBALS['DB_RENAME_PREFIX'] = 'x-bak__';

			//UPDATE TABLE SETTINGS
			$GLOBALS['REPLACE_LIST'] = array();
			$GLOBALS['DEBUG_JS'] = false;

			//PHP SETUP: all time in seconds
			@ini_set('memory_limit', '5000M');
			@ini_set("max_execution_time", '5000');
			@ini_set("max_input_time", '5000');
			@ini_set('default_socket_timeout', '5000');
			@set_time_limit(0);
			/* ================================================================================================
			  END ADVANCED FEATURES: Do not edit below here.
			  =================================================================================================== */

			//CONSTANTS
			define("DUPLICATOR_PRO_INIT", 1);
			define("DUPLICATOR_PRO_SSDIR_NAME", 'wp-snapshots-dup-pro');  //This should match DUPLICATOR_PRO_SSDIR_NAME in duplicator.php
			
			//SHARED POST PARMS
			$_GET['debug'] = isset($_GET['debug']) ? true : false;
			$_GET['basic'] = isset($_GET['basic']) ? true : false;
			$_POST['view'] = isset($_POST['view']) ? $_POST['view'] : "secure";

			//GLOBALS
			$GLOBALS["VIEW"] = isset($_GET["view"]) ? $_GET["view"] : $_POST["view"];
			$GLOBALS["SQL_FILE_NAME"] = "installer-data.sql";
			$GLOBALS["LOG_FILE_NAME"] = "installer-log.txt";
			$GLOBALS['SEPERATOR1'] = str_repeat("********", 10);
			$GLOBALS['LOGGING'] = isset($_POST['logging']) ? $_POST['logging'] : 1;
			$GLOBALS['CURRENT_ROOT_PATH'] = realpath(dirname(__FILE__) . "/../../../");			
			$GLOBALS["LOG_FILE_PATH"] = $GLOBALS['CURRENT_ROOT_PATH'] . '/' . $GLOBALS["LOG_FILE_NAME"];
			$GLOBALS['CHOWN_ROOT_PATH'] = @chmod("{$GLOBALS['CURRENT_ROOT_PATH']}", 0755);
			$GLOBALS['CHOWN_LOG_PATH'] = @chmod("{$GLOBALS['CURRENT_ROOT_PATH']}/{$GLOBALS['LOG_FILE_NAME']}", 0644);
			$GLOBALS['URL_SSL'] = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on') ? true : false;
			$GLOBALS['URL_PATH'] = ($GLOBALS['URL_SSL']) ? "https://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}" : "http://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}";

			//Restart log if user starts from step 1
			if ($GLOBALS["VIEW"] == "deploy")
			{
				$GLOBALS['LOG_FILE_HANDLE'] = @fopen($GLOBALS['LOG_FILE_PATH'], "w+");
			}
			else
			{
				$GLOBALS['LOG_FILE_HANDLE'] = @fopen($GLOBALS['LOG_FILE_PATH'], "a+");
			}
			
			$GLOBALS['FW_USECDN'] = false;
			$GLOBALS['HOST_NAME'] = strlen($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST'];			
		}
	}
	
	DUPX_Constants::init();
}
