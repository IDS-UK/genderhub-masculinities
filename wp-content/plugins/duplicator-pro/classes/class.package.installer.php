<?php
if (!defined('DUPLICATOR_PRO_VERSION'))
	exit; // Exit if accessed directly

require_once (DUPLICATOR_PRO_PLUGIN_PATH . 'classes/entities/class.system.global.entity.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH . 'classes/utilities/class.u.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH . 'classes/utilities/class.utility.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH . 'classes/utilities/class.shell.u.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH . 'classes/class.archive.config.php');

class DUP_PRO_Installer
{
	public $File;
	public $Size = 0;
	//SETUP
	public $OptsSecureOn;
	public $OptsSecurePass;
	public $OptsSkipScan;
	//BASIC
	public $OptsDBHost;
	public $OptsDBName;
	public $OptsDBUser;
	//CPANEL
	public $OptsCPNLHost = '';
	public $OptsCPNLUser = '';
	public $OptsCPNLPass = '';
	public $OptsCPNLEnable = false;
	public $OptsCPNLConnect = false;
	//CPANEL DB
	//1 = Create New, 2 = Connect Remove
	public $OptsCPNLDBAction = 'create';
	public $OptsCPNLDBHost = '';
	public $OptsCPNLDBName = '';
	public $OptsCPNLDBUser = '';
	//ADVANCED OPTS
	public $OptsSSLAdmin;
	public $OptsSSLLogin;
	public $OptsCacheWP;
	public $OptsCachePath;
	//OTHER
	public $OptsURLNew;
	
	//PROTECTED
	protected $Package;

	//CONSTRUCTOR
	function __construct($package)
	{
		$this->Package = $package;
	}

	public function get_safe_filepath()
	{
		return DUP_PRO_Util::SafePath(DUPLICATOR_PRO_SSDIR_PATH . "/{$this->File}");
	}

	public function get_url()
	{
		return DUPLICATOR_PRO_SSDIR_URL . "/{$this->File}";
	}

		
	public function Build($package, $build_progress)
	{
		/* @var $package DUP_PRO_Package */
		DUP_PRO_U::log("building installer");

		$this->Package = $package;		
		$success = false;

		if($this->build_legacy_installer())
		{					
			if($this->create_enhanced_installer_files())
			{
				$success = $this->add_extra_files($package);
			}
		}		

		if($success)
		{				
			$build_progress->installer_built = true;							
		}
		else
		{
			$build_progress->failed = true;
		}
	}

