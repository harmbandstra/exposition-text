<?php declare(strict_types=1);

namespace OpenMetricsPhp\Exposition\Text\Tests\Unit\Collections;

use OpenMetricsPhp\Exposition\Text\Collections\InfoCollection;
use OpenMetricsPhp\Exposition\Text\Exceptions\InvalidArgumentException;
use OpenMetricsPhp\Exposition\Text\Metrics\Info;
use OpenMetricsPhp\Exposition\Text\Types\Label;
use OpenMetricsPhp\Exposition\Text\Types\MetricName;
use PHPUnit\Framework\TestCase;

final class InfoCollectionTest extends TestCase
{
	/**
	 * @throws InvalidArgumentException
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testCanGetNewInstance() : void
	{
		$metricName = MetricName::fromString( 'unit_test_metric' );
		$collection = InfoCollection::withMetricName( $metricName );

		$this->assertCount( 0, $collection );
		$this->assertSame( 0, $collection->count() );
	}

	/**
	 * @throws InvalidArgumentException
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testCanCount() : void
	{
		$metricName = MetricName::fromString( 'unit_test_metric' );
		$collection = InfoCollection::withMetricName( $metricName );

		$this->assertCount( 0, $collection );
		$this->assertSame( 0, $collection->count() );

		$collection->add( Info::new()->withLabels( Label::fromNameAndValue( 'version', '1.0' ) ) );
		$collection->add( Info::new()->withLabels( Label::fromNameAndValue( 'version', '2.0' ) ) );

		$this->assertCount( 2, $collection );
		$this->assertSame( 2, $collection->count() );
	}

	/**
	 * @throws InvalidArgumentException
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testCanGetNewInstanceFromInfos() : void
	{
		$metricName = MetricName::fromString( 'unit_test_metric' );
		$infos      = [
			Info::new()->withLabels( Label::fromNameAndValue( 'version', '1.0' ) ),
			Info::new()->withLabels( Label::fromNameAndValue( 'version', '2.0' ) ),
		];

		$collection = InfoCollection::fromInfos( $metricName, ...$infos );

		$this->assertCount( 2, $collection );
		$this->assertSame( 2, $collection->count() );
	}

	/**
	 * @throws InvalidArgumentException
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testCanAddInfos() : void
	{
		$metricName = MetricName::fromString( 'unit_test_metric' );
		$infos      = [
			Info::new()->withLabels( Label::fromNameAndValue( 'version', '1.0' ) ),
			Info::new()->withLabels( Label::fromNameAndValue( 'version', '2.0' ) ),
		];

		$collection = InfoCollection::withMetricName( $metricName );
		$collection->add( ...$infos );
		$collection->add( Info::new()->withLabels( Label::fromNameAndValue( 'version', '3.0' ) ) );

		$this->assertCount( 3, $collection );
		$this->assertSame( 3, $collection->count() );
	}

	/**
	 * @throws InvalidArgumentException
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testCanGetMetricStrings() : void
	{
		$metricName            = MetricName::fromString( 'unit_test_metric' );
		$expectedMetricStrings = "# TYPE unit_test_metric info\n";
		$expectedMetricStrings .= "# HELP unit_test_metric Information about the test application\n";
		$expectedMetricStrings .= "unit_test_metric_info{version=\"8.2.7\",name=\"pretty name\"} 1\n";
		$expectedMetricStrings .= 'unit_test_metric_info{env="production"} 1';

		$collection = InfoCollection::fromInfos(
			$metricName,
			Info::new()->withLabels(
				Label::fromNameAndValue( 'version', '8.2.7' ),
				Label::fromNameAndValue( 'name', 'pretty name' )
			),
			Info::new()->withLabels(
				Label::fromNameAndValue( 'env', 'production' )
			)
		)->withHelp(
			'Information about the test application'
		);

		$this->assertSame( $expectedMetricStrings, $collection->getMetricsString() );
	}

	/**
	 * @throws InvalidArgumentException
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testMetricsStringIsEmptyIfNoInfosWereAdded() : void
	{
		$metricName = MetricName::fromString( 'unit_test_metric' );
		$collection = InfoCollection::withMetricName( $metricName );

		$this->assertSame( '', $collection->getMetricsString() );
	}

	/**
	 * @throws InvalidArgumentException
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testHelpStringIsOmittedIfNotSet() : void
	{
		$metricName = MetricName::fromString( 'unit_test_metric' );
		$collection = InfoCollection::fromInfos(
			$metricName,
			Info::new()->withLabels( Label::fromNameAndValue( 'version', '1.0' ) )
		);

		$expectedMetricString = "# TYPE unit_test_metric info\n";
		$expectedMetricString .= 'unit_test_metric_info{version="1.0"} 1';

		$this->assertSame( $expectedMetricString, $collection->getMetricsString() );
	}
}
