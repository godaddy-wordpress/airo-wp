<?php
/**
 * Mcp Package tests.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp;

use Brain\Monkey\Actions;
use Brain\Monkey\Filters;
use GoDaddy\WordPress\Plugins\AiroWp\Container;
use GoDaddy\WordPress\Plugins\AiroWp\Internal\DependencyManagement\TestingContainer;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Infrastructure\AbilitiesApiProxy;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Package;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

/**
 * Tests for Mcp\Package boot.
 */
final class PackageTest extends TestCase {

	/**
	 * Init() calls setup() on the proxy resolved from the container.
	 */
	public function test_init_calls_proxy_setup(): void {
		$mock_proxy = \Mockery::mock( AbilitiesApiProxy::class );
		$mock_proxy->shouldReceive( 'setup' )->once();

		$testing_container = new TestingContainer( array() );
		$testing_container->replace( AbilitiesApiProxy::class, $mock_proxy );
		$container = new Container( $testing_container );

		// McpAdapter::instance() internally calls add_action( 'rest_api_init', ... ).
		Actions\expectAdded( 'rest_api_init' )->atLeast()->once();
		// Package hooks mcp_adapter_init for server creation.
		Actions\expectAdded( 'mcp_adapter_init' )->once();

		Package::init( $container );

		$this->addToAssertionCount( 1 );
	}

	/**
	 * Init() wires both auth compatibility layers.
	 *
	 * application_password_is_api_request is asserted TWICE, deliberately. Two
	 * classes assert api-request status for different reasons and neither can
	 * substitute for the other:
	 *
	 *   RouteAccess            - anchored on the MCP route, so credentials the
	 *                            GoDaddy Launch platform resolves early (notably
	 *                            core's own Basic auth) are still validated
	 *   AppPasswordHeaderAuth  - keyed on the presence of an airowp credential,
	 *                            regardless of route
	 *
	 * If this count ever drops to one, check that the removed layer's case is
	 * genuinely covered rather than merely absent.
	 */
	public function test_init_sets_up_auth_compatibility_layers(): void {
		$container = new Container( new TestingContainer( array() ) );

		// Only these filters are asserted. The adapter's own add_action calls are
		// singleton-guarded, so whether they fire depends on whether an earlier
		// test already built McpAdapter in this process.
		Filters\expectAdded( 'application_password_is_api_request' )->twice();
		Filters\expectAdded( 'gdl_unrestricted_rest_endpoints' )->once();
		Filters\expectAdded( 'determine_current_user' )->once();

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