	private function build_legacy_installer()
	{
		/* @var $global DUP_PRO_Global_Entity */
		$global = DUP_PRO_Global_Entity::get_instance();
		
		$success = false;

		DUP_PRO_Log::Info("\n********************************************************************************");
		DUP_PRO_Log::Info("MAKE LEGACY INSTALLER:");
		DUP_PRO_Log::Info("********************************************************************************");
		DUP_PRO_Log::Info("Build Start");

		$template_uniqid = uniqid('') . '_' . time();
		$template_path = DUP_PRO_Util::SafePath(DUPLICATOR_PRO_SSDIR_PATH_TMP . "/installer.template_{$template_uniqid}.{$global->get_installer_extension()}");
		$main_path = DUP_PRO_Util::SafePath(DUPLICATOR_PRO_PLUGIN_PATH . 'installer/build/main.installer.php');
		@chmod($template_path, 0777);
		@chmod($main_path, 0777);

		@touch($template_path);
		$main_data = file_get_contents("{$main_path}");
		$template_result = file_put_contents($template_path, $main_data);

		if ($main_data === false)
		{
			$err_info = "Possible permission issues with file_get_contents. Please validate that PHP has read/write access.\nMain Installer: '{$main_path}";
			DUP_PRO_Log::Error("Install builder failed to generate files.", $err_info, false);

			return false;
		}
		
		if ($template_result === false)
		{
			$err_info = "Possible permission issues with file_put_contents. Please validate that PHP has read/write access.\nTemplate Installer: '{$template_path}'";
			DUP_PRO_Log::Error("Install builder failed to generate files.", $err_info, false);
			
			return false;
		}

		$embeded_files = array(
			//ASSETS
			"main.download.php"			=> "@@MAIN.DOWNLOAD.PHP@@",
			"assets/inc.libs.css.php"	=> "@@INC.LIBS.CSS.PHP@@",
			"assets/inc.css.php"		=> "@@INC.CSS.PHP@@",
			"assets/inc.libs.js.php"	=> "@@INC.LIBS.JS.PHP@@",
			"assets/inc.js.php"			=> "@@INC.JS.PHP@@",
			//CLASSES
			"classes/_libs.php"				=> "@@INC.LIBS.PHP@@",
			"classes/class.logging.php"		=> "@@CLASS.LOGGING.PHP@@",
			"classes/class.utils.php"		=> "@@CLASS.UTILS.PHP@@",
			"classes/class.http.php"		=> "@@CLASS.HTTP.PHP@@",
			"classes/class.server.php"		=> "@@CLASS.SERVER.PHP@@",
			"classes/class.conf.srv.php"	=> "@@CLASS.CONF.SRV.PHP@@",
			"classes/class.conf.wp.php"		=> "@@CLASS.CONF.WP.PHP@@",
			"classes/class.engine.php"		=> "@@CLASS.ENGINE.PHP@@",
			"classes/class.cpanel.php"		=> "@@CLASS.CPANEL.PHP@@",
			"classes/class.db.base.php"		=> "@@CLASS.DB.BASE.PHP@@",
			"classes/class.db.mysqli.php"	=> "@@CLASS.DB.MYSQLI.PHP@@",
			"classes/class.db.pdo.php"		=> "@@CLASS.DB.PDO.PHP@@",
			//CONTROLLERS
			"ctrls/action.api.php"		=> "@@ACTION.API.PHP@@",
			"ctrls/action.step1.php"	=> "@@ACTION.STEP1.PHP@@",
			"ctrls/action.step2.php"	=> "@@ACTION.STEP2.PHP@@",
			//VIEWS
			"views/view.api.php"	=> "@@VIEW.API.PHP@@",
			"views/view.init1.php"	=> "@@VIEW.INIT1.PHP@@",
			"views/view.init2.php"	=> "@@VIEW.INIT2.PHP@@",
			"views/view.step1.php"	=> "@@VIEW.STEP1.PHP@@",
			"views/view.step2.php"	=> "@@VIEW.STEP2.PHP@@",
			"views/view.step3.php"	=> "@@VIEW.STEP3.PHP@@",
			"views/view.help.php"	=> "@@VIEW.HELP.PHP@@");

		foreach ($embeded_files as $name => $token)
		{
			$file_path = DUPLICATOR_PRO_PLUGIN_PATH . "installer/build/{$name}";
			@chmod($file_path, 0777);

			$search_data = @file_get_contents($template_path);
			$insert_data = @file_get_contents($file_path);
			file_put_contents($template_path, str_replace("${token}", "{$insert_data}", $search_data));

			if ($search_data === false || $insert_data == false)
			{
				DUP_PRO_Log::Error("Installer generation failed at {$token}.");

				return false;
			}

			@chmod($file_path, 0644);
		}

		@chmod($template_path, 0644);
		@chmod($main_path, 0644);

		DUP_PRO_Log::Info("Build Finished");

		if ($this->create_legacy_installer_from_template($template_path) == false)
		{
			return false;
		}
		
		return true;
	}
	
	private function create_enhanced_installer_files()
	{			
		$success = false;
		
		if($this->create_enhanced_installer())
		{
			$success = $this->create_archive_config_file();
		}
							
		return $success;
	}
	
	private function create_enhanced_installer()
	{
		$global = DUP_PRO_Global_Entity::get_instance();
		
		$success = true;
		
		$installer_filepath = DUP_PRO_Util::SafePath(DUPLICATOR_PRO_SSDIR_PATH_TMP) . "/{$this->Package->NameHash}_{$global->installer_base_name}";
		
		$template_filepath = DUPLICATOR_PRO_PLUGIN_PATH .'/installerv2/installer.tpl';
		
		// Replace the @@ARCHIVE@@ token
		$installer_contents = file_get_contents($template_filepath);
				
		$search_array = array('@@ARCHIVE@@', '@@VERSION@@');
		$replace_array = array($this->Package->Archive->File, DUPLICATOR_PRO_VERSION);
		
		//$installer_contents = str_replace("@@ARCHIVE@@", $this->Package->Archive->File, $installer_contents);
		$installer_contents = str_replace($search_array, $replace_array, $installer_contents);
		
		if(@file_put_contents($installer_filepath, $installer_contents) === false)
		{
			DUP_PRO_Log::Error(DUP_PRO_U::__('Error writing installer contents'), DUP_PRO_U::__("Couldn't write to $installer_filepath"),false);
			$success = false;
		}
			
		if($success)
		{
			$storePath = "{$this->Package->StorePath}/{$this->File}";
			$this->Size = @filesize($storePath);
		}
				
		return $success;
	}
	
