<?php


// ------------------------------------------------------------------------
// REQUIRE MINIMUM VERSION OF WORDPRESS:                                               
// ------------------------------------------------------------------------
// THIS IS USEFUL IF YOU REQUIRE A MINIMUM VERSION OF WORDPRESS TO RUN YOUR
// PLUGIN. IN THIS PLUGIN THE WP_EDITOR() FUNCTION REQUIRES WORDPRESS 3.3 
// OR ABOVE. ANYTHING LESS SHOWS A WARNING AND THE PLUGIN IS DEACTIVATED.                    
// ------------------------------------------------------------------------

function requires_wordpress_version() {
	global $wp_version;
	$plugin = plugin_basename( __FILE__ );
	$plugin_data = get_plugin_data( __FILE__, false );

	if ( version_compare($wp_version, "3.3", "<" ) ) {
		if( is_plugin_active($plugin) ) {
			deactivate_plugins( $plugin );
			wp_die( "'".$plugin_data['Name']."' requires WordPress 3.3 or higher, and has been deactivated! Please upgrade WordPress and try again.<br /><br />Back to <a href='".admin_url()."'>WordPress admin</a>." );
		}
	}
}
add_action( 'admin_init', 'requires_wordpress_version' );

// ------------------------------------------------------------------------
// PLUGIN PREFIX:                                                          
// ------------------------------------------------------------------------
// A PREFIX IS USED TO AVOID CONFLICTS WITH EXISTING PLUGIN FUNCTION NAMES.
// WHEN CREATING A NEW PLUGIN, CHANGE THE PREFIX AND USE YOUR TEXT EDITORS 
// SEARCH/REPLACE FUNCTION TO RENAME THEM ALL QUICKLY.
// ------------------------------------------------------------------------

// 'onFilm_' prefix is derived from [p]plugin [o]ptions [s]tarter [k]it

// ------------------------------------------------------------------------
// REGISTER HOOKS & CALLBACK FUNCTIONS:
// ------------------------------------------------------------------------
// HOOKS TO SETUP DEFAULT PLUGIN OPTIONS, HANDLE CLEAN-UP OF OPTIONS WHEN
// PLUGIN IS DEACTIVATED AND DELETED, INITIALISE PLUGIN, ADD OPTIONS PAGE.
// ------------------------------------------------------------------------

// Set-up Action and Filter Hooks
register_activation_hook(__FILE__, 'onFilm_add_defaults');
register_uninstall_hook(__FILE__, 'onFilm_delete_plugin_options');
add_action('admin_init', 'onFilm_init' );
add_action('admin_menu', 'onFilm_add_options_page');
add_filter( 'plugin_action_links', 'onFilm_plugin_action_links', 10, 2 );


add_action('admin_footer', 'my_admin_footer');


// --------------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: register_uninstall_hook(__FILE__, 'onFilm_delete_plugin_options')
// --------------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE USER DEACTIVATES AND DELETES THE PLUGIN. IT SIMPLY DELETES
// THE PLUGIN OPTIONS DB ENTRY (WHICH IS AN ARRAY STORING ALL THE PLUGIN OPTIONS).
// --------------------------------------------------------------------------------------

