<?php declare(strict_types=1);

namespace OpenMetricsPhp\Exposition\Text\Tests\Unit\Metrics;

use OpenMetricsPhp\Exposition\Text\Collections\LabelCollection;
use OpenMetricsPhp\Exposition\Text\Metrics\Info;
use OpenMetricsPhp\Exposition\Text\Types\Label;
use PHPUnit\Framework\TestCase;

final class InfoTest extends TestCase
{
	/**
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testCanGetInstance() : void
	{
		$info = Info::new();

		$this->assertInstanceOf( Info::class, $info );
	}

	/**
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testGetSampleString() : void
	{
		$expectedSampleString = '_info 1';

		$info = Info::new();

		$this->assertSame( $expectedSampleString, $info->getSampleString() );
	}

	/**
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testAddLabels() : void
	{
		$expectedSampleStringWithoutLabels   = '_info 1';
		$expectedSampleStringWithOneLabel    = '_info{unit_test="123"} 1';
		$expectedSampleStringWithThreeLabels = '_info{unit_test="123",test_unit="456",label_last="789"} 1';

		$info = Info::new();

		$this->assertSame( $expectedSampleStringWithoutLabels, $info->getSampleString() );

		$info->addLabels( Label::fromNameAndValue( 'unit_test', '123' ) );

		$this->assertSame( $expectedSampleStringWithOneLabel, $info->getSampleString() );

		$info->addLabels(
			Label::fromNameAndValue( 'test_unit', '456' ),
			Label::fromNameAndValue( 'label_last', '789' )
		);

		$this->assertSame( $expectedSampleStringWithThreeLabels, $info->getSampleString() );
	}

	/**
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testCanGetInfoWithLabels() : void
	{
		$info = Info::new()
		           ->withLabels(
			           Label::fromNameAndValue( 'unit', 'test' ),
			           Label::fromNameAndValue( 'test', 'unit' )
		           );

		$expectedSampleString = '_info{unit="test",test="unit"} 1';

		$this->assertSame( $expectedSampleString, $info->getSampleString() );
	}

	/**
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testCanGetInfoWithLabelCollection() : void
	{
		$info = Info::new()
		           ->withLabelCollection(
			           LabelCollection::fromAssocArray(
				           [
					           'unit' => 'test',
					           'test' => 'unit',
				           ]
			           )
		           );

		$expectedSampleString = '_info{unit="test",test="unit"} 1';

		$this->assertSame( $expectedSampleString, $info->getSampleString() );
	}
}
