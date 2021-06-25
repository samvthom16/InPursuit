<?php

class INPURSUIT_ADMIN_SETTINGS extends INPURSUIT_BASE {

	function __construct()
	{
		$this->setNavigationTabs();
		add_action( 'admin_menu', [$this, 'registerMenu'] );
		add_action( 'admin_init', [$this, 'settingsOptionsRegistration'] );
	}


	/**
	 * Callback function for registering menu 
	 */
	public function registerMenu()
	{
		add_menu_page(
	        'InPursuit Settings',
	        'InPursuit',
	        'manage_options',
	        'inpursuit-settings',
	        [$this, 'settingsTemplateCallback'],
	        'dashicons-sos',
	        82
	    );
	}


	/**
	 * Initialize options for Tabbed Navigation. 
	 * Update this attribute if new tab needs to be added
	 * Match the 'section-page' value to 'page-slug' value of section-settings-attributes 
	 */
	public function setNavigationTabs()
	{		
		$this->navigationTabs = [
			[
				'slug' 			=> 'email-templates',
				'title'			=> 'Email Templates',
				'section-page' 	=> 'inpursuit-email-templates'
			],

			[
				'slug' 			=> 'email-fields',
				'title'			=> 'Email Fields',
				'section-page' 	=> 'inpursuit-email-fields'
			],

			[
				'slug' 			=> 'cron-settings',
				'title'			=> 'Cron Settings',
				'section-page' 	=> 'inpursuit-cron-settings'
			],

		];
	}


	/**
	 * Returns array config for TabbedNavigation
	 */
	public function getNavgitaionTabs()
	{
		return $this->navigationTabs;
	}



	public function settingsTemplateCallback()
	{
		?>
	    <div class="wrap">
	      <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	      <?php
		  	$navigation_tabs = $this->getNavgitaionTabs(); 
		  	$active_tab = 'email-templates';

	      	if(isset($_GET['tab'])) {
		      	$active_tab = $_GET['tab'];
	      	}
	      ?>

		  <h2 class="nav-tab-wrapper">
			  <?php 
			 	foreach ($navigation_tabs as $tab) {
					
					$active_class = ($tab['slug'] == $active_tab) ? 'nav-tab-active' : '';
					
					echo '<a href="?page=inpursuit-settings&tab='.$tab['slug'].'" class="nav-tab '.$active_class.'">'.$tab['title'].'</a>';

				} 
			  ?>
		  </h2>


	      <form action="options.php" method="post">
	        <?php
			
			foreach ($navigation_tabs as $tab) {
				if( $active_tab == $tab['slug'] ) {
					settings_fields( $tab['section-page'] );
					do_settings_sections( $tab['section-page'] );
					
					break;
				}
			}	
	        
	        // output save settings button
	        submit_button( 'Save Settings' );
	        ?>
	      </form>
	    </div>
    <?php
	}


