<?php

class INPURSUIT_ADMIN_SETTINGS extends INPURSUIT_BASE {

	function __construct()
	{
		$this->setNavigationTabs();
		add_action( 'admin_menu', [$this, 'registerMenu'] );
		add_action( 'admin_init', [$this, 'settingsOptionsRegistration'] );
	}


	/**
	 * Callback function for registering submenu
	 */
	public function registerMenu()
	{
		add_submenu_page(
			'inpursuit',
			'Settings',
      'Settings',
      'manage_options',
      'inpursuit-settings',
      [$this, 'settingsTemplateCallback']
	   );
	}


	/**
	 * Initialize options for Tabbed Navigation.
	 * Update this attribute if new tab needs to be added
	 * Match the 'section-page' value to 'page-slug' value of section-settings-attributes
	 */
	public function setNavigationTabs(){
		$this->navigationTabs = array();
	}


	/*
	* Returns array config for TabbedNavigation
	*/
	public function getNavigationTabs(){ return $this->navigationTabs; }

	public function settingsTemplateCallback(){
	?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<?php
		  	$navigation_tabs = apply_filters( 'inpursuit_settings_tabs', $this->getNavigationTabs() );

				// CHECK IF ACTIVE TAB IS PASSED IN THE URL
				// OR ELSE THE SLUG OF THE FIRST TAB
		  	$active_tab = '';
				if( isset( $_GET['tab'] ) ){ $active_tab = $_GET['tab']; }
				elseif( count( $navigation_tabs ) ){
					$active_tab = $navigation_tabs[0]['slug'];
				}
	    ?>

		  <h2 class="nav-tab-wrapper">
			<?php
				foreach ($navigation_tabs as $tab) {
					$active_class = ($tab['slug'] == $active_tab) ? 'nav-tab-active' : '';

					$page = $_GET['page'];
					$tab_slug = $tab['slug'];
					$tab_title = $tab['title'];

					echo "<a href='?page=$page&tab=$tab_slug' class='nav-tab $active_class'>$tab_title</a>";
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


	/*
	* Settings Options Registeration
	*/
	public function settingsOptionsRegistration(){

		//section settings attributes setup
		$section_args = apply_filters( 'inpursuit_settings_sections', array() );

		foreach ( $section_args as $key => $option ) {
			$this->registerSection($option);
		}

		$setting_args = apply_filters( 'inpursuit_settings_args', array() );
		foreach ( $setting_args as $key => $option ) {
			$this->registerSetting($option);
		}

		$settings_fields_args = apply_filters( 'inpursuit_settings_fields_args', array() );
		foreach ( $settings_fields_args as $key => $option ) {
			$this->registerSettingField($option);
		}

	}


	/*
	* Wrapper function for registering section
	*/
	public function registerSection( $arg ){
		add_settings_section(
			$arg['section-id'],
			$arg['section-title'],
			$arg['section-callback'],
			$arg['page-slug']
		);
	}


	/*
	* Wrapper function for registering a Setting
	*/
	public function registerSetting( $arg ){
		register_setting(
			$arg['page-slug'],
			$arg['setting-name'],
			$arg['type-args']
		);
	}


	/**
	 * Wrapper function for registering Settings Field
	 */
	public function registerSettingField( $arg ){
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
	public function textareaFieldCb( $args ){
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
