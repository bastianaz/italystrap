<?php
declare(strict_types=1);

namespace ItalyStrap\Asset;

use ItalyStrap\Event\SubscriberInterface;
use ItalyStrap\Event\Manager as Event;

class AssetsManager implements SubscriberInterface {

	const ENQUEUE_EVENT_NAME = 'wp_enqueue_scripts';

	/**
	 * @var array Asset
	 */
	private $assets = [];

	/**
	 * @inheritDoc
	 */
	public function getSubscribedEvents(): array {
		return [
			static::ENQUEUE_EVENT_NAME	=> [
				Event::CALLBACK	=> 'register',
			],
		];
	}

	public function withAssets( Asset ...$assets ) {
		$this->assets = \array_merge($assets, $this->assets);
	}

	/**
	 * @inheritDoc
	 */
	public function register(): void {
		\array_walk($this->assets, function ( Asset $asset, $key) {
			$asset->register_all();
		});
	}
}
