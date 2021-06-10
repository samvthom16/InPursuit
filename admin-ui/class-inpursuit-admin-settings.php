<?php

class INPURSUIT_ADMIN_SETTINGS extends INPURSUIT_BASE {

	function __construct()
	{
		add_action( 'admin_menu', [$this, 'registerSetttingMenu'] );
		add_action( 'admin_init', [$this, 'settingsOptionsRegistartion'] );
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
	public function settingsOptionsRegistartion()
	{


		add_settings_section( 
			'inpursuit_settings_email_section',
			'', 
			'', 
			'inpursuit-settings'
		);


		register_setting(
			'inpursuit-settings',
			'inpursuit_settings_bday_template',
	        [
	            'type' => 'string',
	            'sanitize_callback' => 'sanitize_textarea_field',
	            'default' => ''
	        ]
		);


		add_settings_field( 
			'inpursuit_settings_bday_template', 
			'Birthday Email Template', 
			[$this, 'birthdayEmailTemplateFieldCallback'], 
			'inpursuit-settings', 
			'inpursuit_settings_email_section',
			[
			 	'label_for' => 'inpursuit_settings_bday_template',
			],
		);

	}



	public function birthdayEmailTemplateFieldCallback($args)
	{ 
		$options =  get_option($args['label_for']); ?>

		<textarea name="<?php echo $args['label_for'];?>" class="large-text" rows="10"><?php echo isset($options) ? $options : '';  ?></textarea>
			
		<?php
	}

}

new INPURSUIT_ADMIN_SETTINGS();