	private function create_archive_config_file()
	{
		global $wpdb;
		
		$global = DUP_PRO_Global_Entity::get_instance();
		$success = true;
		$archive_config_filepath = DUP_PRO_Util::SafePath(DUPLICATOR_PRO_SSDIR_PATH_TMP) . "/{$this->Package->NameHash}_archive.cfg";
		//$archive_config_filepath = DUP_PRO_Util::SafePath(DUPLICATOR_PRO_SSDIR_PATH_TMP) . "/dpa.cfg";
		$ac = new DUP_PRO_Archive_Config();
			
		//COMPARE VALUES		
		$ac->created = $this->Package->Created;
		$ac->version_dup = DUPLICATOR_PRO_VERSION;
		$ac->version_wp = $this->Package->VersionWP;
		$ac->version_db = $this->Package->VersionDB;
		$ac->version_php = $this->Package->VersionPHP;
		$ac->version_os = $this->Package->VersionOS;
		
		//GENERAL
		$ac->secure_on = $this->Package->Installer->OptsSecureOn;
		$ac->secure_pass = $this->Package->Installer->OptsSecurePass;
		$ac->skipscan = $this->Package->Installer->OptsSkipScan;
		$ac->installer_base_name = $global->installer_base_name;
		$ac->package_name = "{$this->Package->NameHash}_archive.zip";
		$ac->package_notes = $this->Package->Notes;
		$ac->url_old = get_option('siteurl');
		$ac->url_new = $this->Package->Installer->OptsURLNew;
		$ac->dbhost = $this->Package->Installer->OptsDBHost;
		$ac->dbname = $this->Package->Installer->OptsDBName;
		$ac->dbuser = $this->Package->Installer->OptsDBUser;
		$ac->dbpass = '';
		$ac->ssl_admin = $this->Package->Installer->OptsSSLAdmin;
		$ac->ssl_login = $this->Package->Installer->OptsSSLLogin;
		$ac->cache_wp = $this->Package->Installer->OptsCacheWP;
		$ac->cache_path = $this->Package->Installer->OptsCachePath;
		$ac->wp_tableprefix = $wpdb->prefix;
		$ac->opts_delete = json_encode($GLOBALS['DUPLICATOR_PRO_OPTS_DELETE']);
		$ac->blogname = esc_html(get_option('blogname'));
		$ac->wproot = DUPLICATOR_PRO_WPROOTPATH;
		$ac->mu_mode = DUP_PRO_U::get_mu_mode();
		
		//CPANEL
		$ac->cpnl_host = $this->Package->Installer->OptsCPNLHost;
		$ac->cpnl_user = $this->Package->Installer->OptsCPNLUser;
		$ac->cpnl_pass = $this->Package->Installer->OptsCPNLPass;
		$ac->cpnl_enable = $this->Package->Installer->OptsCPNLEnable;
		$ac->cpnl_connect = $this->Package->Installer->OptsCPNLConnect;
			
		//CPANEL:DB		
		$ac->cpnl_dbaction = $this->Package->Installer->OptsCPNLDBAction;
		$ac->cpnl_dbhost = $this->Package->Installer->OptsCPNLDBHost;
		$ac->cpnl_dbname = $this->Package->Installer->OptsCPNLDBName;					
		$ac->cpnl_dbuser = $this->Package->Installer->OptsCPNLDBUser;
		
		$json = json_encode($ac);
		
		DUP_PRO_U::log_object('json', $json);
		
	//	@chmod($archive_config_filepath, 0777);
		
		if(file_put_contents($archive_config_filepath, $json) === false)
		{
			DUP_PRO_Log::Error("Error writing archive config", "Couldn't write archive config at $archive_config_filepath", false);
			$success = false;
		}
		
		return $success;
	}
	
