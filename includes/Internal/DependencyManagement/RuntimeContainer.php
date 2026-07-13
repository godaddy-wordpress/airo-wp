<?php
/**
 * Runtime dependency injection container.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Internal\DependencyManagement;

defined( 'ABSPATH' ) || exit;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;

/**
 * Reflection-based DI container for plugin classes.
 */
class RuntimeContainer {

	private const ROOT_NAMESPACE = 'GoDaddy\\WordPress\\Plugins\\AiroWp\\';

	/**
	 * Resolved service instances.
	 *
	 * @var array<string, object>
	 */
	protected $resolved_cache;

	/**
	 * Initial resolved instances passed to the constructor.
	 *
	 * @var array<string, object>
	 */
	protected $initial_resolved_cache;

	/**
	 * Constructor.
	 *
	 * @param array<string, object> $initial_resolved_cache Pre-resolved instances.
	 */
	public function __construct( array $initial_resolved_cache ) {
		$this->initial_resolved_cache = $initial_resolved_cache;
		$this->resolved_cache         = $initial_resolved_cache;
	}

	/**
	 * Get a service instance.
	 *
	 * @param string $class_name Class to resolve.
	 * @return object
	 * @throws ContainerException When resolution fails.
	 */
	public function get( string $class_name ) {
		$class_name    = trim( $class_name, '\\' );
		$resolve_chain = array();
		return $this->get_core( $class_name, $resolve_chain );
	}

	/**
	 * Check if a class can be resolved.
	 *
	 * @param string $class_name Class name.
	 * @return bool
	 */
	public function has( string $class_name ): bool {
		$class_name = trim( $class_name, '\\' );
		return isset( $this->resolved_cache[ $class_name ] )
			|| ( $this->is_class_allowed( $class_name ) && class_exists( $class_name ) );
	}

	/**
	 * Core resolution logic.
	 *
	 * @param string   $class_name Class to resolve.
	 * @param string[] $resolve_chain Active resolution chain.
	 * @return object
	 * @throws ContainerException When resolution fails.
	 */
	protected function get_core( string $class_name, array &$resolve_chain ) {
		if ( isset( $this->resolved_cache[ $class_name ] ) ) {
			return $this->resolved_cache[ $class_name ];
		}

		if ( in_array( $class_name, $resolve_chain, true ) ) {
			throw new ContainerException(
				'Recursive resolution of class \'' . esc_html( $class_name ) . '\'. Chain: ' . esc_html( implode( ', ', $resolve_chain ) )
			);
		}

		if ( ! $this->is_class_allowed( $class_name ) ) {
			throw new ContainerException(
				'Class \'' . esc_html( $class_name ) . '\' is outside ' . esc_html( self::ROOT_NAMESPACE )
			);
		}

		if ( ! class_exists( $class_name ) ) {
			throw new ContainerException( 'Class \'' . esc_html( $class_name ) . '\' does not exist.' );
		}

		$resolve_chain[] = $class_name;

		try {
			$instance = $this->instantiate( $class_name, $resolve_chain );
		} catch ( ReflectionException $e ) {
			throw new ContainerException(
				'Reflection error resolving \'' . esc_html( $class_name ) . '\': ' . esc_html( $e->getMessage() ),
				0,
				$e // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- previous exception, not output.
			);
		}

		$this->resolved_cache[ $class_name ] = $instance;
		return $instance;
	}

	/**
	 * Whether the class is in the allowed namespace.
	 *
	 * @param string $class_name Class name.
	 * @return bool
	 */
	private function is_class_allowed( string $class_name ): bool {
		return 0 === strpos( $class_name, self::ROOT_NAMESPACE )
			|| isset( $this->initial_resolved_cache[ $class_name ] );
	}

	/**
	 * Instantiate a class via reflection.
	 *
	 * @param string   $class_name Class to instantiate.
	 * @param string[] $resolve_chain Active resolution chain.
	 * @return object
	 * @throws ContainerException When instantiation fails.
	 */
	private function instantiate( string $class_name, array &$resolve_chain ): object {
		$ref  = new ReflectionClass( $class_name );
		$ctor = $ref->getConstructor();

		$args = array();
		if ( null !== $ctor && ! $ctor->isPublic() ) {
			throw new ContainerException( '\'' . esc_html( $class_name ) . '\' constructor is not public.' );
		}
		if ( null !== $ctor ) {
			$args = $this->resolve_parameters( $ctor, $resolve_chain );
		}

		$instance = $ref->newInstanceArgs( $args );
		$this->invoke_init( $instance, $resolve_chain );
		return $instance;
	}

	/**
	 * Invoke public init() method if present.
	 *
	 * @param object   $instance Instance to initialize.
	 * @param string[] $resolve_chain Active resolution chain.
	 */
	private function invoke_init( object $instance, array &$resolve_chain ): void {
		if ( ! method_exists( $instance, 'init' ) ) {
			return;
		}
		$method = new ReflectionMethod( $instance, 'init' );
		if ( ! $method->isPublic() || $method->isStatic() ) {
			return;
		}
		$args = $this->resolve_parameters( $method, $resolve_chain );
		$method->invokeArgs( $instance, $args );
	}

	/**
	 * Resolve reflection parameters for a method.
	 *
	 * @param ReflectionMethod $reflection_method Method or constructor.
	 * @param string[]         $resolve_chain Active resolution chain.
	 * @return array<int, mixed>
	 * @throws ContainerException When a parameter cannot be resolved.
	 */
	private function resolve_parameters( ReflectionMethod $reflection_method, array &$resolve_chain ): array {
		$args = array();
		foreach ( $reflection_method->getParameters() as $param ) {
			$args[] = $this->resolve_parameter( $param, $resolve_chain );
		}
		return $args;
	}

	/**
	 * Resolve a single reflection parameter.
	 *
	 * @param ReflectionParameter $param Parameter to resolve.
	 * @param string[]            $resolve_chain Active resolution chain.
	 * @return mixed
	 * @throws ContainerException When parameter cannot be resolved.
	 */
	private function resolve_parameter( ReflectionParameter $param, array &$resolve_chain ) {
		$type = $param->getType();
		if ( ! $type instanceof ReflectionNamedType || $type->isBuiltin() ) {
			if ( $param->isDefaultValueAvailable() ) {
				return $param->getDefaultValue();
			}
			throw new ContainerException(
				'Cannot resolve parameter $' . esc_html( $param->getName() ) . ' without type hint.'
			);
		}
		return $this->get_core( $type->getName(), $resolve_chain );
	}
}
