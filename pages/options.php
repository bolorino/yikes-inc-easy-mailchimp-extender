<script type="text/javascript">
jQuery(document).ready(function ($) {
	// check for blank fields
	// runs when we add or remove a list from the lists pages
    function blankFieldCheck() {
        err = 0;
        msg = '';
        if ($('#yks-mailchimp-api-key').val() == '') {
            msg += '* Enter a Mailchimp API Key!' + "\n";
            err++;
        }
        if (msg != '') {
            msg = 'Please fix the following before submitting the form:' + "\n\n" + msg;
            alert(msg);
        }
        return (err > 0 ? false : true);
    }
	// ajax save the WordPress Plugin options page
    $('#yks-mailchimp-form').submit(function (e) {	
        e.preventDefault();
        // Make sure the api key exists
        if (blankFieldCheck()) {
            $('#yks-status').slideUp('fast');
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'yks_mailchimp_form',
                    form_action: 'update_options',
                    form_data: $('#yks-mailchimp-form').serialize()
                },
                dataType: 'json',
                success: function (MAILCHIMP) {
                    if (MAILCHIMP == '1') {
						console.log(MAILCHIMP);
                        $('#yks-status').html('<div class="updated"><p><?php _e('The options were saved successfully!', 'yikes-inc-easy-mailchimp-extender'); ?></p></div>');
                        $('#yks-status').slideDown('fast');
                    } else {
                        $('#yks-status').html('<div class="error"><p><?php _e('The options could not be saved (or you did not change them).', 'yikes-inc-easy-mailchimp-extender'); ?></p></div>');
                        $('#yks-status').slideDown('fast');
						console.log(MAILCHIMP);
                    }
                }
            });
        }
        return false;
    });
	
	/*******************	Validate MailChimp API Key ****************************/
	// Ajax function which is fired when the user types in a value into the API input field
	function yikes_mc_api_key_validate() {
		jQuery('#submit').attr('disabled', 'disabled');
		jQuery('.mailChimp_api_key_validation_message').hide();
		// delay the function incase the user has deleted their API key
		setTimeout(function() {
			
			var thisLength = jQuery('#yks-mailchimp-api-key').val().length;
					
			if (thisLength >= 1) {	
					// store Mail Chimp API Key
					var apiKey = jQuery('#yks-mailchimp-api-key').val();
					// store datacenter value, from end of api key
					var dataCenter = apiKey.substr(apiKey.indexOf("-") + 1);
					
					if ( jQuery('.mailChimp_api_key_preloader').is(":visible")) {
						//
					} else {
						jQuery('.mailChimp_api_key_preloader').fadeIn();
					}
						// post the data to our api key validation function inside of lib.ajax.php
						jQuery.ajax({
							type: 'POST',
							url: ajaxurl,
							data: {
								 action: 'yks_mailchimp_form',
								form_action: 'validate_api_key',
								api_key: apiKey,
								data_center: dataCenter
							},
							success: function(response) {
								// if our response contains 'Everything's Chimpty' - everythings good to go
								if(response.indexOf('Everything\'s Chimpy!') > -1) {
										var yikes_mc_ajax_response = response;
										jQuery('#ajax_list_replace').html(yikes_mc_ajax_response);
										var yikes_mc_ajax_html_content = jQuery('#ajax_list_replace').html();
										var replaced_text = yikes_mc_ajax_html_content.replace("Everything's Chimpy!", "");
										jQuery('#ajax_list_replace').html(replaced_text);
									jQuery('.mailChimp_api_key_preloader').fadeOut('fast', function() {
										jQuery('.mailChimp_api_key_validation_message').html('<img src="<?php echo plugins_url().'/yikes-inc-easy-mailchimp-extender/images/yikes-mc-checkmark.png'; ?>" alt=message > <?php _e('Valid API Key','yikes-inc-easy-mailchimp-extender'); ?>').css("color", "green").fadeIn();
										jQuery('#submit').removeAttr('disabled');
									});
								// if our response contains 'Invalid MailChimp API Key' - display an error	
								} else if (response.indexOf('Invalid Mailchimp API Key') > -1) {
									jQuery('.mailChimp_api_key_preloader').fadeOut('fast', function() {
										jQuery('.mailChimp_api_key_validation_message').html('<img src="<?php echo plugins_url().'/yikes-inc-easy-mailchimp-extender/images/yikes-mc-error-icon.png'; ?>" alt=message > <?php _e('Sorry, that is an invalid MailChimp API key.','yikes-inc-easy-mailchimp-extender'); ?>').css("color", "red").fadeIn();
									});
									console.log('MailChimp API Response : '+response);
								} else {
								// if our response contains anything else, other than whats above, just let them know its invalid
									jQuery('.mailChimp_api_key_preloader').fadeOut('fast', function() {
										jQuery('.mailChimp_api_key_validation_message').html('<img src="<?php echo plugins_url().'/yikes-inc-easy-mailchimp-extender/images/yikes-mc-error-icon.png'; ?>" alt=message > <?php _e('Sorry, that is an invalid MailChimp API key. Please check the console for further information.','yikes-inc-easy-mailchimp-extender'); ?>').css("color", "red").fadeIn();
									});	
									console.log('MailChimp API Response : '+response);
								};
							}
						});	
			} else {
				// if the length of the API input value is less than 1 (aka 0)
				jQuery('.mailChimp_api_key_preloader').fadeOut('fast', function() {
					jQuery('.mailChimp_api_key_validation_message').html('<img src="<?php echo plugins_url().'/yikes-inc-easy-mailchimp-extender/images/yikes-mc-error-icon.png'; ?>" alt=message > <?php _e('Error: Please enter a valid Mail Chimp API Key.','yikes-inc-easy-mailchimp-extender'); ?>').css("color", "red").fadeIn();
				});	
			}
		}, 1);

	}

	// run the validation on every keyup
	jQuery('#yks-mailchimp-api-key').keyup(function() {
			stop();
			yikes_mc_api_key_validate();
	});
	
	// check the API key on page load
	yikes_mc_api_key_validate();
	
});