// Delete options table entries ONLY when plugin deactivated AND deleted
function onFilm_delete_plugin_options() {
	delete_option('onFilm_options');
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: register_activation_hook(__FILE__, 'onFilm_add_defaults')
// ------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE PLUGIN IS ACTIVATED. IF THERE ARE NO THEME OPTIONS
// CURRENTLY SET, OR THE USER HAS SELECTED THE CHECKBOX TO RESET OPTIONS TO THEIR
// DEFAULTS THEN THE OPTIONS ARE SET/RESET.
//
// OTHERWISE, THE PLUGIN OPTIONS REMAIN UNCHANGED.
// ------------------------------------------------------------------------------

// Define default option settings
function onFilm_add_defaults() {
	$tmp = get_option('onFilm_options');
    if(($tmp['chk_default_options_db']=='1')||(!is_array($tmp))) {
		delete_option('onFilm_options'); // so we don't have to reset all the 'off' checkboxes too! (don't think this is needed but leave for now)
		$arr = array(	"chk_button1" => "1",
					//	"chk_button3" => "1",
					//	"textarea_one" => "This type of control allows a large amount of information to be entered all at once. Set the 'rows' and 'cols' attributes to set the width and height.",
					//	"textarea_two" => "This text area control uses the TinyMCE editor to make it super easy to add formatted content.",
					//	"textarea_three" => "Another TinyMCE editor! It is really easy now in WordPress 3.3 to add one or more instances of the built-in WP editor.",
						"weather_lat" => "Enter the peak yurt price here..",
					//	"drp_select_box" => "four",
						"chk_default_options_db" => "",
						// "rdo_group_one" => "one",
						// 					"rdo_group_two" => "two"
		);
		update_option('onFilm_options', $arr);
	}
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: add_action('admin_init', 'onFilm_init' )
// ------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE 'admin_init' HOOK FIRES, AND REGISTERS YOUR PLUGIN
// SETTING WITH THE WORDPRESS SETTINGS API. YOU WON'T BE ABLE TO USE THE SETTINGS
// API UNTIL YOU DO.
// ------------------------------------------------------------------------------

// Init plugin options to white list our options
function onFilm_init(){
	register_setting( 'onFilm_plugin_options', 'onFilm_options', 'onFilm_validate_options' );
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: add_action('admin_menu', 'onFilm_add_options_page');
// ------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE 'admin_menu' HOOK FIRES, AND ADDS A NEW OPTIONS
// PAGE FOR YOUR PLUGIN TO THE SETTINGS MENU.
// ------------------------------------------------------------------------------

// Add menu page
function onFilm_add_options_page() {
	add_options_page('onFilm Options ', 'onFilm Options', 'manage_options', __FILE__, 'onFilm_render_form');
}


// ------------------------------------------------------------------------------
// CALLBACK FUNCTION SPECIFIED IN: add_options_page()
// ------------------------------------------------------------------------------
// THIS FUNCTION IS SPECIFIED IN add_options_page() AS THE CALLBACK FUNCTION THAT
// ACTUALLY RENDER THE PLUGIN OPTIONS FORM AS A SUB-MENU UNDER THE EXISTING
// SETTINGS ADMIN MENU.
// ------------------------------------------------------------------------------




// Render the Plugin options form
function onFilm_render_form() {
	?>
	<div class="wrap">
		
		<!-- Display Plugin Icon, Header, and Description -->
		<div class="icon32" id="icon-options-general"><br></div>
		<h2>onFilm Theme Options</h2>
	

		<!-- Beginning of the Plugin Options Form -->
		<form method="post" action="options.php">
			<?php settings_fields('onFilm_plugin_options'); ?>
			<?php $options = get_option('onFilm_options'); ?>

			<!-- Table Structure Containing Form Controls -->
			<!-- Each Plugin Option Defined on a New Table Row -->
			<table class="form-table">

				
<!-- ------------------------------------------------------------------Associates -->
				<tr>
								<th scope="row">Set the target link for Sponsor 1</th>
								<td>
									<input  type="text" size="57" name="onFilm_options[logo1_link]" value="<?php echo $options['logo1_link']; ?>" />
								</td>
							</tr>
								<tr>
									
									
									
									<th scope="row">Link to the picture to display in Sponsor 1</th>
									<td>
										<input type="text" size="57" name="onFilm_options[logo1_logo]" value="<?php echo $options['logo1_logo']; ?>" />
									</td>
								</tr>
								<tr><td colspan="2"><div style="margin-top:10px;"></div></td></tr>
		
					<tr>
				<th scope="row">Set the target link for Sponsor 2</th>
				<td>
					<input  type="text" size="57" name="onFilm_options[logo2_link]" value="<?php echo $options['logo2_link']; ?>" />
				</td>
			</tr>
				<tr>



				<th scope="row">Link to the picture to display in Sponsor 2</th>
				<td>
					<input type="text" size="57" name="onFilm_options[logo2_logo]" value="<?php echo $options['logo2_logo']; ?>" />
				</td>
			</tr>
			
			<tr><td colspan="2"><div style="margin-top:10px;"></div></td></tr>
			
			<tr>
			<th scope="row">Set the target link for Sponsor 3</th>
			<td>
				<input  type="text" size="57" name="onFilm_options[logo3_link]" value="<?php echo $options['logo3_link']; ?>" />
			</td>
		</tr>
			<tr>



				<th scope="row">Link to the picture to display in Sponsor 3</th>
				<td>
					<input type="text" size="57" name="onFilm_options[logo3_logo]" value="<?php echo $options['logo3_logo']; ?>" />
				</td>
			</tr>
			
			
			<tr><td colspan="2"><div style="margin-top:10px;"></div></td></tr>
			
			<tr>
		<th scope="row">Set the target link for Sponsor 4</th>
		<td>
			<input  type="text" size="57" name="onFilm_options[logo4_link]" value="<?php echo $options['logo4_link']; ?>" />
		</td>
	</tr>
		<tr>



	<th scope="row">Link to the picture to display in Sponsor 4</th>
	<td>
		<input type="text" size="57" name="onFilm_options[logo4_logo]" value="<?php echo $options['logo4_logo']; ?>" />
	</td>
</tr>

<tr><td colspan="2"><div style="margin-top:10px;"></div></td></tr>


	<tr>
					<th scope="row">Set the target link for Sponsor 5</th>
					<td>
						<input  type="text" size="57" name="onFilm_options[logo5_link]" value="<?php echo $options['logo5_link']; ?>" />
					</td>
				</tr>
					<tr>



						<th scope="row">Link to the picture to display in Sponsor 5</th>
						<td>
							<input type="text" size="57" name="onFilm_options[logo5_logo]" value="<?php echo $options['logo5_logo']; ?>" />
						</td>
					</tr>
													
																			
					<tr><td colspan="2"><div style="margin-top:10px;"></div></td></tr>
				
							<tr>
										<th scope="row">Set the target link for Sponsor 6</th>
										<td>
											<input  type="text" size="57" name="onFilm_options[logo6_link]" value="<?php echo $options['logo6_link']; ?>" />
										</td>
									</tr>
										<tr>



						<th scope="row">Link to the picture to display in Sponsor 6</th>
						<td>
							<input type="text" size="57" name="onFilm_options[logo6_logo]" value="<?php echo $options['logo6_logo']; ?>" />
						</td>
					</tr>
				
				
				
		
				
				
						
	<tr>
					<th scope="row">Set the target link for Sponsor 7</th>
					<td>
						<input  type="text" size="57" name="onFilm_options[logo7_link]" value="<?php echo $options['logo7_link']; ?>" />
					</td>
				</tr>
					<tr>



	<th scope="row">Link to the picture to display in Sponsor 7</th>
	<td>
		<input type="text" size="57" name="onFilm_options[logo7_logo]" value="<?php echo $options['logo7_logo']; ?>" />
	</td>
</tr>


				
				<tr>
							<th scope="row">Set the target link for Sponsor 8</th>
							<td>
								<input  type="text" size="57" name="onFilm_options[logo8_link]" value="<?php echo $options['logo8_link']; ?>" />
							</td>
						</tr>
							<tr>



			<th scope="row">Link to the picture to display in Sponsor 8</th>
			<td>
				<input type="text" size="57" name="onFilm_options[logo8_logo]" value="<?php echo $options['logo8_logo']; ?>" />
			</td>
		</tr>



			<tr><td colspan="2"><div style="margin-top:10px;"></div></td></tr>
			<tr valign="top" style="border-top:#dddddd 1px solid;">
				
			
				
						<tr>
										<th scope="row">Set the target link for Image 1</th>
										<td>
											<input  type="text" size="57" name="onFilm_options[img1_link]" value="<?php echo $options['img1_link']; ?>" />
										</td>
									</tr>
										<tr>



											<th scope="row">Link to the picture to display in Image 1</th>
											<td>
												<input type="text" size="57" name="onFilm_options[img1_img]" value="<?php echo $options['img1_img']; ?>" />
											</td>
										</tr>
										<tr><td colspan="2"><div style="margin-top:10px;"></div></td></tr>

						<tr>
					<th scope="row">Set the target link for Image 2</th>
					<td>
						<input  type="text" size="57" name="onFilm_options[img2_link]" value="<?php echo $options['img2_link']; ?>" />
					</td>
				</tr>
					<tr>



					<th scope="row">Link to the picture to display in Image 2</th>
					<td>
						<input type="text" size="57" name="onFilm_options[img2_img]" value="<?php echo $options['img2_img']; ?>" />
					</td>
				</tr>

				<tr><td colspan="2"><div style="margin-top:10px;"></div></td></tr>

				<tr>
				<th scope="row">Set the target link for Image 3</th>
				<td>
					<input  type="text" size="57" name="onFilm_options[img3_link]" value="<?php echo $options['img3_link']; ?>" />
				</td>
			</tr>
				<tr>



					<th scope="row">Link to the picture to display in Image 3</th>
					<td>
						<input type="text" size="57" name="onFilm_options[img3_img]" value="<?php echo $options['img3_img']; ?>" />
					</td>
				</tr>


				<tr><td colspan="2"><div style="margin-top:10px;"></div></td></tr>

				<tr>
			<th scope="row">Set the target link for Image 4</th>
			<td>
				<input  type="text" size="57" name="onFilm_options[img4_link]" value="<?php echo $options['img4_link']; ?>" />
			</td>
			</tr>
			<tr>



		<th scope="row">Link to the picture to display in Image 4</th>
		<td>
		<input type="text" size="57" name="onFilm_options[img4_img]" value="<?php echo $options['img4_img']; ?>" />
		</td>
		</tr>

		<tr><td colspan="2"><div style="margin-top:10px;"></div></td></tr>


		<tr>
				<th scope="row">Set the target link for Image 5</th>
				<td>
					<input  type="text" size="57" name="onFilm_options[img5_link]" value="<?php echo $options['img5_link']; ?>" />
				</td>
			</tr>
				<tr>



					<th scope="row">Link to the picture to display in Image 5</th>
					<td>
						<input type="text" size="57" name="onFilm_options[img5_img]" value="<?php echo $options['img5_img']; ?>" />
					</td>
				</tr>


	<tr><td colspan="2"><div style="margin-top:10px;"></div></td></tr>



	<tr>
		<th scope="row">Set the target link for Image 6</th>
		<td>
			<input  type="text" size="57" name="onFilm_options[img6_link]" value="<?php echo $options['img6_link']; ?>" />
		</td>
	</tr>
		<tr>



			<th scope="row">Link to the picture to display in Image 6</th>
			<td>
				<input type="text" size="57" name="onFilm_options[img6_img]" value="<?php echo $options['img6_img']; ?>" />
			</td>
		</tr>




		<tr>	
			<th scope="row">Set the target link for Image 7</th>
			<td>
				<input  type="text" size="57" name="onFilm_options[img7_link]" value="<?php echo $options['img7_link']; ?>" />
			</td>
		</tr>
			<tr>



				<th scope="row">Link to the picture to display in Image 6</th>
				<td>
					<input type="text" size="57" name="onFilm_options[img7_img]" value="<?php echo $options['img7_img']; ?>" />
				</td>
			</tr>				



		<tr>
						<th scope="row">Set the target link for Image 8</th>
						<td>
							<input  type="text" size="57" name="onFilm_options[img8_link]" value="<?php echo $options['img8_link']; ?>" />
						</td>
					</tr>
						<tr>



							<th scope="row">Link to the picture to display in Image 8</th>
							<td>
								<input type="text" size="57" name="onFilm_options[img8_img]" value="<?php echo $options['img8_img']; ?>" />
							</td>
						</tr>




				<tr><td colspan="2"><div style="margin-top:10px;"></div></td></tr>
				<tr valign="top" style="border-top:#dddddd 1px solid;">
					<th scope="row">Showcase Active?</th>
					<td>
						<label><input name="onFilm_options[films_shown]" type="checkbox" value="1" <?php if (isset($options['films_shown'])) { checked('1', $options['films_shown']); } ?> /> Check to show all videos on the front page.</label>
						
					</td>
						

				<tr><td colspan="2"><div style="margin-top:10px;"></div></td></tr>
				<tr valign="top" style="border-top:#dddddd 1px solid;">
					<th scope="row">Database Options</th>
					<td>
						<label><input name="onFilm_options[chk_default_options_db]" type="checkbox" value="1" <?php if (isset($options['chk_default_options_db'])) { checked('1', $options['chk_default_options_db']); } ?> /> Restore defaults upon plugin deactivation/reactivation</label>
						<br /><span style="color:#666666;margin-left:2px;">Only check this if you want to reset plugin settings upon Plugin reactivation</span>
					</td>
				</tr>
			</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>

	</div>
	<?php	
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function onFilm_validate_options($input) {
	// strip html from textboxes
	// $input['textarea_one'] =  wp_filter_nohtml_kses($input['textarea_one']); // Sanitize textarea input (strip html tags, and escape characters)
	$input['logo1_logo'] =  wp_filter_nohtml_kses($input['logo1_logo']); // Sanitize textbox input (strip html tags, and escape characters)
	return $input;
		// $input['logo1_link'] =  wp_filter_nohtml_kses($input['txt_one']); // Sanitize textbox input (strip html tags, and escape characters)
		// 	return $input;
		// 	$input['logo2_logo'] =  wp_filter_nohtml_kses($input['txt_one']); // Sanitize textbox input (strip html tags, and escape characters)
		// 	return $input;
		// 	$input['logo2_link'] =  wp_filter_nohtml_kses($input['txt_one']); // Sanitize textbox input (strip html tags, and escape characters)
		// 	return $input;
		// 	$input['logo3_logo'] =  wp_filter_nohtml_kses($input['txt_one']); // Sanitize textbox input (strip html tags, and escape characters)
		// 	return $input;
		// 	$input['logo3_link'] =  wp_filter_nohtml_kses($input['txt_one']); // Sanitize textbox input (strip html tags, and escape characters)
		// 	return $input;
		// 	$input['logo4_logo'] =  wp_filter_nohtml_kses($input['txt_one']); // Sanitize textbox input (strip html tags, and escape characters)
		// 	return $input;
		// 	$input['logo4_link'] =  wp_filter_nohtml_kses($input['txt_one']); // Sanitize textbox input (strip html tags, and escape characters)
	return $input;
}

// Display a Settings link on the main Plugins page
function onFilm_plugin_action_links( $links, $file ) {

	if ( $file == plugin_basename( __FILE__ ) ) {
		$onFilm_links = '<a href="'.get_admin_url().'options-general.php?page=onFilm-options/calendar-option.php">'.__('Settings').'</a>';
		// make the 'Settings' link appear first
		array_unshift( $links, $onFilm_links );
	}

	return $links;
}

// ------------------------------------------------------------------------------
// SAMPLE USAGE FUNCTIONS:
// ------------------------------------------------------------------------------
// THE FOLLOWING FUNCTIONS SAMPLE USAGE OF THE PLUGINS OPTIONS DEFINED ABOVE. TRY
// CHANGING THE DROPDOWN SELECT BOX VALUE AND SAVING THE CHANGES. THEN REFRESH
// A PAGE ON YOUR SITE TO SEE THE UPDATED VALUE.
// ------------------------------------------------------------------------------

// As a demo let's add a paragraph of the select box value to the content output
// add_filter( "the_content", "onFilm_add_content" );
// function onFilm_add_content($text) {
// 	$options = get_option('onFilm_options');
// 	$select = $options['drp_select_box'];
// 	$text = "<p style=\"color: #777;border:1px dashed #999; padding: 6px;\">Select box Plugin option is: {$select}</p>{$text}";
// 	return $text;
// }

?>