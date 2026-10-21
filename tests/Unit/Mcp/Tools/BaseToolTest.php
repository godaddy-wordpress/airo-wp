<?php
/**
 * BaseTool tests.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

/**
 * Tests for BaseTool abstract class.
 */
final class BaseToolTest extends TestCase {

	/**
	 * BaseTool test double.
	 *
	 * @var BaseTool
	 */
	private BaseTool $tool;

	/**
	 * Set up.
	 */
	protected function setUp(): void {
		parent::setUp();

		Functions\when( '__' )->returnArg();

		$this->tool = new class() extends BaseTool {
			public function execute( array $input ): array {
				return array( 'executed' => true );
			}

			public function register(): void {}

			public function check_permissions(): bool {
				return current_user_can( 'manage_options' );
			}

			public function exposed_build_output_schema( string $description = '', array $properties = array(), array $required = array() ): array {
				return $this->build_output_schema( $description, $properties, $required );
			}

			public function exposed_load_admin_file( string $filename ): void {
				$this->load_admin_file( $filename );
			}

			public function exposed_can_read_post( $post ): bool {
				return $this->can_read_post( $post );
			}

			public function exposed_is_content_withheld( $post, bool $can_edit ): bool {
				return $this->is_content_withheld( $post, $can_edit );
			}

			public function exposed_prime_parent_caches( array $posts ): void {
				$this->prime_parent_caches( $posts );
			}
		};
	}

	public function test_execute_delegates_to_implementation(): void {
		$result = $this->tool->execute( array() );

		$this->assertSame( array( 'executed' => true ), $result );
	}

	public function test_check_permissions_delegates_to_current_user_can(): void {
		Functions\expect( 'current_user_can' )->once()->with( 'manage_options' )->andReturn( true );

		$this->assertTrue( $this->tool->check_permissions() );
	}

	public function test_check_permissions_returns_false_when_user_lacks_capability(): void {
		Functions\expect( 'current_user_can' )->once()->with( 'manage_options' )->andReturn( false );

		$this->assertFalse( $this->tool->check_permissions() );
	}

	public function test_build_output_schema_returns_base_schema(): void {
		$schema = $this->tool->exposed_build_output_schema();

		$this->assertSame( 'object', $schema['type'] );
		$this->assertArrayHasKey( 'success', $schema['properties'] );
		$this->assertArrayHasKey( 'message', $schema['properties'] );
		$this->assertContains( 'success', $schema['required'] );
	}

	public function test_build_output_schema_merges_properties(): void {
		$schema = $this->tool->exposed_build_output_schema(
			'Test description',
			array( 'extra' => array( 'type' => 'string' ) ),
			array( 'extra' )
		);

		$this->assertSame( 'Test description', $schema['description'] );
		$this->assertArrayHasKey( 'extra', $schema['properties'] );
		$this->assertContains( 'extra', $schema['required'] );
	}

	public function test_load_admin_file_skips_in_test_env(): void {
		if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
			define( 'PHPUNIT_RUNNING', true );
		}
		$this->tool->exposed_load_admin_file( 'nonexistent.php' );
		$this->assertTrue( true );
	}

	public function test_can_read_post_denies_without_read_post(): void {
		Functions\when( 'current_user_can' )->justReturn( false );

		$this->assertFalse( $this->tool->exposed_can_read_post( $this->post( 5, 'post' ) ) );
	}

	public function test_can_read_post_requires_edit_post_for_revisions(): void {
		Functions\when( 'current_user_can' )->alias(
			function ( $cap ) {
				return 'read_post' === $cap;
			}
		);

		$this->assertFalse( $this->tool->exposed_can_read_post( $this->post( 6, 'revision' ) ) );
	}

	public function test_can_read_post_allows_revisions_with_edit_post(): void {
		Functions\when( 'current_user_can' )->justReturn( true );

		$this->assertTrue( $this->tool->exposed_can_read_post( $this->post( 6, 'revision' ) ) );
	}

	public function test_can_read_post_allows_viewable_custom_type_without_type_edit_cap(): void {
		Functions\when( 'get_post_type_object' )->justReturn( $this->type_object( 'edit_products' ) );
		Functions\when( 'is_post_type_viewable' )->justReturn( true );
		Functions\when( 'current_user_can' )->alias(
			function ( $cap ) {
				return 'edit_products' !== $cap;
			}
		);

		$this->assertTrue( $this->tool->exposed_can_read_post( $this->post( 7, 'product' ) ) );
	}

	public function test_can_read_post_requires_type_edit_cap_for_non_viewable_types(): void {
		Functions\when( 'get_post_type_object' )->justReturn( $this->type_object( 'edit_theme_options' ) );
		Functions\when( 'is_post_type_viewable' )->justReturn( false );
		Functions\when( 'current_user_can' )->alias(
			function ( $cap ) {
				return 'edit_theme_options' !== $cap;
			}
		);

		$this->assertFalse( $this->tool->exposed_can_read_post( $this->post( 8, 'wp_template' ) ) );
	}

	public function test_can_read_post_allows_non_viewable_types_with_type_edit_cap(): void {
		Functions\when( 'get_post_type_object' )->justReturn( $this->type_object( 'edit_theme_options' ) );
		Functions\when( 'is_post_type_viewable' )->justReturn( false );
		Functions\when( 'current_user_can' )->justReturn( true );

		$this->assertTrue( $this->tool->exposed_can_read_post( $this->post( 8, 'wp_template' ) ) );
	}

	public function test_can_read_post_denies_unregistered_post_type(): void {
		Functions\when( 'get_post_type_object' )->justReturn( null );
		Functions\when( 'current_user_can' )->justReturn( true );

		$this->assertFalse( $this->tool->exposed_can_read_post( $this->post( 9, 'gone_cpt' ) ) );
	}

	public function test_is_content_withheld(): void {
		$open                  = $this->post( 1, 'post' );
		$locked                = $this->post( 2, 'post' );
		$locked->post_password = 'hunter2';

		$this->assertFalse( $this->tool->exposed_is_content_withheld( $open, false ) );
		$this->assertTrue( $this->tool->exposed_is_content_withheld( $locked, false ) );
		$this->assertFalse( $this->tool->exposed_is_content_withheld( $locked, true ) );
	}

	public function test_prime_parent_caches_loads_unique_parents_once(): void {
		$posts = array(
			(object) array( 'post_parent' => 5 ),
			(object) array( 'post_parent' => 0 ),
			(object) array( 'post_parent' => 7 ),
			(object) array( 'post_parent' => 5 ),
		);
		Functions\expect( '_prime_post_caches' )->once()->with( array( 5, 7 ), false, false );

		$this->tool->exposed_prime_parent_caches( $posts );
		$this->addToAssertionCount( 1 );
	}

	public function test_prime_parent_caches_skips_when_nothing_is_attached(): void {
		Functions\expect( '_prime_post_caches' )->never();

		$this->tool->exposed_prime_parent_caches( array( (object) array( 'post_parent' => 0 ) ) );
		$this->addToAssertionCount( 1 );
	}

	private function post( int $id, string $type ): \stdClass {
		$post                = new \stdClass();
		$post->ID            = $id;
		$post->post_type     = $type;
		$post->post_password = '';
		return $post;
	}

	private function type_object( string $edit_cap ): \stdClass {
		$type      = new \stdClass();
		$type->cap = (object) array( 'edit_posts' => $edit_cap );
		return $type;
	}
}