	/**
	 *  createZipBackup
	 *  Puts an installer zip file in the archive for backup purposes.
	 */
	private function add_extra_files($package)
	{
		$success = false;
		$global = DUP_PRO_Global_Entity::get_instance();
		$installer_filepath = DUP_PRO_Util::SafePath(DUPLICATOR_PRO_SSDIR_PATH_TMP) . "/{$this->Package->NameHash}_{$global->installer_base_name}";
		$scan_filepath = DUP_PRO_Util::SafePath(DUPLICATOR_PRO_SSDIR_PATH_TMP) . "/{$this->Package->NameHash}_scan.json";
		$sql_filepath = DUP_PRO_Util::SafePath("{$this->Package->StorePath}/{$this->Package->Database->File}");
		$zip_filepath = DUP_PRO_Util::SafePath("{$this->Package->StorePath}/{$this->Package->Archive->File}");
		$archive_config_filepath = DUP_PRO_Util::SafePath(DUPLICATOR_PRO_SSDIR_PATH_TMP) . "/{$this->Package->NameHash}_archive.cfg";
		$legacy_installer_filepath = DUP_PRO_Util::SafePath(DUPLICATOR_PRO_SSDIR_PATH_TMP) . "/{$this->Package->NameHash}_{$global->get_legacy_installer_filename()}";
		
		if(file_exists($installer_filepath) == false)
		{
			DUP_PRO_Log::Error("Installer $installer_filepath not present", '', false);
			return false;
		}
		
		if(file_exists($legacy_installer_filepath) == false)
		{
			DUP_PRO_Log::Error("Legacy installer $legacy_installer_filepath not present", '', false);
			return false;
		}
		
		if(file_exists($sql_filepath) == false)
		{
			DUP_PRO_Log::Error("Database SQL file $sql_filepath not present", '', false);
			return false;
		}
		
		if(file_exists($archive_config_filepath) == false)
		{
			DUP_PRO_Log::Error("Archive configuration file $archive_config_filepath not present", '', false);
			return false;
		}				
			
		if($package->Archive->file_count != 2)
		{
			DUP_PRO_U::log("Doing archive file check");
			// Only way it's 2 is if the root was part of the filter in which case the archive won't be there
			if(file_exists($zip_filepath) == false)
			{
				$error_text = DUP_PRO_U::__("Zip archive {$zip_filepath} not present.");
				$fix_text = DUP_PRO_U::__("Go to: Settings > Packages Tab > Set Archive Engine to ZipArchive.");
				
				DUP_PRO_Log::Error("$error_text. **RECOMMENDATION: $fix_text", '', false);
				
				$system_global = DUP_PRO_System_Global_Entity::get_instance();
				$system_global->add_recommended_text_fix($error_text, $fix_text);
				$system_global->save();
				
				return false;
			}
		}
			
		DUP_PRO_U::log("Add extra files: Current build mode = " . $package->build_progress->current_build_mode);
		
		if ($package->build_progress->current_build_mode == DUP_PRO_Archive_Build_Mode::ZipArchive)
		{			
			$success = $this->add_extra_files_using_zip_archive($installer_filepath, $legacy_installer_filepath, $scan_filepath, $sql_filepath, $zip_filepath, $archive_config_filepath);
		}
		else if ($package->build_progress->current_build_mode == DUP_PRO_Archive_Build_Mode::Shell_Exec)
		{
			$success = $this->add_extra_files_using_shellexec($zip_filepath, $installer_filepath, $legacy_installer_filepath, $scan_filepath, $sql_filepath, $archive_config_filepath);
		}
		
		// No sense keeping the archive config around
		@unlink($archive_config_filepath);
		
		$package->Archive->Size = @filesize($zip_filepath);

		return $success;
	}
	
	private function add_extra_files_using_zip_archive($installer_filepath, $legacy_installer_filepath, $scan_filepath, $sql_filepath, $zip_filepath, $archive_config_filepath)
	{	
		$success = false;
		
		$zipArchive = new ZipArchive();
		
		if ($zipArchive->open($zip_filepath, ZIPARCHIVE::CREATE) === TRUE)
		{
			DUP_PRO_U::log("Successfully opened zip $zip_filepath");
			
			if($zipArchive->addFile($scan_filepath, DUPLICATOR_PRO_EMBEDDED_SCAN_FILENAME))
			{						
				if ($this->add_installer_files_using_zip_archive($zipArchive, $installer_filepath, $legacy_installer_filepath, $archive_config_filepath))
				{
					DUP_PRO_Log::Info("Installer files added to archive");
					DUP_PRO_U::log("Added to archive");

					$success = true;
				}
				else
				{					
					DUP_PRO_Log::Error("Unable to add enhanced enhanced installer files to archive.", '', false);
				}
			}
			else
			{					
				DUP_PRO_Log::Error("Unable to add scan file to archive.", '', false);
			}				

			if($zipArchive->close() === false)
			{
				DUP_PRO_Log::Error("Couldn't close archive when adding extra files.");
				$success = false;
			}

			DUP_PRO_U::log('After ziparchive close when adding installer');		
		}
		
		return $success;
	}
	
