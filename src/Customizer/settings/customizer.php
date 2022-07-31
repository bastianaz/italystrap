<?php
declare(strict_types=1);

namespace ItalyStrap\Customizer;

use WP_Customize_Manager;
use WP_Customize_Control;
use WP_Customize_Color_Control;
use	WP_Customize_Media_Control;

use ItalyStrap\Core as Core;
use	ItalyStrap\Customizer\Control\Textarea;

/**
 * Let's make some stuff use live preview JS...
 */
$manager->get_setting( 'blogname' )->transport = 'postMessage';
$manager->get_setting( 'blogdescription' )->transport = 'postMessage';

/**
 * Add new panel for ItalyStrap theme options
 */
$manager->add_panel(
	$this->panel,
	array(
		'title'			=> sprintf(
			__( '%s Options', 'italystrap' ),
			\wp_get_theme()->get( 'Name' )
		),
		// 'description'	=> 'add_panel', // Include html tags such as <p>.
		// 'priority'		=> 160, // Mixed with top-level-section hierarchy.
		'priority'		=> 10, // Mixed with top-level-section hierarchy.
	)
);