	/**
	* Settings Options Registeration
	*/
	public function settingsOptionsRegistration()
	{

		//section settings attributes setup

		$section_args = [
			[
				'section-id' 	=> 'inpursuit_email_template_section',
				'section-title' => '',
				'section-callback' => '',
				'page-slug'		=> 'inpursuit-email-templates'
			],

			[
				'section-id' 	=> 'inpursuit_email_field_section',
				'section-title' => '',
				'section-callback' => '',
				'page-slug'		=> 'inpursuit-email-fields'
			],

			[
				'section-id' 	=> 'inpursuit_cron_settings_section',
				'section-title' => '',
				'section-callback' => '',
				'page-slug'		=> 'inpursuit-cron-settings'
			],
		];

		foreach ($section_args as $key => $option) {
			$this->registerSection($option);
		}


		//register setting

		$setting_args = [
			[
				'page-slug' 	=> 'inpursuit-email-templates',
				'setting-name' 	=> 'inpursuit_settings_bday_template',
			    'type-args' =>  [
					             'type' => 'string',
					             'sanitize_callback' => 'sanitize_textarea_field',
					             'default' => ''
					        	] 
			],

			[
				'page-slug' 	=> 'inpursuit-email-templates',
				'setting-name' 	=> 'inpursuit_settings_marriage_template',
			    'type-args' =>  [
					             'type' => 'string',
					             'sanitize_callback' => 'sanitize_textarea_field',
					             'default' => ''
					        	] 
			],

			[
				'page-slug' 	=> 'inpursuit-email-fields',
				'setting-name' 	=> 'inpursuit_settings_email_from',
			    'type-args' =>  [
					             'type' => 'string',
					             'sanitize_callback' => 'sanitize_text_field',
					             'default' => ''
					        	] 
			],

			[
				'page-slug' 	=> 'inpursuit-email-fields',
				'setting-name' 	=> 'inpursuit_settings_email_subject',
			    'type-args' =>  [
					             'type' => 'string',
					             'sanitize_callback' => 'sanitize_text_field',
					             'default' => ''
					        	] 
			],

			[
				'page-slug' 	=> 'inpursuit-cron-settings',
				'setting-name' 	=> 'inpursuit_settings_cron_time',
			    'type-args' =>  [
					             'type' => 'string',
					             'sanitize_callback' => 'sanitize_text_field',
					             'default' => ''
					        	] 
			],
		];

		foreach ($setting_args as $key => $option) {
			$this->registerSetting($option);
		}

		
		//register setting field

		$settings_fields_args = [
			[
				'setting-name' => 'inpursuit_settings_bday_template', 
				'field-title'  => 'Birthday Email Template', 
				'field-callback' =>	[$this, 'textareaFieldCb'], 
				'page-slug'	   => 'inpursuit-email-templates', 
				'section-id'   => 'inpursuit_email_template_section',
				'field-args' => [
							 	 'label_for' => 'inpursuit_settings_bday_template',
							    ],	
			],

			[
				'setting-name' => 'inpursuit_settings_marriage_template', 
				'field-title'  => 'Marriage Anniversary Email Template', 
				'field-callback' =>	[$this, 'textareaFieldCb'], 
				'page-slug'	   => 'inpursuit-email-templates', 
				'section-id'   => 'inpursuit_email_template_section',
				'field-args' => [
							 	 'label_for' => 'inpursuit_settings_marriage_template',
							    ],	
			],

			[
				'setting-name' => 'inpursuit_settings_email_from', 
				'field-title'  => 'Email From', 
				'field-callback' =>	[$this, 'textFieldCb'], 
				'page-slug'	   => 'inpursuit-email-fields', 
				'section-id'   => 'inpursuit_email_field_section',
				'field-args' => [
							 	 'label_for' => 'inpursuit_settings_email_from',
							    ],	
			],

			[
				'setting-name' => 'inpursuit_settings_email_subject', 
				'field-title'  => 'Email Subject', 
				'field-callback' =>	[$this, 'textFieldCb'], 
				'page-slug'	   => 'inpursuit-email-fields', 
				'section-id'   => 'inpursuit_email_field_section',
				'field-args' => [
							 	 'label_for' => 'inpursuit_settings_email_subject',	
							    ],	
			],

			[
				'setting-name' => 'inpursuit_settings_cron_time', 
				'field-title'  => 'Time to Schedule Email', 
				'field-callback' =>	[$this, 'textFieldCb'], 
				'page-slug'	   => 'inpursuit-cron-settings', 
				'section-id'   => 'inpursuit_cron_settings_section',
				'field-args' => [
							 	 'label_for' 	=> 'inpursuit_settings_cron_time',
								 'class'	 	=> '',
								 'placeholder' 	=> 'HH:MM:SS'
							    ],	
			],
		];

	
		foreach ($settings_fields_args as $key => $option) {
			$this->registerSettingField($option);
		}

	}


	/**
	 * Wrapper function for registering section
	 */
	public function registerSection($arg)
	{
		add_settings_section(
			$arg['section-id'], 
			$arg['section-title'], 
			$arg['section-callback'], 
			$arg['page-slug']
		);
	}


	/**
	 * Wrapper function for registering a Setting
	 */		
	public function registerSetting($arg)
	{
		register_setting(
			$arg['page-slug'], 
			$arg['setting-name'], 
			$arg['type-args']
		);
	}


	/**
	 * Wrapper function for registering Settings Field
	 */
	public function registerSettingField($arg)
	{
		add_settings_field(
			$arg['setting-name'], 
			$arg['field-title'], 
			$arg['field-callback'], 
			$arg['page-slug'], 
			$arg['section-id'], 
			$arg['field-args']
		);
	}


	/**
	 * Callback for rendering textarea field for registered setting
	 */
	public function textareaFieldCb($args)
	{ 
		$option =  get_option($args['label_for']); ?>

		<textarea name="<?php echo $args['label_for'];?>" class="large-text" rows="10"><?php echo isset($option) ? $option : '';  ?></textarea>
			
		<?php
	}

	/**
	 * Callback for rendering text input field for registered setting
	 */
	public function textFieldCb($args)
	{
		$option = get_option($args['label_for']); ?>

		<input type="text" name="<?php echo $args['label_for'];?>" class="<?php echo isset($args['class']) ? $args['class'] : 'regular-text';?>" value="<?php echo isset($option) ? $option : ''; ?>"  placeholder="<?php echo isset($args['placeholder']) ? $args['placeholder'] : ''; ?>"/>

		<?php
	}

}

INPURSUIT_ADMIN_SETTINGS::getInstance();