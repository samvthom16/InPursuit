<?php

class INPURSUIT_ADMIN_SETTINGS extends INPURSUIT_BASE {

	function __construct()
	{
		add_action( 'admin_menu', [$this, 'registerSetttingMenu'] );
		add_action( 'admin_init', [$this, 'settingsOptionsRegistration'] );
	}


	public function registerSetttingMenu()
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



	public function settingsTemplateCallback()
	{
		?>
	    <div class="wrap">
	      <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	      <form action="options.php" method="post">
	        <?php

	        // output security fields
	        settings_fields( 'inpursuit-settings' );
	        
	        // output setting sections
	        do_settings_sections( 'inpursuit-settings' );
	        
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

		//register section

		$section_args = [
			[
				'section-id' 	=> 'inpursuit_settings_email_section',
				'section-title' => '',
				'section-callback' => '',
				'page-slug'		=> 'inpursuit-settings'
			],
		];

		foreach ($section_args as $key => $option) {
			$this->registerSection($option);
		}


		//register setting

		$setting_args = [
			[
				'page-slug' 	=> 'inpursuit-settings',
				'setting-name' 	=> 'inpursuit_settings_bday_template',
			    'type-args' =>  [
					             'type' => 'string',
					             'sanitize_callback' => 'sanitize_textarea_field',
					             'default' => ''
					        	] 
			],

			[
				'page-slug' 	=> 'inpursuit-settings',
				'setting-name' 	=> 'inpursuit_settings_marriage_template',
			    'type-args' =>  [
					             'type' => 'string',
					             'sanitize_callback' => 'sanitize_textarea_field',
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
				'field-callback' =>	[$this, 'birthdayEmailTemplateFieldCb'], 
				'page-slug'	   => 'inpursuit-settings', 
				'section-id'   => 'inpursuit_settings_email_section',
				'field-args' => [
							 	 'label_for' => 'inpursuit_settings_bday_template',
							    ],	
			],

			[
				'setting-name' => 'inpursuit_settings_marriage_template', 
				'field-title'  => 'Marriage Anniversary Email Template', 
				'field-callback' =>	[$this, 'marriageEmailTemplateFieldCb'], 
				'page-slug'	   => 'inpursuit-settings', 
				'section-id'   => 'inpursuit_settings_email_section',
				'field-args' => [
							 	 'label_for' => 'inpursuit_settings_marriage_template',
							    ],	
			],
		];

		foreach ($settings_fields_args as $key => $option) {
			$this->registerSettingField($option);
		}


	}


	public function registerSection($arg)
	{
		add_settings_section(
			$arg['section-id'], 
			$arg['section-title'], 
			$arg['section-callback'], 
			$arg['page-slug']
		);
	}


	public function registerSetting($arg)
	{
		register_setting(
			$arg['page-slug'], 
			$arg['setting-name'], 
			$arg['type-args']
		);
	}

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



	public function birthdayEmailTemplateFieldCb($args)
	{ 
		$options =  get_option($args['label_for']); ?>

		<textarea name="<?php echo $args['label_for'];?>" class="large-text" rows="10"><?php echo isset($options) ? $options : '';  ?></textarea>
			
		<?php
	}


	public function marriageEmailTemplateFieldCb($args)
	{ 
		$options =  get_option($args['label_for']); ?>

		<textarea name="<?php echo $args['label_for'];?>" class="large-text" rows="10"><?php echo isset($options) ? $options : '';  ?></textarea>
			
		<?php
	}

}

INPURSUIT_ADMIN_SETTINGS::getInstance();