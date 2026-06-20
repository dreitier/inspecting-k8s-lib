<?php

use Dreitier\Alm\Inspecting\Versioning\Version;

test('raw version is correct', function () {
	$version = Version::of('5.41.9');

	expect($version->raw)->toBe('5.41.9');
});


test('major, minor, patch is correctly identified', function () {
	$version = Version::of('5.41.9');

	expect($version->major)->toBe(5);
	expect($version->minor)->toBe(41);
	expect($version->patch)->toBe(9);
	expect($version->buildnumber)->toBe(0);
});

test('special build information from Rancher can be parsed', function () {
	$version = Version::of('5.41.9+up1.0.2');

	expect($version->main())->toBe('5.41.9');
	expect($version->flavor)->toBe('up1.0.2');
});

test('flavor can be extracted', function () {
	$version = Version::of('5.41.9+up1.0.2');

	expect($version->flavorAsVersion()->main())->toBe('1.0.2');
});

test('two versions can be checked for equality', function () {
	$a = Version::of('5.41.9+up1.0.2');
	$b = Version::of('5.41.9+up1.0.2');

	expect($a->compare($b))->toBe(0);
});

test('two versions can be checked for LESS THAN in major', function () {
	$a = Version::of('5.41.9+up1.0.2');
	$b = Version::of('6.41.9+up1.0.2');

	expect($a->compare($b))->toBe(-1);
});

test('two versions can be checked for GREATER THAN in major', function () {
	$a = Version::of('5.41.9+up1.0.2');
	$b = Version::of('4.41.9+up1.0.2');

	expect($a->compare($b))->toBe(1);
});

test('two versions can be checked for LESS THAN in minor', function () {
	$a = Version::of('5.39.9+up1.0.2');
	$b = Version::of('5.40.9+up1.0.2');

	expect($a->compare($b))->toBe(-1);
});

test('two versions can be checked for GREATER THAN in minor', function () {
	$a = Version::of('5.40.9+up1.0.2');
	$b = Version::of('5.39.9+up1.0.2');

	expect($a->compare($b))->toBe(1);
});

test('two versions can be checked for LESS THAN in patch', function () {
	$a = Version::of('5.41.9+up1.0.2');
	$b = Version::of('5.41.10+up1.0.2');

	expect($a->compare($b))->toBe(-1);
});

test('two versions can be checked for GREATER THAN in patch', function () {
	$a = Version::of('5.41.9+up1.0.2');
	$b = Version::of('5.41.8+up1.0.2');

	expect($a->compare($b))->toBe(1);
});

test('isAfter works', function () {
	$a = Version::of('5.41.9+up1.0.2');
	$b = Version::of('5.41.8+up1.0.2');

	expect($a->isHigherThan($b))->toBe(true);
});

test('isBefore works', function () {
	$a = Version::of('5.41.9+up1.0.2');
	$b = Version::of('5.41.10+up1.0.2');

	expect($a->isLowerThan($b))->toBe(true);
});

test('isEqual works', function () {
	$a = Version::of('5.41.9+up1.0.2');
	$b = Version::of('5.41.9+up1.0.2');

	expect($a->isEqual($b))->toBe(true);
});