	private function add_extra_files_using_shellexec($zip_filepath, $installer_filepath, $legacy_installer_filepath, $scan_filepath, $sql_filepath, $archive_config_filepath)
	{
		$success = false;
		$global = DUP_PRO_Global_Entity::get_instance();				

		$installer_source_directory = DUP_PRO_U::safe_path(DUP_PRO_U::$PLUGIN_DIRECTORY . DIRECTORY_SEPARATOR . 'installerv2/');
		$installer_dpro_source_directory = "$installer_source_directory/dpro-installer";
		$extras_directory = DUP_PRO_Util::SafePath(DUPLICATOR_PRO_SSDIR_PATH_TMP) . '/extras';
		$extras_installer_directory = $extras_directory . '/dpro-installer';
		
		$installer_backup_filepath = "$extras_directory/" . $global->get_installer_backup_filename();
		
		$dest_sql_filepath = "$extras_directory/database.sql";
		$dest_legacy_installer_filepath = "$extras_directory/installer-legacy.php";
		$dest_archive_config_filepath = "$extras_installer_directory/archive.cfg";
		$dest_scan_filepath = "$extras_directory/scan.json";
		
		if(file_exists($extras_directory))
		{
			if(DUP_PRO_U::del_tree($extras_directory) === false)
			{
				DUP_PRO_Log::Error("Error deleting $extras_directory", '', false);
				return false;
			}			
		}		
		
		if(!@mkdir($extras_directory))
		{
			DUP_PRO_Log::Error("Error creating extras directory", "Couldn't create $extras_directory", false);
			return false;
		}
		
		if(!@mkdir($extras_installer_directory))
		{
			DUP_PRO_Log::Error("Error creating extras directory", "Couldn't create $extras_installer_directory", false);
			return false;
		}
		
		if(@copy($installer_filepath, $installer_backup_filepath) === false)
		{
			DUP_PRO_Log::Error("Error copying $installer_filepath to $installer_backup_filepath", '', false);
			return false;
		}
				
		if(@copy($legacy_installer_filepath, $dest_legacy_installer_filepath) === false)
		{
			DUP_PRO_Log::Error("Error copying $legacy_installer_filepath to $dest_legacy_installer_filepath", '', false);
			return false;
		}

		if(@copy($sql_filepath, $dest_sql_filepath) === false)
		{
			DUP_PRO_Log::Error("Error copying $sql_filepath to $dest_sql_filepath", '', false);
			return false;
		}
		
		if(@copy($archive_config_filepath, $dest_archive_config_filepath) === false)
		{
			DUP_PRO_Log::Error("Error copying $archive_config_filepath to $dest_archive_config_filepath", '', false);
			return false;
		}
		
		if(@copy($scan_filepath, $dest_scan_filepath) === false)
		{
			DUP_PRO_Log::Error("Error copying $scan_filepath to $dest_scan_filepath", '', false);
			return false;
		}
						
		$one_stage_add = strtoupper($global->get_installer_extension()) == 'PHP';
		
		if($one_stage_add)
		{
			// If the installer has the PHP extension copy the installer files to add all extras in one shot since the server supports creation of PHP files						
			if(DUP_PRO_U::copy_directory($installer_dpro_source_directory, $extras_installer_directory) === false)
			{
				DUP_PRO_Log::Error("Error copying installer file directory to extras directory", "Couldn't copy $installer_source_directory to $extras_installer_directory", false);
				return false;	
			}					
		}
				
		//-- STAGE 1 ADD
		$compression_parameter = DUP_PRO_Shell_U::get_compression_parameter();

		$command = 'cd ' . escapeshellarg(DUP_PRO_U::safe_path($extras_directory));
		$command .= ' && ' . escapeshellcmd(DUP_PRO_Util::get_zip_filepath()) . " $compression_parameter" . ' -g -rq ';
		$command .= escapeshellarg($zip_filepath) . ' ./*';

		DUP_PRO_U::log("Executing Shell Exec Zip Stage 1 to add extras: $command");

		$stderr = shell_exec($command);

		//-- STAGE 2 ADD
		if($stderr == '')
		{
			if(!$one_stage_add)
			{
				// Since we didn't bundle the installer files in the earlier stage we have to zip things up right from the plugin source area
				$command = 'cd ' . escapeshellarg($installer_source_directory);
				$command .= ' && ' . escapeshellcmd(DUP_PRO_Util::get_zip_filepath()) . " $compression_parameter" . ' -g -rq ';
				$command .= escapeshellarg($zip_filepath) . ' dpro-installer/*';

				DUP_PRO_U::log("Executing Shell Exec Zip Stage 2 to add installer files: $command");
				$stderr = shell_exec($command);
			}
		}
		
		DUP_PRO_U::del_tree($extras_directory);
			
		if ($stderr == '')
		{
			if(DUP_PRO_Util::get_exe_filepath('unzip') != NULL)
			{					
				$installer_backup_filename = basename($installer_backup_filepath);
				
				// Verify the essential extras got in there						
				$extra_count_string = "unzip -Z1 '$zip_filepath' | grep '$installer_backup_filename\|installer-legacy.php\|scan.json\|database.sql\|archive.cfg' | wc -l";
				
				DUP_PRO_U::log("Executing extra count string $extra_count_string");
				
				$extra_count = DUP_PRO_Shell_U::execute_and_get_value($extra_count_string, 1);
								
				if(is_numeric($extra_count))
				{	
					// Accounting for the sql and installer back files
					if($extra_count >= 5)
					{
						// Since there could be files with same name accept when there are m
						DUP_PRO_U::log("Core extra files confirmed to be in the archive");	
						$success = true;
					}
					else
					{
						DUP_PRO_Log::Error("Tried to verify core extra files but one or more were missing. Count = $extra_count", '', false);
					}
				}
				else
				{                  
					DUP_PRO_U::log("Executed extra count string of $extra_count_string");
					DUP_PRO_Log::Error("Error retrieving extra count in shell zip " . $extra_count, '', false);
				}   
			}
			else
			{
				DUP_PRO_U::log("unzip doesn't exist so not doing the extra file check");
				$success = true;
			}
		}
		else
		{
			$error_text = DUP_PRO_U::__("Unable to add installer extras to archive $stderr.");
			$fix_text = DUP_PRO_U::__("Go to: Settings > Packages Tab > Set Archive Engine to ZipArchive.");

			DUP_PRO_Log::Error("$error_text  **RECOMMENDATION: $fix_text", '', false);

			$system_global = DUP_PRO_System_Global_Entity::get_instance();

			$system_global->add_recommended_text_fix($error_text, $fix_text);

			$system_global->save();
		}
		
		return $success;
	}

