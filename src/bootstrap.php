<?php
/**
 * ItalyStrap Bootstrap File
 *
 * This is the bootstrapping file for the ItalyStrap framework.
 *
 *
 * @package ItalyStrap
 * @since 4.0.0
 *
 * @TODO https://github.com/understrap/understrap/issues/585 for gutenberg integration
 */
declare(strict_types=1);

namespace ItalyStrap;

use Auryn\InjectorException;
use ItalyStrap\Config\ConfigFactory;
use ItalyStrap\Config\ConfigInterface;
use ItalyStrap\Empress\AurynConfig;
use ItalyStrap\Event\SubscriberRegister;
use ItalyStrap\Event\SubscribersConfigExtension;
use ItalyStrap\Event\EventDispatcher;
use ItalyStrap\Event\EventDispatcherInterface;
use Throwable;
use function ItalyStrap\Config\get_config_file_content;
use function ItalyStrap\Core\set_default_constants;
use function ItalyStrap\Factory\get_config;
use function ItalyStrap\Factory\injector;

/**
 * ========================================================================
 *
 * Autoload theme core files.
 *
 * ========================================================================
 */
$autoload_theme_files = [
	'../vendor/autoload.php',
	'../functions/autoload.php',
];

foreach ( $autoload_theme_files as $file ) {
	require __DIR__ . DIRECTORY_SEPARATOR . $file;
}

try {
	$injector = injector();

	$injector->share( $injector );

	$injector
		->alias( EventDispatcherInterface::class, EventDispatcher::class )
		->share( EventDispatcher::class )
		->share( SubscriberRegister::class );

	/**
	 * ========================================================================
	 *
	 * Set the default theme constant
	 *
	 * @see /config/constants.php
	 *
	 * @var array $constants
	 *
	 * ========================================================================
	 */
	$constants = set_default_constants( get_config_file_content( 'constants' ) );

	get_config()->merge(
		$constants, // This need to be set as soon as possible
		get_config_file_content( 'default' ),
		$theme_mods ?? (array) \get_theme_mods()
	);

	unset( $theme_mods, $constants );

	$subscriber_config = $injector->make( SubscribersConfigExtension::class, [
		':config'	=> get_config(),
	] );

	$dependencies_collection = get_config_file_content( 'dependencies' );
	$dependencies_collection[ SubscribersConfigExtension::SUBSCRIBERS ] = \array_merge(
		$dependencies_collection[ SubscribersConfigExtension::SUBSCRIBERS ],
		get_config_file_content( 'dependencies-admin' ),
		get_config_file_content( 'dependencies-front' )
	);

	/** @var ConfigInterface $dependencies */
	$dependencies = ConfigFactory::make($dependencies_collection);

	$injector_config = $injector->make( AurynConfig::class, [
		':dependencies'	=> $dependencies
	] );

	$injector_config->extend( $subscriber_config );

	$event_dispatcher = $injector->make( EventDispatcher::class );

	/**
	 * ========================================================================
	 *
	 * Load the framework
	 *
	 * ========================================================================
	 */
	$event_dispatcher->addListener( 'italystrap_theme_load', function () use ( $injector_config ) {
		$injector_config->resolve();
	} );

	/**
	 * ========================================================================
	 *
	 * This will load the framework after setup theme
	 *
	 * ========================================================================
	 */
	$event_dispatcher->addListener( 'after_setup_theme', function () use ( $injector, $event_dispatcher ) {

		/**
		 * Fires before ItalyStrap theme load.
		 *
		 * @since 4.0.0
		 */
		$event_dispatcher->dispatch( 'italystrap_theme_will_load', $injector );

		/**
		 * Fires once ItalyStrap theme is loading.
		 *
		 * @since 4.0.0
		 */
		$event_dispatcher->dispatch( 'italystrap_theme_load', $injector );

		/**
		 * Fires once ItalyStrap theme has loaded.
		 *
		 * @since 4.0.0
		 */
		$event_dispatcher->dispatch( 'italystrap_theme_loaded', $injector );
	}, 20 );
} catch ( InjectorException $exception ) {
	\_doing_it_wrong( \get_class( injector() ), $exception->getMessage(), '4.0.0' );
} catch ( Throwable $exception ) {
	\_doing_it_wrong( 'General error.', $exception->getMessage(), '4.0.0' );
}
