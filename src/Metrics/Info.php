<?php declare(strict_types=1);

namespace OpenMetricsPhp\Exposition\Text\Metrics;

use OpenMetricsPhp\Exposition\Text\Collections\LabelCollection;
use OpenMetricsPhp\Exposition\Text\Interfaces\CollectsLabels;
use OpenMetricsPhp\Exposition\Text\Interfaces\ProvidesNamedValue;
use OpenMetricsPhp\Exposition\Text\Interfaces\ProvidesSampleString;

final class Info implements ProvidesSampleString
{
	/** @var CollectsLabels */
	private $labels;

	private function __construct()
	{
		$this->labels = LabelCollection::new();
	}

	public static function new() : self
	{
		return new self();
	}

	public function withLabels( ProvidesNamedValue $label, ProvidesNamedValue ...$labels ) : self
	{
		$this->addLabels( $label, ...$labels );

		return $this;
	}

	public function withLabelCollection( CollectsLabels $labels ) : self
	{
		foreach ( $labels as $label )
		{
			$this->addLabels( $label );
		}

		return $this;
	}

	public function addLabels( ProvidesNamedValue $label, ProvidesNamedValue ...$labels ) : void
	{
		$this->labels->add( $label, ...$labels );
	}

	public function getSampleString() : string
	{
		return sprintf(
			'_info%s 1',
			$this->labels->getCombinedLabelString()
		);
	}
}