	// Add installerv2 directory to the archive and the archive.cfg
	private function add_installer_files_using_zip_archive(&$zip_archive, $installer_filepath, $legacy_installer_filepath, $archive_config_filepath)
	{
		$success = false;
		/* @var $global DUP_PRO_Global_Entity */
		$global = DUP_PRO_Global_Entity::get_instance();		
		$installer_backup_filename = $global->get_installer_backup_filename();
		$legacy_installer_filename = $global->get_legacy_installer_filename();
				
		DUP_PRO_U::log('Adding enhanced installer files to archive using ZipArchive');
		
		if($zip_archive->addFile($legacy_installer_filepath, $legacy_installer_filename))
		{
			if($zip_archive->addFile($installer_filepath, $installer_backup_filename))
			{
				$installer_directory = DUP_PRO_U::safe_path(DUP_PRO_U::$PLUGIN_DIRECTORY . DIRECTORY_SEPARATOR . 'installerv2/dpro-installer');

				if(DUP_PRO_U::add_directory_using_ziparchive($zip_archive, $installer_directory))
				{				
					$archive_config_local_name = 'dpro-installer/archive.cfg';

					if($zip_archive->addFile($archive_config_filepath, $archive_config_local_name))
					{
						$success = true;
					}						
					else
					{
						DUP_PRO_Log::Error("Error adding $archive_config_filepath to zipArchive", '', false);
					}
				}
				else
				{
					DUP_PRO_Log::Error("Error adding directory $installer_directory to zipArchive", '', false);
				}
			}
			else
			{
				DUP_PRO_Log::Error("Error adding backup installer file to zipArchive", '', false);
			}
		}
		else
		{
			DUP_PRO_Log::Error("Error adding legacy installer file to zipArchive", '', false);
		}
				
		return $success;
	}
	
