<?php
declare(strict_types=1);

namespace ItalyStrap\Customizer;

use ItalyStrap\Empress\AurynConfigInterface;
use ItalyStrap\Empress\Injector;
use ItalyStrap\Event\EventDispatcherInterface;

class CustomizerProviderExtension implements \ItalyStrap\Empress\Extension {

	private EventDispatcherInterface $dispatcher;
	private Injector $injector;

	public function __construct(
		EventDispatcherInterface $dispatcher,
		Injector $injector
	) {
		$this->dispatcher = $dispatcher;
		$this->injector = $injector;
	}

	/**
	 * @inheritDoc
	 */
	public function name(): string {
		return self::class;
	}

	/**
	 * @inheritDoc
	 */
	public function execute(AurynConfigInterface $application) {
		$this->dispatcher->addListener(
			'customize_register',
			function (\WP_Customize_Manager $manager) use ( $application ): void {
				$this->injector->share($manager);
				$application->walk( $this->name(), [$this, 'walk'] );
			},
			99,
			3
		);
	}

	public function walk( string $class, $index_or_optionName, Injector $injector ): void {
		$object = $injector->make($class);
		$object->display();
	}
}
