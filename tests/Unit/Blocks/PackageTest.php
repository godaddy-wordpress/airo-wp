<?php
/**
 * Blocks Package tests.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Blocks;

use Brain\Monkey\Actions;
use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Blocks\Package;
use GoDaddy\WordPress\Plugins\AiroWp\Container;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

/**
 * Tests for Blocks\Package boot.
 */
final class PackageTest extends TestCase {

	/**
	 * Package::init() is callable (satisfies PackageInterface).
	 */
	public function test_init_is_callable(): void {
		$this->assertTrue( is_callable( [ Package::class, 'init' ] ) );
	}

	/**
	 * When DSG is inactive, init() hooks register_blocks and register_patterns onto 'init'.
	 */
	public function test_init_hooks_register_methods_when_designsetgo_inactive(): void {
		Functions\when( 'is_plugin_active' )->justReturn( false );

		Actions\expectAdded( 'init' )
			->with(
				\Mockery::on(
					function ( $cb ) {
						return is_array( $cb ) && $cb[0] instanceof Package && 'register_blocks' === $cb[1];
					}
				)
			)
			->once();
		Actions\expectAdded( 'init' )
			->with(
				\Mockery::on(
					function ( $cb ) {
						return is_array( $cb ) && $cb[0] instanceof Package && 'register_patterns' === $cb[1];
					}
				)
			)
			->once();

		Package::init( new Container() );

		$this->addToAssertionCount( 1 );
	}

	/**
	 * When DSG is active, init() returns early and adds no 'init' hooks.
	 */
	public function test_init_returns_early_when_designsetgo_active(): void {
		Functions\when( 'is_plugin_active' )->justReturn( true );

		Actions\expectAdded( 'init' )->never();

		Package::init( new Container() );

		$this->addToAssertionCount( 1 );
	}
}
