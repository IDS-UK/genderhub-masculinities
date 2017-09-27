<?php
global $wp_version;
global $wpdb;

require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/classes/utilities/class.license.u.php');

$global = DUP_PRO_Global_Entity::get_instance();
$force_refresh = true;
$nonce_action = 'duppro-settings-licensing-edit';

$license_activation_response = null;

$error_response = null;
$action_response = null;

//SAVE RESULTS
if (isset($_POST['action']))
{    
    $action = $_POST['action'];
    switch($action)
    {
        case 'activate':            
            $submitted_license_key = trim($_REQUEST['_license_key']);
            update_option(DUP_PRO_Constants::LICENSE_KEY_OPTION_NAME, $submitted_license_key);
            
			$license_activation_response = DUP_PRO_License_Utility::change_license_activation(true);
				
			switch($license_activation_response)
			{
				case DUP_PRO_License_Activation_Response::OK:
					$action_response = DUP_PRO_U::__("License Activated");
					break;
				
				case DUP_PRO_License_Activation_Response::POST_ERROR:
					$error_response = sprintf(DUP_PRO_U::__("Cannot communicate with snapcreek.com. Please see <a target='_blank' href='%s'>this FAQ entry</a> for possible causes and resolutions."), 'https://snapcreek.com/duplicator/docs/faqs-tech/#faq-licensing-08-q');
					break;
				
				case DUP_PRO_License_Activation_Response::INVALID_RESPONSE:
				default:
					$error_response = DUP_PRO_U::__('Error activating license.');
					break;
			}

            break;
        
        case 'deactivate':
			
			$license_activation_response = DUP_PRO_License_Utility::change_license_activation(false);
			
			switch($license_activation_response)
			{
				case DUP_PRO_License_Activation_Response::OK:
					$action_response = DUP_PRO_U::__("License Deactivated");
					break;
				
				case DUP_PRO_License_Activation_Response::POST_ERROR:
					$error_response = sprintf(DUP_PRO_U::__("Cannot communicate with snapcreek.com. Please see <a target='_blank' href='%s'>this FAQ entry</a> for possible causes and resolutions."), 'https://snapcreek.com/duplicator/docs/faqs-tech/#faq-licensing-08-q');
					break;
				
				case DUP_PRO_License_Activation_Response::INVALID_RESPONSE:
				default:
					$error_response = DUP_PRO_U::__('Error deactivating license.');
					break;
			}
			
            break;
		
		case 'hide_key':
			// RSR TODO: Passwords must match. If they do then set the password and set visibility
			$password = $_REQUEST['_key_password'];
			$password_confirmation = $_REQUEST['_key_password_confirmation'];
			
			if(empty($password))
			{
				$error_response = DUP_PRO_U::__('Password cannot be empty.');
			}
			else
			{
				if($password == $password_confirmation)
				{
					$global->license_key_visible = false;
					$global->lkp = $password;
					$global->save();

					$action_response = DUP_PRO_U::__("Key now hidden.");
				}
				else
				{
					$error_response = DUP_PRO_U::__("Passwords don't match.");
				}
			}
			break;
		
		case 'show_key':
			// RSR TODO: Passwords must match. If they do then set the password and set visibility
			$password = $_REQUEST['_key_password'];
			
			if($password == $global->lkp)
			{
				$global->license_key_visible = true;
				$global->lkp = '';
				$global->save();
				
				$action_response = DUP_PRO_U::__("Key now visible.");
			}
			else
			{
				$error_response = DUP_PRO_U::__("Wrong password entered. Key remains hidden.");
			}
			
			break;
     }
     
     $force_refresh = true;
}

$license_status = DUP_PRO_License_Utility::get_license_status($force_refresh);
$license_text_disabled = false;
$activate_button_text = DUP_PRO_U::__('Activate');     
$license_status_text_alt = '';

if($license_status == DUP_PRO_License_Status::Valid)
{
    $license_status_style = 'color:#509B18';
    $license_status_text = DUP_PRO_U::__('Status: Active');
    $activate_button_text = DUP_PRO_U::__('Deactivate');
    $license_text_disabled = true;
}
else if(($license_status == DUP_PRO_License_Status::Inactive))
{
    $license_status_style = 'color:#dd3d36;';	
    $license_status_text = DUP_PRO_U::__('Status: Inactive');
}
else if($license_status == DUP_PRO_License_Status::Site_Inactive)
{
	$license_status_style = 'color:#dd3d36;';	
	
	/* @var $global DUP_PRO_Global_Entity */
	$global = DUP_PRO_Global_Entity::get_instance();
	
	if($global->license_no_activations_left)
	{
		$license_status_text = DUP_PRO_U::__('Status: Inactive (out of site licenses)');
	}
	else
	{
		$license_status_text = DUP_PRO_U::__('Status: Inactive');
	}
}
else if($license_status == DUP_PRO_License_Status::Expired)
{
	$license_key = get_option(DUP_PRO_Constants::LICENSE_KEY_OPTION_NAME, '');				
	$renewal_url = 'https://snapcreek.com/checkout?edd_license_key=' . $license_key;				
	$license_status_style = 'color:#dd3d36;';    
	$license_status_text = sprintf('Your Duplicator Pro license key has expired so you aren\'t getting important updates! <a target="_blank" href="%1$s">Renew your license now</a>', $renewal_url);
}
else
{
    $license_status_string = DUP_PRO_License_Utility::get_license_status_string($license_status);
    $license_status_style = 'color:#dd3d36;';    
	$license_status_text  = DUP_PRO_U::__('Status: ') . $license_status_string . '<br/>';
	$license_status_text_alt  = DUP_PRO_U::__('If the license fails to activate please wait a few minutes and try again.');
	$license_status_text_alt .= '<br/><br/>';
    $license_status_text_alt .= sprintf(DUP_PRO_U::__('- Failure to activate after several attempts please review %1$sfaq activation steps%2$s'), '<a target="_blank" href="https://snapcreek.com/duplicator/docs/faqs-tech/#faq-licensing-005-q">', '</a>.<br/>');
	$license_status_text_alt .= sprintf(DUP_PRO_U::__('- To upgrade or renew your license visit %1$ssnapcreek.com%2$s'), '<a target="_blank" href="https://snapcreek.com">', '</a>.<br/>');
}
$license_key = get_option(DUP_PRO_Constants::LICENSE_KEY_OPTION_NAME, '');
?>