// function which runs when we change the OptIn value (from single to double, or double to single)
function changeOptinValue() {
	var newOptinValue = jQuery('#yks-mailchimp-optIn').val();
	if ( newOptinValue == 'true' ) {
		jQuery('label[for="single-optin-message"]').slideUp('fast',function() {
			jQuery('label[for="double-optin-message"]').slideDown('fast');
		});	
	} else {
		jQuery('label[for="double-optin-message"]').slideUp('fast',function() {
			jQuery('label[for="single-optin-message"]').slideDown('fast');
		});	
	}
}

function changeOptinSubscribeCheckbox() {
	var newOptinValue = jQuery('#yks-mailchimp-optIn-checkbox').val();
	if ( newOptinValue == '1' ) {
		jQuery('.optin-checkbox-default-list-container').fadeIn('fast');
	} else {
		jQuery('.optin-checkbox-default-list-container').fadeOut('fast');
	}
}
jQuery(document).ready(function() {
	changeOptinSubscribeCheckbox();
});
</script>

<!-- get and store our api key option -->
<?php
	$api_key_option = get_option( 'api_validation' );
?>
<div class="wrap">

<div id="ykseme-icon" class="icon32"></div>
	<h2 id="ykseme-page-header">
		<?php _e('Easy Mailchimp Forms by YIKES, Inc.','yikes-inc-easy-mailchimp-extender'); ?>
	</h2>

	<h3><?php _e('Manage Mailchimp Forms Settings','yikes-inc-easy-mailchimp-extender'); ?></h3>
	
	<div class="yks-status" id="yks-status"></div>
	
	<form method="post" name="yks-mailchimp-form" id="yks-mailchimp-form">
		
		<table class="form-table yks-admin-form">
			<tbody>
				<!-- MailChimp API Key Field -->
				<tr valign="top">
					<th scope="row"><label for="yks-mailchimp-api-key"><?php _e('Your Mailchimp API Key','yikes-inc-easy-mailchimp-extender'); ?></label></th>
					<td><input name="yks-mailchimp-api-key" type="text" id="yks-mailchimp-api-key" value="<?php echo $this->optionVal['api-key']; ?>" class="regular-text" /><span class="mailChimp_api_key_validation_message"></span><img class="mailChimp_api_key_preloader" src="<?php echo admin_url().'/images/wpspin_light.gif'; ?>" alt="preloader" ><span class="mailChimp_api_key_validation"></span>
					</td>
				</tr>
				<!-- MailChimp API Key Description -->
				<tr>
					<td></td>
					<td class="yks-settings-description">
						<?php _e('Please enter your MailChimp API Key above. The API Key allows your WordPress site to communicate with your MailChimp account.','yikes-inc-easy-mailchimp-extender'); ?><br />
						<?php _e('For more help, visit the MailChimp Support article','yikes-inc-easy-mailchimp-extender'); ?> <a href="http://kb.mailchimp.com/article/where-can-i-find-my-api-key" target="_blank"><?php _e('Where can I find my API Key?','yikes-inc-easy-mailchimp-extender'); ?></a>
					</td>
				</tr>
				<!-- Preferred Form Layout (table or div) -->
				<tr valign="top">
					<th scope="row"><label for="yks-mailchimp-flavor"><?php _e('Preferred Form Layout','yikes-inc-easy-mailchimp-extender'); ?></label></th>
					<td>
						<select name="yks-mailchimp-flavor" id="yks-mailchimp-flavor" class="regular-text" />
							<option value="0"<?php echo ($this->optionVal['flavor'] === '0' ? ' selected' : ''); ?>><?php _e('table','yikes-inc-easy-mailchimp-extender'); ?></option>
							<option value="1"<?php echo ($this->optionVal['flavor'] === '1' ? ' selected' : ''); ?>><?php _e('div','yikes-inc-easy-mailchimp-extender'); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td></td>
					<!-- Preferred Form Layout Description -->
					<td class="yks-settings-description">
						<?php _e('Choose whether you want your forms to use a table or div layout.','yikes-inc-easy-mailchimp-extender'); ?>
					</td>
				</tr>				
				<!-- Advanced Debug -->
				<tr valign="top">
					<th scope="row"><label for="yks-mailchimp-debug"><?php _e('Advanced Error Messaging','yikes-inc-easy-mailchimp-extender'); ?></label></th>
					<td>
						<select name="yks-mailchimp-debug" id="yks-mailchimp-debug" class="regular-text" />
							<option value="0"<?php echo ($this->optionVal['debug'] === '0' ? ' selected' : ''); ?>><?php _e('Disabled','yikes-inc-easy-mailchimp-extender'); ?></option>
							<option value="1"<?php echo ($this->optionVal['debug'] === '1' ? ' selected' : ''); ?>><?php _e('Enabled','yikes-inc-easy-mailchimp-extender'); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td></td>
					<!-- Advanced Debug Description -->
					<td class="yks-settings-description">
						<?php _e('Enable if you\'re having problems with your forms sending data to MailChimp. Enabling Advanced Error Messaging will show you the exact error codes MailChimp is returning.','yikes-inc-easy-mailchimp-extender'); ?>
					</td>
				</tr>
				<tr valign="top">
				<!-- Optin Value (single or double) -->
					<th scope="row"><label for="yks-mailchimp-optIn">Single or Double Opt-In</label></th>
					<td>
						<select name="yks-mailchimp-optin" id="yks-mailchimp-optIn" class="regular-text" onchange="changeOptinValue();" />
							<option value="false"<?php echo ($this->optionVal['optin'] === 'false' ? ' selected' : ''); ?>><?php _e('Single Opt-In','yikes-inc-easy-mailchimp-extender'); ?></option>
							<option value="true"<?php echo ($this->optionVal['optin'] === 'true' ? ' selected' : ''); ?>><?php _e('Double Opt-In','yikes-inc-easy-mailchimp-extender'); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td></td>
					<!-- Optin Description -->
					<td class="yks-settings-description">
						<?php _e('A single opt-in will add the user to your list without any further interaction.','yikes-inc-easy-mailchimp-extender'); ?> <br /> 
						<?php _e('A double opt-in will send an email to the user asking them to confirm their subscription.','yikes-inc-easy-mailchimp-extender'); ?> <br />
						<?php _e('This will also dictate the opt-in settings for people leaving comments.','yikes-inc-easy-mailchimp-extender'); ?>
					</td>
				</tr>
				<tr valign="top">
					<!-- Custom Opt-In Message -->
					<th scope="row"><label for="yks-mailchimp-custom-optIn-message"><?php _e('Custom Opt-In Message','yikes-inc-easy-mailchimp-extender'); ?></label></th>
					<td>
						<label for="double-optin-message" <?php if ($this->optionVal['optin'] == 'false') { echo 'style="display:none;"'; } ?>><b><?php _e('Double Opt-In Message','yikes-inc-easy-mailchimp-extender'); ?></b><br />
						<textarea name="double-optin-message" class="double-optin-message" id="double-optin-message" value="<?php echo $this->optionVal['double-optin-message']; ?>"><?php echo $this->optionVal['double-optin-message']; ?></textarea></label>
						<label for="single-optin-message" <?php if ($this->optionVal['optin'] == 'true') { echo 'style="display:none;"'; } ?>><b><?php _e('Single Opt-In Message','yikes-inc-easy-mailchimp-extender'); ?></b><br />
						<textarea name="single-optin-message" class="single-optin-message" id="single-optin-message" value="<?php echo $this->optionVal['single-optin-message']; ?>"><?php echo $this->optionVal['single-optin-message']; ?></textarea></label>
					</td>
				</tr>
				<tr valign="top">
					<!-- Custom Interest Group Label -->
					<th scope="row"><label for="yks-mailchimp-optIn"><?php _e('Interest Group Label','yikes-inc-easy-mailchimp-extender'); ?></label></th>
					<td>
						<input type="text" name="interest-group-label" placeholder="Select an Interest" class="yks-mailchimp-interest-group-label" value="<?php echo $this->optionVal['interest-group-label']; ?>" />
					</td>
				</tr>
				<tr>
					<td></td>
					<!-- Custom Interest Group Label Description -->
					<td class="yks-settings-description">
						<?php _e('Text to display above interest groups. Leave blank to use MailChimp interest group names.','yikes-inc-easy-mailchimp-extender'); ?>
					</td>
				</tr>
				<tr valign="top">
					<!-- Display OPTIN CHECKBOX SETTING -->
					<th scope="row"><label for="yks-mailchimp-optIn-checkbox"><?php _e('Display opt-in checkbox on comment forms?','yikes-inc-easy-mailchimp-extender'); ?></label></th>
					<td>
						<select name="yks-mailchimp-optIn-checkbox" id="yks-mailchimp-optIn-checkbox" class="regular-text" onchange="changeOptinSubscribeCheckbox();" />
							<option value="0"<?php echo ($this->optionVal['yks-mailchimp-optIn-checkbox'] === '0' ? ' selected' : ''); ?>><?php _e('Hide','yikes-inc-easy-mailchimp-extender'); ?></option>
							<option value="1"<?php echo ($this->optionVal['yks-mailchimp-optIn-checkbox'] === '1' ? ' selected' : ''); ?>><?php _e('Show','yikes-inc-easy-mailchimp-extender'); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td></td>
					<!-- OPTIN CHECKBOX SETTING Description -->
					<td class="yks-settings-description">
						<?php _e('This will display a checkbox just above the submit button on all comment forms. If selected, any users leaving comments will also be added to the mailing list.','yikes-inc-easy-mailchimp-extender'); ?>
					</td>
				</tr>
				<tr class="optin-checkbox-default-list-container">
					<!-- Custom Interest Group Label -->
					<th scope="row"><label for="yks-mailchimp-optin-checkbox-text"><?php _e('Custom Comment Checkbox Text','yikes-inc-easy-mailchimp-extender'); ?></label></th>
					<td>
						<input type="text" name="yks-mailchimp-optin-checkbox-text" placeholder="Sign Me Up For <?php echo bloginfo('name'); ?>'s Newsletter" class="yks-mailchimp-interest-group-label" value="<?php echo $this->optionVal['yks-mailchimp-optin-checkbox-text']; ?>" />
					</td>
				</tr>
				<tr valign="top" class="optin-checkbox-default-list-container">
					<!-- Optin Checkbox Default List to Submit Subscribers Too -->
					<th scope="row"><label for="yks-mailchimp-custom-optIn-message"><?php _e('Default List','yikes-inc-easy-mailchimp-extender'); ?></label></th>
					<td>
						<!-- get all lists from MailChimp -->
						<?php 
						if ( $api_key_option == 'invalid_api_key' ) {
							echo '<div id="ajax_list_replace"><select><option>Please Enter a Valid API Key</option></select></div>';
						} else {
							echo '<div id="ajax_list_replace"></div>'; 
						}
						?>
					</td>
				</tr>
				<tr class="optin-checkbox-default-list-container">
					<td></td>
					<td class="yks-settings-description"> <!-- Description of optin checkbox default list-->
						<?php _e('This is the default list users will be subscribed to when submitting a comment.','yikes-inc-easy-mailchimp-extender'); ?><br />
						<em><?php _e('It is best to select a form where only the email , first name and/or last name are required or you may run into issues.','yikes-inc-easy-mailchimp-extender'); ?></em>
					</td>
				</tr>
				<tr>
					<td></td>
					<td><input type="submit" name="submit" id="submit" class="button-primary" value="Save Settings" disabled="disabled"></td>
				</tr>	
			</tbody>
		</table>

	</form>
	<!-- Plugin Info -->
	<h3><?php _e('Plugin Information','yikes-inc-easy-mailchimp-extender'); ?></h3>
	<!-- Issues? Contact Us. -->
	<p>
		<?php _e('If you experience any issues with our plugin, please','yikes-inc-easy-mailchimp-extender'); ?> <a href="https://github.com/yikesinc/yikes-inc-easy-mailchimp-extender/issues" target="_blank"><?php _e('submit a New Issue on our Github Issue Tracker','yikes-inc-easy-mailchimp-extender'); ?></a>. <?php _e('Please include the information below to help us troubleshoot your problem.','yikes-inc-easy-mailchimp-extender'); ?>
	</p>

	<table class="form-table yks-admin-form">
		<tbody>
			<!-- User Debug Section -->
			<!-- Plugin Version, Browser Version etc. -->
			<tr valign="top">
				<th scope="row"><label><?php _e('Plugin Version','yikes-inc-easy-mailchimp-extender'); ?></label></th>
				<td><?php echo YKSEME_VERSION_CURRENT; ?></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label><?php _e('Wordpress Version','yikes-inc-easy-mailchimp-extender'); ?></label></th>
				<td><?php echo get_bloginfo( 'version' ); ?></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label><?php _e('Browser Information','yikes-inc-easy-mailchimp-extender'); ?></label></th>
				<td>
					<?php
					$theBrowser = $this->getBrowser();
					echo $theBrowser['name'].' '.$theBrowser['version'].' on '.$theBrowser['platform'];					
					?>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<!-- Display Tracking Info? -->
<?php $this->getTrackingGif('options'); ?>