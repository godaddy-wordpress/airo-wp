<?php
/**
 * Rest Package tests.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Rest;

use Brain\Monkey\Actions;
use GoDaddy\WordPress\Plugins\AiroWp\Container;
use GoDaddy\WordPress\Plugins\AiroWp\Internal\DependencyManagement\TestingContainer;
use GoDaddy\WordPress\Plugins\AiroWp\Rest\Endpoints\DraftPages;
use GoDaddy\WordPress\Plugins\AiroWp\Rest\Package;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

/**
 * Tests for Rest\Package boot.
 */
final class PackageTest extends TestCase {

	/**
	 * init() hooks register_routes on rest_api_init for each endpoint.
	 */
	public function test_init_registers_rest_api_init_hook(): void {
		$mock_draft_pages = \Mockery::mock( DraftPages::class );

		$testing_container = new TestingContainer( array() );
		$testing_container->replace( DraftPages::class, $mock_draft_pages );
		$container = new Container( $testing_container );

		Actions\expectAdded( 'rest_api_init' )
			->once()
			->with( array( $mock_draft_pages, 'register_routes' ) );

		Package::init( $container );

		$this->addToAssertionCount( 1 );
	}

	/**
	 * Package::init() is callable (satisfies PackageInterface).
	 */
	public function test_init_is_callable(): void {
		$this->assertTrue( is_callable( array( Package::class, 'init' ) ) );
	}
}
