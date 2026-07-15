<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\GlobalStyles;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\GlobalStyles\ListGlobalStyles;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

final class ListGlobalStylesTest extends TestCase {

	private ListGlobalStyles $tool;

	protected function setUp(): void {
		parent::setUp();

		if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
			define( 'PHPUNIT_RUNNING', true );
		}

		Functions\when( '__' )->returnArg();
		Functions\when( 'sanitize_text_field' )->returnArg();

		$this->tool = new ListGlobalStyles();
	}

	public function test_tool_id_has_correct_prefix(): void {
		$this->assertSame( 'airo-wp/list-global-styles', ListGlobalStyles::TOOL_ID );
	}

	public function test_returns_empty_when_no_styles_found(): void {
		Functions\when( 'get_posts' )->justReturn( array() );

		$result = $this->tool->execute( array() );

		$this->assertTrue( $result['success'] );
		$this->assertSame( array(), $result['styles'] );
		$this->assertSame( 0, $result['total'] );
	}

	public function test_returns_styles_list(): void {
		$post1                = new \stdClass();
		$post1->ID            = 10;
		$post1->post_title    = 'Theme Styles';
		$post1->post_status   = 'publish';
		$post1->post_date     = '2024-01-01 00:00:00';
		$post1->post_modified = '2024-01-02 00:00:00';

		$post2                = new \stdClass();
		$post2->ID            = 20;
		$post2->post_title    = 'Custom Styles';
		$post2->post_status   = 'draft';
		$post2->post_date     = '2024-02-01 00:00:00';
		$post2->post_modified = '2024-02-02 00:00:00';

		Functions\when( 'get_posts' )->justReturn( array( $post1, $post2 ) );
		Functions\when( 'get_post_meta' )->justReturn( 'twentytwentyfive' );

		$result = $this->tool->execute( array() );

		$this->assertTrue( $result['success'] );
		$this->assertCount( 2, $result['styles'] );
		$this->assertSame( 2, $result['total'] );
		$this->assertSame( 10, $result['styles'][0]['id'] );
		$this->assertSame( 'Theme Styles', $result['styles'][0]['title'] );
		$this->assertSame( 'twentytwentyfive', $result['styles'][0]['theme'] );
	}

	public function test_filters_by_theme(): void {
		$post1                = new \stdClass();
		$post1->ID            = 10;
		$post1->post_title    = 'Theme Styles';
		$post1->post_status   = 'publish';
		$post1->post_date     = '2024-01-01 00:00:00';
		$post1->post_modified = '2024-01-02 00:00:00';

		Functions\expect( 'get_posts' )
			->once()
			->with( \Mockery::on( function ( $args ) {
				return isset( $args['meta_query'] )
					&& 'theme' === $args['meta_query'][0]['key']
					&& 'twentytwentyfive' === $args['meta_query'][0]['value'];
			} ) )
			->andReturn( array( $post1 ) );

		Functions\when( 'get_post_meta' )->justReturn( 'twentytwentyfive' );

		$result = $this->tool->execute( array( 'theme' => 'twentytwentyfive' ) );

		$this->assertTrue( $result['success'] );
		$this->assertSame( 1, $result['total'] );
	}
}
