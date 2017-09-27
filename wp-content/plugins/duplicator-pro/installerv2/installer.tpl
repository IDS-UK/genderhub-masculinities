<?php
/*
  Duplicator Pro Website Installer Bootstrap
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

/* To force ziparchvie set:
  installer.php?unzipmode=auto
  installer.php?unzipmode=ziparchive
  installer.php?unzipmode=shellexec */

if (!class_exists('DUPX_Bootstrap_Unzip_Mode'))
{
	abstract class DUPX_Bootstrap_Unzip_Mode
	{
		const Auto = 0;
		const ZipArchive = 1;
		const ShellExec = 2;
	}	
}

if (!class_exists('DUPX_Connectivity'))
{
	abstract class DUPX_Connectivity
	{
		const OK = 0;
		const Error = 1;
		const Unknown = 2;
	}	
}

if (!class_exists('DUPX_Bootstrap'))
{
	class DUPX_Bootstrap
	{
		// Name can get get dynamically swapped when package is built
		const ARCHIVE_FILENAME = '@@ARCHIVE@@';
		const INSTALLER_DIR_NAME = 'dpro-installer';
		const BOOTSTRAP_LOG = './installer-bootlog.txt';

		public function run()
		{
			date_default_timezone_set('UTC'); // Some machines don’t have this set so just do it here

			self::log_write('== DUPLICATOR PRO INSTALLER BOOTSTRAP v@@VERSION@@==');
			self::init_log();
			self::log_write('Installer bootstrap start'); // RSR

			$archive_filepath = $this->get_archive_filepath();
			$archive_filename = basename($archive_filepath);
			$unzip_mode = $this->get_unzip_mode();
			$error_text = null;
			$extract_installer = true;

			$installer_directory = dirname(__FILE__) . '/' . self::INSTALLER_DIR_NAME;
			$extract_success = false;

			if (file_exists($archive_filepath))
			{
				if (file_exists($installer_directory))
				{
					self::log_write("$installer_directory already exists");
					$extract_installer = !file_exists($installer_directory . '/main.installer.php');
					if ($extract_installer)
					{
						self::log_write("But main.installer.php doesn't so extracting anyway");
					}
					else
					{
						self::log_write("main.installer.php also exists so not going to extract installer directory");
					}
				}
				else
				{
					self::log_write("$installer_directory doesn't yet exist");
				}

				if ($extract_installer)
				{
					$ziparchive_exists = class_exists('ZipArchive');
					self::log_write("A to extract the installer");

					if (($unzip_mode == DUPX_Bootstrap_Unzip_Mode::Auto) || ($unzip_mode == DUPX_Bootstrap_Unzip_Mode::ZipArchive) && class_exists('ZipArchive'))
					{
						if ($ziparchive_exists)
						{
							self::log_write("ZipArchive exists so using that");
							$extract_success = $this->extract_installer_with_ziparchive($archive_filepath);

							if ($extract_success)
							{
								self::log_write('Successfully extracted with ZipArchive');
							}
							else
							{
								$error_text = 'Error extracting with ZipArchive.';
								self::log_write($error_text);
							}
						}
						else
						{
							self::log_write("WARNING: ZipArchive is not enabled.");
							$error_text = "NOTICE: ZipArchive is not enabled on this server please talk to your host or server admin about enabling ";
							$error_text .= "<a target='_blank' href='https://snapcreek.com/duplicator/docs/faqs-tech/#faq-trouble-060-q'>ZipArchive</a> on this server. <br/>";
						}
					}

					if (!$extract_success)
					{
						if (($unzip_mode == DUPX_Bootstrap_Unzip_Mode::Auto) || ($unzip_mode == DUPX_Bootstrap_Unzip_Mode::ShellExec))
						{
							$unzip_filepath = self::get_unzip_filepath();
							if ($unzip_filepath != null)
							{
								$extract_success = $this->extract_installer_with_shellexec($archive_filepath);
								if ($extract_success)
								{
									self::log_write('Successfully extracted with Shell Exec');
									$error_text = null;
								}
								else
								{
									$error_text .= '<br/>Error extracting with Shell Exec. Please manually extract archive then choose Advanced > Manual Extract in installer.';
									self::log_write($error_text);
								}
							}
							else
							{
								self::log_write('WARNING: Shell Exec Zip is not available');
								$error_text .= "NOTICE: Shell Exec is not enabled on this server please talk to your host or server admin about enabling ";
								$error_text .= "<a target='_blank' href='http://php.net/manual/en/function.shell-exec.php'>Shell Exec</a> on this server.";
							}
						}
					}
				}
				else
				{
					self::log_write("Didn't need to extract the installer.");
				}
			}
			else
			{
				$error_text = "$archive_filepath doesn't exist";
				self::log_write($error_text);
			}

			if (isset($_SERVER['HTTPS']))
			{
				if ($_SERVER['HTTPS'] == 'off')
				{
					$current_url = 'http://';
				}
				else
				{
					$current_url = 'https://';
				}
			}
			else
			{
				$current_url = 'http://';
			}

			$current_url .= $_SERVER['SERVER_NAME'];
			$current_url .= $_SERVER['REQUEST_URI'];

			$uri_start = dirname($current_url);

			if ($error_text == null)
			{			
				$bootloader_name = basename(__FILE__);
				$main_installer_uri = $uri_start . '/' . self::INSTALLER_DIR_NAME . '/main.installer.php';				
				
				self::fix_installer_permissions($main_installer_uri);
									
				$main_installer_uri = $main_installer_uri . "?archive=$archive_filename&bootloader=$bootloader_name";
										
				self::log_write("No detected errors so redirecting to the main installer. Main Installer URI = $main_installer_uri");
					
echo <<<REDIRECT
				<html>
					<head>										
						<meta http-equiv="refresh" content="2;url="{$main_installer_uri}" />
					
						<script type="text/javascript">
							window.location = "{$main_installer_uri}";
						</script>
					</head>
				</html>
REDIRECT;
												
			}
			
			if($error_text != null)
			{
				$bootstrap_log_uri = $uri_start . '/' . self::BOOTSTRAP_LOG;

				echo "<div style='line-height:26px; font-family:Helvetica Neue,sans-serif'>";
				echo "<b>BOOT-STRAP EXTRACTION NOTICE:</b><br/>{$error_text}<br/>";
				echo "<i>Please Note: Either ZipArchive or Shell Exec will need to be enabled for the installer to run automatically otherwise a manual extraction will need to be performed.</i><br/><br/>";
				echo "MANUAL EXTRACTION:<br/> In order to run the installer manually follow the instructions to ";
				echo "<a href='https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-015-q' target='_blank'>manually extract</a> before running the installer.";
				echo "<br/><br/>Check <a target='_blank' href='$bootstrap_log_uri'>installer bootstrap log</a> for full command trace.";
				echo "</div>";
			}
		}

		private static function fix_installer_permissions($main_installer_uri)
		{			
			$perms = substr(sprintf('%o', fileperms(__FILE__)), -4);;
		
			$perms = octdec($perms);

			$installer_directory_path = dirname(__FILE__) . '/' . self::INSTALLER_DIR_NAME;
			
			self::set_file_perms($installer_directory_path, $perms);
			
			// RSR TODO: Add execute to dpro-installer-dir? Is setting execute of group safe/reliable?
		}
		
		// Best attempt at setting file permissions
		private static function set_file_perms($directory, $perms)
		{
			$filepaths = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

			foreach($filepaths as $filepath => $object)
			{
				if(is_file($filepath))
				{
					$result = @chmod($filepath, $perms);

					$perms_display = decoct($perms);

					if($result === false)
					{
						self::log_write("Couldn't set permissions of $filepath to {$perms_display}<br/>");
					}
					else
					{
						self::log_write("Set permissions of $filepath to {$perms_display}<br/>");
					}
				}
			}
		}

		public static function init_log()
		{
			@unlink(self::BOOTSTRAP_LOG);
			self::log_write('== DUPLICATOR PRO INSTALLER BOOTSTRAP v@@VERSION@@==');
			self::log_write('----------------------------------------------------');
		}

		public static function log_write($s)
		{
			$timestamp = date('M j H:i:s'); // RSR
			file_put_contents(self::BOOTSTRAP_LOG, "$timestamp $s\n", FILE_APPEND); // RSR
		}

		private function extract_installer_with_ziparchive($archive_filepath)
		{
			$success = true;
			$zipArchive = new ZipArchive();

			if ($zipArchive->open($archive_filepath) === true)
			{
				self::log_write("Successfully opened $archive_filepath");
				$destination = dirname(__FILE__);

				$folder_prefix = self::INSTALLER_DIR_NAME . '/';
				self::log_write("Extracting all files from archive within " . self::INSTALLER_DIR_NAME);

				for ($i = 0; $i < $zipArchive->numFiles; $i++)
				{
					$stat = $zipArchive->statIndex($i);
					$filename = $stat['name'];

					if (self::starts_with($filename, $folder_prefix))
					{
						if ($zipArchive->extractTo($destination, $filename) === true)
						{
							self::log_write("Successfully extracted {$filename} to {$destination}");
						}
						else
						{
							self::log_write("Error extracting {$filename} from archive {$archive_filepath}");
							$success = false;
							break;
						}
					}
				}

				if ($zipArchive->close() === true)
				{
					self::log_write("Successfully closed {$archive_filepath}");
				}
				else
				{
					self::log_write("Problem closing {$archive_filepath}");
					$success = false;
				}
			}
			else
			{
				self::log_write("Couldn't open archive {$archive_filepath} with ZipArchive");
				$success = false;
			}
			return $success;
		}

		private function extract_installer_with_shellexec($archive_filepath)
		{
			$success = false;
			self::log_write("Attempting to use Shell Exec");
			$unzip_filepath = self::get_unzip_filepath();

			if ($unzip_filepath != null)
			{
				$unzip_command = "$unzip_filepath -q $archive_filepath " . self::INSTALLER_DIR_NAME . '/* 2>&1';
				self::log_write("Executing $unzip_command");
				$stderr = shell_exec($unzip_command);

				if ($stderr == '')
				{
					self::log_write("Shell exec unzip succeeded");
					$success = true;
				}
				else
				{
					self::log_write("Shell exec unzip failed. Output={$stderr}");
				}
			}

			return $success;
		}

		private function get_archive_filepath()
		{
			$archive_filename = self::ARCHIVE_FILENAME;

			if (isset($_GET['archive']))
			{
				$archive_filename = $_GET['archive'];
			}

			$archive_filepath = dirname(__FILE__) . '/' . $archive_filename;
			self::log_write("Using archive $archive_filepath");
			return $archive_filepath;
		}

		private function get_unzip_mode()
		{
			$unzip_mode = DUPX_Bootstrap_Unzip_Mode::Auto;

			if (isset($_GET['unzipmode']))
			{
				$unzipmode_string = $_GET['unzipmode'];
				self::log_write("Unzip mode specified in querystring: $unzipmode_string");

				switch ($unzipmode_string)
				{
					case 'auto':
						$unzip_mode = DUPX_Bootstrap_Unzip_Mode::Auto;
						break;

					case 'ziparchive':
						$unzip_mode = DUPX_Bootstrap_Unzip_Mode::ZipArchive;
						break;

					case 'shellexec':
						$unzip_mode = DUPX_Bootstrap_Unzip_Mode::ShellExec;
						break;
				}
			}

			return $unzip_mode;
		}

		static function starts_with($haystack, $needle)
		{
			return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
		}

		static function is_shell_exec_available()
		{
			$cmds = array('shell_exec', 'escapeshellarg', 'escapeshellcmd', 'extension_loaded');

			//Function disabled at server level
			if (array_intersect($cmds, array_map('trim', explode(',', @ini_get('disable_functions')))))
				return false;

			//Suhosin: http://www.hardened-php.net/suhosin/
			//Will cause PHP to silently fail
			if (extension_loaded('suhosin'))
			{
				$suhosin_ini = @ini_get("suhosin.executor.func.blacklist");
				if (array_intersect($cmds, array_map('trim', explode(',', $suhosin_ini))))
					return false;
			}
			// Can we issue a simple echo command?
			if (!@shell_exec('echo duplicator'))
				return false;

			return true;
		}

		public static function get_unzip_filepath()
		{
			$filepath = null;

			if (self::is_shell_exec_available())
			{
				if (shell_exec('hash unzip 2>&1') == NULL)
				{
					$filepath = 'unzip';
				}
				else
				{
					$possible_paths = array(
						'/usr/bin/unzip',
						'/opt/local/bin/unzip'// RSR TODO put back in when we support shellexec on windows,
					);

					foreach ($possible_paths as $path)
					{
						if (file_exists($path))
						{
							$filepath = $path;
							break;
						}
					}
				}
			}

			return $filepath;
		}

	}

}

$bootstrap = new DUPX_Bootstrap();

$bootstrap->run();
/* Used for integrity check do not remove:
  DUPLICATOR_PRO_INSTALLER_EOF */
?>