	// Returns true if correctly added installer backup to root false if not
	private static function add_installer_backup_file_to_root($package)
	{
		$global = DUP_PRO_Global_Entity::get_instance();
		$installer_path = DUP_PRO_Util::SafePath(DUPLICATOR_PRO_SSDIR_PATH_TMP) . "/{$package->NameHash}_{$global->installer_base_name}";

		$home_path = get_home_path();

		// Add installer to root directory
		$archive_installerbak_filepath = $home_path . $global->get_installer_backup_filename();

		return DUP_PRO_U::copy_with_verify($installer_path, $archive_installerbak_filepath);
	}

	// Returns false if correctly added installer backup to root false if not
	private static function add_sql_file_to_root($source_sql_filepath)
	{
		$home_path = get_home_path();

		$archive_sql_filepath = $home_path . 'database.sql';

		return DUP_PRO_U::copy_with_verify($source_sql_filepath, $archive_sql_filepath);
	}
	
	private static function add_scan_file_to_root($package)
	{
		$global = DUP_PRO_Global_Entity::get_instance();
		$source_scan_path = DUP_PRO_Util::SafePath(DUPLICATOR_PRO_SSDIR_PATH_TMP) . "/{$package->NameHash}_scan.json";

		$home_path = get_home_path();

		// Add scan to root directory
		$dest_scan_path = $home_path . DUPLICATOR_PRO_EMBEDDED_SCAN_FILENAME;

		return DUP_PRO_U::copy_with_verify($source_scan_path, $dest_scan_path);
	}
	

