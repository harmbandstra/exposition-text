<?php declare(strict_types=1);

namespace YourVendor\YourProject;

use OpenMetricsPhp\Exposition\Text\Collections\InfoCollection;
use OpenMetricsPhp\Exposition\Text\Collections\LabelCollection;
use OpenMetricsPhp\Exposition\Text\Metrics\Info;
use OpenMetricsPhp\Exposition\Text\Types\Label;
use OpenMetricsPhp\Exposition\Text\Types\MetricName;

require __DIR__ . '/../vendor/autoload.php';

$infos = InfoCollection::fromInfos(
	MetricName::fromString( 'your_app' ),
	Info::new()->withLabels(
		Label::fromNameAndValue( 'version', '8.2.7' ),
		Label::fromNameAndValue( 'name', 'pretty name' )
	)
)->withHelp( 'Information about the application.' );

# Add infos after creating the collection
$infos->add(
	Info::new()->withLabels(
		Label::fromNameAndValue( 'env', 'production' )
	),
	Info::new()->withLabels(
		Label::fromLabelString( 'build_revision="abc123"' )
	)
);

# Prepare labels upfront
$labels = LabelCollection::fromAssocArray(
	[
		'compiler' => 'gcc',
		'compiler_version' => '12.2.0',
	]
);

$infos->add(
	Info::new()->withLabelCollection( $labels )
);

echo $infos->getMetricsString();