<form id="dup-settings-form" action="<?php echo self_admin_url('admin.php?page=' . DUP_PRO_Constants::$SETTINGS_SUBMENU_SLUG ); ?>" method="post" data-parsley-validate>
    <?php wp_nonce_field($nonce_action); ?>
    <input type="hidden" name="action" value="save" id="action">
    <input type="hidden" name="page"   value="<?php echo DUP_PRO_Constants::$SETTINGS_SUBMENU_SLUG ?>">
    <input type="hidden" name="tab"   value="licensing">

    <?php if ($action_response != null) : ?>
        <div id="message" class="updated below-h2"><p><?php echo $action_response; ?></p></div>
	<?php endif; ?>
    <?php if ($error_response != null) : ?>	
        <div id="message" class="error below-h2"><p><?php echo $error_response; ?></p></div>
    <?php endif; ?>

    <h3 class="title"><?php DUP_PRO_U::_e("Activation") ?> </h3>
    <hr size="1" />
    <table class="form-table">
        <tr valign="top">           
            <th scope="row"><?php DUP_PRO_U::_e("Manage") ?></th>
            <td><?php echo sprintf(DUP_PRO_U::__('%1$s[Manage Licenses Online]%2$s'), '<a target="_blank" href="https://snapcreek.com/dashboard">', '</a>'); ?></td>
        </tr>		
        <tr valign="top">           
            <th scope="row"><label><?php DUP_PRO_U::_e("License Key"); ?></label></th>
            <td>
                <input type="<?php echo $global->license_key_visible ? 'text' : 'password'; ?>" class="wide-input" name="_license_key" id="_license_key" <?php DUP_PRO_U::echo_disabled($license_text_disabled); ?> value="<?php echo $license_key; ?>" />
				<br/>
				<p class="description">
                    <?php 
						echo "<span style='$license_status_style'>$license_status_text</span>"; 
						echo $license_status_text_alt;
					?>
                </p>				
				<br/><br/>
				<button onclick="DupPro.Licensing.ChangeActivationStatus(<?php echo (($license_status != DUP_PRO_License_Status::Valid) ? 'true' : 'false'); ?>);return false;"><?php echo $activate_button_text; ?></button>          				
            </td>
        </tr>
    </table>   
	
	<h3 class="title"><?php DUP_PRO_U::_e("Key Visibility") ?> </h3>
	<small>
		<?php 
			DUP_PRO_U::_e("This is an optional setting that prevents the 'License Key' from being copied.  Enter a password and hit the 'Hide Key' button."); 
			echo '<br/>';
			DUP_PRO_U::_e("To show the 'License Key' and allow for it to be copied to your clipboard enter in the password and hit the 'Show Key' button."); 
		?>
	</small>
    <hr size="1" />
    <table class="form-table">
        <tr valign="top">           
            <th scope="row"><label><?php DUP_PRO_U::_e("Password"); ?></label></th>
            <td>
                <input type="password" class="wide-input" name="_key_password" id="_key_password"  />
            </td>
        </tr>
		<tr style="display:<?php echo $global->license_key_visible ? 'table-row' : 'none'; ?>" valign="top">           
            <th scope="row"><label><?php DUP_PRO_U::_e("Retype Password"); ?></label></th>
            <td>
                <input type="password" class="wide-input" name="_key_password_confirmation" id="_key_password_confirmation"  />				             				
            </td>
        </tr>
		<tr valign="top">           
            <th scope="row"></th>
            <td>
				<button id="show_hide" onclick="DupPro.Licensing.ChangeKeyVisibility(<?php DUP_PRO_U::echo_boolean(!$global->license_key_visible); ?>); return false;"><?php echo $global->license_key_visible ? DUP_PRO_U::__('Hide Key') : DUP_PRO_U::__('Show Key'); ?></button>
            </td>
        </tr>
    </table>   
</form>

<script type="text/javascript">
    jQuery(document).ready(function($) 
	{
        DupPro.Licensing = new Object();
		
		// Ensure if they hit enter in one of the password boxes the correct action takes place
		$("#_key_password, #_key_password_confirmation").keyup(function(event){

			if(event.keyCode == 13){
				$("#show_hide").click();
			}
		});
		
        // which: 0=installer, 1=archive, 2=sql file, 3=log
        DupPro.Licensing.ChangeActivationStatus = function (activate) 
		{    
            if(activate){
                $('#action').val('activate');
            } 
            else  {
                $('#action').val('deactivate');
            }
            $('#dup-settings-form').submit();
        }
		
		DupPro.Licensing.ChangeKeyVisibility = function (show) 
		{    
            if(show){
                $('#action').val('show_key');
            } 
            else  {
                $('#action').val('hide_key');
            }
            $('#dup-settings-form').submit();
        }							
    });
</script>
