<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Posts;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Posts\UpdatePostImageAltText;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

final class UpdatePostImageAltTextTest extends TestCase {

	private UpdatePostImageAltText $tool;

	protected function setUp(): void {
		parent::setUp();

		if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
			define( 'PHPUNIT_RUNNING', true );
		}

		Functions\when( '__' )->returnArg();
		Functions\when( 'sanitize_text_field' )->returnArg();
		Functions\when( 'esc_url_raw' )->returnArg();
		Functions\when( 'esc_attr' )->returnArg();

		$this->tool = new UpdatePostImageAltText();
	}

	public function test_tool_id_has_correct_prefix(): void {
		$this->assertSame( 'airo-wp/update-post-image-alt-text', UpdatePostImageAltText::TOOL_ID );
	}

	public function test_missing_post_id_returns_error(): void {
		$result = $this->tool->execute( array() );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Post ID is required', $result['message'] );
	}

	public function test_missing_image_src_returns_error(): void {
		$result = $this->tool->execute( array( 'post_id' => 1 ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Image source URL is required', $result['message'] );
	}

	public function test_missing_alt_returns_error(): void {
		$result = $this->tool->execute( array( 'post_id' => 1, 'image_src' => 'https://example.com/img.jpg' ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Alt text is required', $result['message'] );
	}

	public function test_empty_alt_returns_error(): void {
		$result = $this->tool->execute( array( 'post_id' => 1, 'image_src' => 'https://example.com/img.jpg', 'alt' => '   ' ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Alt text is required', $result['message'] );
	}

	public function test_nonexistent_post_returns_error(): void {
		Functions\expect( 'get_post' )
			->once()
			->with( 999 )
			->andReturn( null );

		$result = $this->tool->execute( array(
			'post_id'   => 999,
			'image_src' => 'https://example.com/img.jpg',
			'alt'       => 'Some alt text',
		) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( '999', $result['message'] );
	}

	public function test_user_without_edit_post_permission_returns_error(): void {
		$post               = new \stdClass();
		$post->post_content = '';

		Functions\expect( 'get_post' )->once()->with( 1 )->andReturn( $post );
		Functions\expect( 'current_user_can' )->with( 'edit_post', 1 )->andReturn( false );

		$result = $this->tool->execute( array(
			'post_id'   => 1,
			'image_src' => 'https://example.com/img.jpg',
			'alt'       => 'New alt text',
		) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'permission', $result['message'] );
	}

	public function test_image_not_found_in_post_returns_error(): void {
		$post               = new \stdClass();
		$post->post_content = '<!-- wp:paragraph --><p>Hello</p><!-- /wp:paragraph -->';

		Functions\expect( 'get_post' )
			->once()
			->with( 1 )
			->andReturn( $post );
		Functions\when( 'current_user_can' )->justReturn( true );

		Functions\expect( 'parse_blocks' )
			->once()
			->andReturn( array(
				array(
					'blockName'    => 'core/paragraph',
					'attrs'        => array(),
					'innerHTML'    => '<p>Hello</p>',
					'innerContent' => array( '<p>Hello</p>' ),
					'innerBlocks'  => array(),
				),
			) );

		$result = $this->tool->execute( array(
			'post_id'   => 1,
			'image_src' => 'https://example.com/img.jpg',
			'alt'       => 'Some alt text',
		) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'not found in post', $result['message'] );
	}

	public function test_successful_update_via_block_attrs_url(): void {
		$post               = new \stdClass();
		$post->post_content = '<!-- wp:image {"url":"https://example.com/img.jpg"} --><figure class="wp-block-image"><img src="https://example.com/img.jpg" alt="old"/></figure><!-- /wp:image -->';

		Functions\expect( 'get_post' )
			->once()
			->with( 1 )
			->andReturn( $post );
		Functions\when( 'current_user_can' )->justReturn( true );

		Functions\expect( 'parse_blocks' )
			->once()
			->andReturn( array(
				array(
					'blockName'    => 'core/image',
					'attrs'        => array( 'url' => 'https://example.com/img.jpg' ),
					'innerHTML'    => '<figure class="wp-block-image"><img src="https://example.com/img.jpg" alt="old"/></figure>',
					'innerContent' => array( '<figure class="wp-block-image"><img src="https://example.com/img.jpg" alt="old"/></figure>' ),
					'innerBlocks'  => array(),
				),
			) );

		Functions\expect( 'serialize_blocks' )
			->once()
			->andReturn( '<!-- updated content -->' );

		Functions\expect( 'wp_update_post' )
			->once()
			->andReturn( 1 );

		Functions\when( 'is_wp_error' )->justReturn( false );

		$result = $this->tool->execute( array(
			'post_id'   => 1,
			'image_src' => 'https://example.com/img.jpg',
			'alt'       => 'New alt text',
		) );

		$this->assertTrue( $result['success'] );
		$this->assertSame( 1, $result['post_id'] );
		$this->assertSame( 'https://example.com/img.jpg', $result['image_src'] );
	}

	public function test_wp_update_post_error_returns_error(): void {
		$post               = new \stdClass();
		$post->post_content = '';

		$wp_error = $this->createMock( \WP_Error::class );
		$wp_error->method( 'get_error_message' )->willReturn( 'DB error' );

		Functions\expect( 'get_post' )
			->once()
			->with( 1 )
			->andReturn( $post );
		Functions\when( 'current_user_can' )->justReturn( true );

		Functions\expect( 'parse_blocks' )
			->once()
			->andReturn( array(
				array(
					'blockName'    => 'core/image',
					'attrs'        => array( 'url' => 'https://example.com/img.jpg' ),
					'innerHTML'    => '<figure><img src="https://example.com/img.jpg" alt="old"/></figure>',
					'innerContent' => array( '<figure><img src="https://example.com/img.jpg" alt="old"/></figure>' ),
					'innerBlocks'  => array(),
				),
			) );

		Functions\expect( 'serialize_blocks' )
			->once()
			->andReturn( '' );

		Functions\expect( 'wp_update_post' )
			->once()
			->andReturn( $wp_error );

		Functions\when( 'is_wp_error' )->justReturn( true );

		$result = $this->tool->execute( array(
			'post_id'   => 1,
			'image_src' => 'https://example.com/img.jpg',
			'alt'       => 'New alt text',
		) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Failed to update post', $result['message'] );
	}
}
