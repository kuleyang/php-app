<?php


namespace Db\Adapter;

interface AdapterInterface
{
	/**
	 * @return Driver\DriverInterface
	 */
	public function getDriver();

	/**
	 * @return Platform\PlatformInterface
	 */
	public function getPlatform();

	/**
	 * @return Metadata\MetadataInterface
	 */
	public function getMetadata();
}
