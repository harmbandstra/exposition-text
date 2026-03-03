<?php declare(strict_types=1);

namespace OpenMetricsPhp\Exposition\Text\Collections;

use OpenMetricsPhp\Exposition\Text\Interfaces\NamesMetric;
use OpenMetricsPhp\Exposition\Text\Metrics\Info;
use Traversable;
use function count;

final class InfoCollection extends AbstractMetricCollection
{
	/** @var array<Info> */
	private $infos = [];

	public static function withMetricName( NamesMetric $metricName ) : self
	{
		return new self( $metricName, 'info' );
	}

	public static function fromInfos( NamesMetric $metricName, Info $info, Info ...$infos ) : self
	{
		$collection = self::withMetricName( $metricName );
		$collection->add( $info, ...$infos );

		return $collection;
	}

	public function add( Info $info, Info ...$infos ) : void
	{
		$this->infos = array_merge( $this->infos, [$info], $infos );
	}

	public function count() : int
	{
		return count( $this->infos );
	}

	/**
	 * @return Traversable<string>
	 */
	public function getMetricLines() : Traversable
	{
		if ( 0 === $this->count() )
		{
			return;
		}

		yield $this->getTypeString();

		$helpString = $this->getHelpString();
		if ( '' !== $helpString )
		{
			yield $helpString;
		}

		foreach ( $this->infos as $info )
		{
			yield $this->getMetricName()->toString() . $info->getSampleString();
		}
	}

	public function getMetricsString() : string
	{
		return implode( "\n", iterator_to_array( $this->getMetricLines(), false ) );
	}
}