	/**
	 *  createFromTemplate
	 *  Generates the final installer file from the template file
	 */
	private function create_legacy_installer_from_template($template)
	{
		global $wpdb;
		/* @var $global DUP_PRO_Global_Entity */
		$global = DUP_PRO_Global_Entity::get_instance();
		
		DUP_PRO_Log::Info("Prepping for use");
		//$legacy_installer_filepath = DUP_PRO_Util::SafePath(DUPLICATOR_PRO_SSDIR_PATH_TMP) . "/{$this->Package->NameHash}_{$global->installer_base_name}";
		$legacy_installer_filepath = DUP_PRO_Util::SafePath(DUPLICATOR_PRO_SSDIR_PATH_TMP) . "/{$this->Package->NameHash}_{$global->get_legacy_installer_filename()}";

		//Option values to delete at install time
		$deleteOpts = $GLOBALS['DUPLICATOR_PRO_OPTS_DELETE'];

		
		$replace_items = Array(
			//COMPARE VALUES
			"fwrite_created" => $this->Package->Created,
			"fwrite_version_dup" => DUPLICATOR_PRO_VERSION,
			"fwrite_version_wp" => $this->Package->VersionWP,
			"fwrite_version_db" => $this->Package->VersionDB,
			"fwrite_version_php" => $this->Package->VersionPHP,
			"fwrite_version_os" => $this->Package->VersionOS,
			//GENERAL
			"fwrite_secure_on" => $this->Package->Installer->OptsSecureOn,						
			"fwrite_secure_pass" => $this->Package->Installer->OptsSecurePass,	
			"fwrite_skipscan" => $this->Package->Installer->OptsSkipScan,	
			"fwrite_installer_base_name" => $global->installer_base_name,
			"fwrite_package_name" => "{$this->Package->NameHash}_archive.zip",
			"fwrite_package_notes" => $this->Package->Notes,
			"fwrite_url_old" => get_option('siteurl'),							
			"fwrite_url_new" => $this->Package->Installer->OptsURLNew,
			"fwrite_dbhost" => $this->Package->Installer->OptsDBHost,
			"fwrite_dbname" => $this->Package->Installer->OptsDBName,
			"fwrite_dbuser" => $this->Package->Installer->OptsDBUser,
			"fwrite_dbpass" => '',
			"fwrite_ssl_admin" => $this->Package->Installer->OptsSSLAdmin,
			"fwrite_ssl_login" => $this->Package->Installer->OptsSSLLogin,
			"fwrite_cache_wp" => $this->Package->Installer->OptsCacheWP,
			"fwrite_cache_path" => $this->Package->Installer->OptsCachePath,
			"fwrite_wp_tableprefix" => $wpdb->prefix,
			"fwrite_opts_delete" => json_encode($deleteOpts),
			"fwrite_blogname" => esc_html(get_option('blogname')),
			"fwrite_wproot" => DUPLICATOR_PRO_WPROOTPATH,
			'mu_mode' => DUP_PRO_U::get_mu_mode(),
		
			//CPANEL
			"fwrite_cpnl_host" => $this->Package->Installer->OptsCPNLHost,	
			"fwrite_cpnl_user" => $this->Package->Installer->OptsCPNLUser,		
			"fwrite_cpnl_pass" => $this->Package->Installer->OptsCPNLPass,
			"fwrite_cpnl_enable" => $this->Package->Installer->OptsCPNLEnable,	
			"fwrite_cpnl_connect" => $this->Package->Installer->OptsCPNLConnect,
			//CPANEL:DB		
			"fwrite_cpnl_dbaction"  => $this->Package->Installer->OptsCPNLDBAction,
			"fwrite_cpnl_dbhost"	=> $this->Package->Installer->OptsCPNLDBHost,
			"fwrite_cpnl_dbname"	=> $this->Package->Installer->OptsCPNLDBName,					
			"fwrite_cpnl_dbuser"	=> $this->Package->Installer->OptsCPNLDBUser);

		if (file_exists($template) && is_readable($template))
		{
			$err_msg = "ERROR: Unable to read/write installer. \nERROR INFO: Check permission/owner on file and parent folder.\nInstaller File = <{$legacy_installer_filepath}>";
			$install_str = $this->parseTemplate($template, $replace_items);
			(empty($install_str)) ? DUP_PRO_Log::Error("{$err_msg}", "DUP_PRO_Installer::createFromTemplate => file-empty-read") : DUP_PRO_Log::Info("Template parsed with new data");

			//INSTALLER FILE
			$fp = (!file_exists($legacy_installer_filepath)) ? fopen($legacy_installer_filepath, 'x+') : fopen($legacy_installer_filepath, 'w');
			if (!$fp || !fwrite($fp, $install_str, strlen($install_str)))
			{
				$error_text = sprintf(DUP_PRO_U::__("Cannot write to %s"), $installer);
				$fix_text = sprintf(DUP_PRO_U::__("Go to: Settings > Packages Tab and 'Installer Name' extension OR change permissions on %s"), DUPLICATOR_PRO_SSDIR_PATH_TMP);
				
				$system_global = DUP_PRO_System_Global_Entity::get_instance();
				$system_global->add_recommended_text_fix($error_text, $fix_text);
				$system_global->save();
				
				DUP_PRO_Log::Error("{$err_msg}", "DUP_PRO_Installer::createFromTemplate => file-write-error");
				return false;
			}

			@fclose($fp);
		}
		else
		{
			DUP_PRO_Log::Error("Installer Template missing or unreadable.", "Template [{$template}]");
			return false;
		}
		@unlink($template);
		DUP_PRO_Log::Info("Complete [{$legacy_installer_filepath}]");
		return true;
	}

	/**
	 *  parseTemplate
	 *  Tokenize a file based on an array key 
	 *
	 *  @param string $filename		The filename to tokenize
	 *  @param array  $data			The array of key value items to tokenize
	 */
	private function parseTemplate($filename, $data)
	{
		$q = file_get_contents($filename);
		foreach ($data as $key => $value)
		{
			//NOTE: Use var_export as it's probably best and most "thorough" way to
			//make sure the values are set correctly in the template.  But in the template,
			//need to make things properly formatted so that when real syntax errors
			//exist they are easy to spot.  So the values will be surrounded by quotes

			$find = array("'%{$key}%'", "\"%{$key}%\"");
			$q = str_replace($find, var_export($value, true), $q);
			//now, account for places that do not surround with quotes...  these
			//places do NOT need to use var_export as they are not inside strings
			$q = str_replace('%' . $key . '%', $value, $q);
		}
		return $q;
	}

}

?>
