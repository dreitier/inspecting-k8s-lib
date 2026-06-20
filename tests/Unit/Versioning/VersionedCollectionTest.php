<?php

use Dreitier\Alm\Inspecting\Versioning\Version;
use Dreitier\Alm\Inspecting\Versioning\VersionedCollection;

describe('default array access logic', function () {
	beforeEach()->with([
		function() {
			$a = Version::of('3.0.0');
			$b = Version::of('2.0.0');
			$c = Version::of('5.0.0');

			$sut = VersionedCollection::of([$a, $b, $c]);

			return [$sut, $a, $b, $c];
		}
	]);

	test('count works', function (VersionedCollection $sut, Version $a, Version $b, Version $c) {
		expect(sizeof($sut))->toBe(3);
	});


	test('first works', function (VersionedCollection $sut, Version $a, Version $b, Version $c) {
		expect($sut->first())->toBe($a);
	});

	test('last works', function (VersionedCollection $sut, Version $a, Version $b, Version $c) {
		expect($sut->last())->toBe($c);
	});

	it('can be sorted by newest', function (VersionedCollection $sut, Version $a, Version $b, Version $c) {
		expect($sut->highestFirst()->first())->toBe($c);
	});

	it('can be sorted by oldest first', function (VersionedCollection $sut, Version $a, Version $b, Version $c) {
		expect($sut->lowestFirst()->first())->toBe($b);
	});

	it('finds versions lower than', function (VersionedCollection $sut, Version $a, Version $b, Version $c) {
		$sut = $sut->findLowerThan(Version::of('4.0.0'))->highestFirst();
		expect($sut->count())->toBe(2);
		expect($sut[0])->toBe($a);
		expect($sut[1])->toBe($b);
	});

	it('finds versions higher than', function (VersionedCollection $sut, Version $a, Version $b, Version $c) {
		$sut = $sut->findHigherThan(Version::of('4.0.0'))->highestFirst();
		expect($sut->count())->toBe(1);
		expect($sut[0])->toBe($c);
	});

	it('finds versions higher or equal than', function (VersionedCollection $sut, Version $a, Version $b, Version $c) {
		$sut = $sut->findHigherOrEqualThan(Version::of('3.0.0'))->highestFirst();
		expect($sut->count())->toBe(2);
		expect($sut[0])->toBe($c);
		expect($sut[1])->toBe($a);
	});
});

