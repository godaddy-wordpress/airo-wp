<?php
/**
 * WP-CLI --require helper: registers a minimal OpenAI-compatible AI provider
 * for Plugin Check's --ai analysis, keyed to PLUGIN_CHECK_AI_KEY.
 *
 * Env vars consumed:
 *   PLUGIN_CHECK_AI_KEY   API key (Bearer token). Required; file is a no-op when absent.
 *   PLUGIN_CHECK_AI_URL   Base URL for the provider API (default: https://api.openai.com/v1).
 *   PLUGIN_CHECK_AI_MODEL Model ID used when no --ai-model flag is given (default: gpt-4o).
 *
 * Registers provider ID "plugin-check-ai". Pair with --ai-model=plugin-check-ai::<model>
 * on the wp plugin check command to target a specific model.
 *
 * @package AiroWp
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

$_pcp_ai_key = (string) ( getenv( 'PLUGIN_CHECK_AI_KEY' ) ?: '' );
if ( '' === $_pcp_ai_key ) {
	return;
}

$GLOBALS['_pcp_ai_base_url'] = rtrim( (string) ( getenv( 'PLUGIN_CHECK_AI_URL' ) ?: 'https://api.openai.com/v1' ), '/' );
$GLOBALS['_pcp_ai_model_id'] = (string) ( getenv( 'PLUGIN_CHECK_AI_MODEL' ) ?: 'gpt-4o' );

WP_CLI::add_hook(
	'after_wp_load',
	static function () use ( $_pcp_ai_key ) {

		if ( ! class_exists( 'WordPress\AiClient\AiClient' ) ) {
			WP_CLI::warning( 'PLUGIN_CHECK_AI: WordPress AI Client not available (requires WP 7.0+). AI analysis skipped.' );
			return;
		}

		// Availability: key-presence check without making a real API call.
		if ( ! class_exists( 'PcpAiProviderAvailability' ) ) {
			class PcpAiProviderAvailability implements \WordPress\AiClient\Providers\Contracts\ProviderAvailabilityInterface {
				public function isConfigured() : bool {
					return '' !== (string) ( getenv( 'PLUGIN_CHECK_AI_KEY' ) ?: '' );
				}
			}
		}

		// Static model metadata directory — one model, no API listing call.
		if ( ! class_exists( 'PcpAiStaticModelDirectory' ) ) {
			class PcpAiStaticModelDirectory implements \WordPress\AiClient\Providers\Contracts\ModelMetadataDirectoryInterface {
				/** @var string */
				private $modelId;

				public function __construct( string $modelId ) {
					$this->modelId = $modelId;
				}

				private function buildMeta() : \WordPress\AiClient\Providers\Models\DTO\ModelMetadata {
					return new \WordPress\AiClient\Providers\Models\DTO\ModelMetadata(
						$this->modelId,
						$this->modelId,
						[ \WordPress\AiClient\Providers\Models\Enums\CapabilityEnum::textGeneration() ],
						[]
					);
				}

				public function listModelMetadata() : array {
					return [ $this->buildMeta() ];
				}

				public function hasModelMetadata( string $modelId ) : bool {
					return $modelId === $this->modelId;
				}

				public function getModelMetadata( string $modelId ) : \WordPress\AiClient\Providers\Models\DTO\ModelMetadata {
					if ( $modelId !== $this->modelId ) {
						throw new \WordPress\AiClient\Common\Exception\InvalidArgumentException(
							"Model {$modelId} not registered in Plugin Check AI provider"
						);
					}
					return $this->buildMeta();
				}
			}
		}

		// Concrete text-generation model using wp_remote_post() for the HTTP call.
		// Avoids extending AbstractOpenAiCompatibleTextGenerationModel which is not
		// included in the WP.org release package (only in the develop repo).
		if ( ! class_exists( 'PcpAiTextModel' ) ) {
			// Implements ModelInterface only; TextGenerationModelInterface is absent from the WP.org
			// release package. generateTextResult() is still callable via duck typing / capability dispatch.
			class PcpAiTextModel implements \WordPress\AiClient\Providers\Models\Contracts\ModelInterface {

				/** @var \WordPress\AiClient\Providers\Models\DTO\ModelMetadata */
				private $modelMetadata;

				/** @var \WordPress\AiClient\Providers\DTO\ProviderMetadata */
				private $providerMetadata;

				/** @var \WordPress\AiClient\Providers\Models\DTO\ModelConfig|null */
				private $config = null;

				public function __construct(
					\WordPress\AiClient\Providers\Models\DTO\ModelMetadata $modelMetadata,
					\WordPress\AiClient\Providers\DTO\ProviderMetadata $providerMetadata
				) {
					$this->modelMetadata   = $modelMetadata;
					$this->providerMetadata = $providerMetadata;
				}

				public function metadata() : \WordPress\AiClient\Providers\Models\DTO\ModelMetadata {
					return $this->modelMetadata;
				}

				public function providerMetadata() : \WordPress\AiClient\Providers\DTO\ProviderMetadata {
					return $this->providerMetadata;
				}

				public function setConfig( \WordPress\AiClient\Providers\Models\DTO\ModelConfig $config ) : void {
					$this->config = $config;
				}

				public function getConfig() : \WordPress\AiClient\Providers\Models\DTO\ModelConfig {
					if ( null === $this->config ) {
						$this->config = \WordPress\AiClient\Providers\Models\DTO\ModelConfig::fromArray( [] );
					}
					return $this->config;
				}

				public function generateTextResult( array $prompt ) : \WordPress\AiClient\Results\DTO\GenerativeAiResult {
					$base_url = rtrim( (string) ( $GLOBALS['_pcp_ai_base_url'] ?? 'https://api.openai.com/v1' ), '/' );
					$api_key  = (string) ( getenv( 'PLUGIN_CHECK_AI_KEY' ) ?: '' );

					$messages = [];
					foreach ( $prompt as $message ) {
						$role  = $message->getRole()->isModel() ? 'assistant' : 'user';
						$text  = '';
						foreach ( $message->getParts() as $part ) {
							if ( $part->getType()->isText() && ! $part->getChannel()->isThought() ) {
								$text .= $part->getText();
							}
						}
						if ( '' !== $text ) {
							$messages[] = [
								'role'    => $role,
								'content' => $text,
							];
						}
					}

					$response = wp_remote_post(
						$base_url . '/chat/completions',
						[
							'headers' => [
								'Content-Type'  => 'application/json',
								'Authorization' => 'Bearer ' . $api_key,
							],
							'body'    => wp_json_encode( [
								'model'    => $this->modelMetadata->getId(),
								'messages' => $messages,
							] ),
							'timeout' => 60,
						]
					);

					if ( is_wp_error( $response ) ) {
						throw new \WordPress\AiClient\Common\Exception\RuntimeException(
							'PLUGIN_CHECK_AI: HTTP request failed — ' . $response->get_error_message()
						);
					}

					$status = (int) wp_remote_retrieve_response_code( $response );
					if ( $status < 200 || $status >= 300 ) {
						throw new \WordPress\AiClient\Common\Exception\RuntimeException(
							'PLUGIN_CHECK_AI: API returned HTTP ' . $status
						);
					}

					$body = json_decode( (string) wp_remote_retrieve_body( $response ), true );
					if ( ! is_array( $body ) ) {
						throw new \WordPress\AiClient\Common\Exception\RuntimeException(
							'PLUGIN_CHECK_AI: Failed to parse API response'
						);
					}

					$candidates = [];
					foreach ( ( $body['choices'] ?? [] ) as $choice ) {
						$message_text = (string) ( $choice['message']['content'] ?? '' );
						$ai_message   = new \WordPress\AiClient\Messages\DTO\Message(
							\WordPress\AiClient\Messages\Enums\MessageRoleEnum::model(),
							[ new \WordPress\AiClient\Messages\DTO\MessagePart( $message_text ) ]
						);

						switch ( $choice['finish_reason'] ?? 'stop' ) {
							case 'length':
								$finish_reason = \WordPress\AiClient\Results\Enums\FinishReasonEnum::length();
								break;
							case 'content_filter':
								$finish_reason = \WordPress\AiClient\Results\Enums\FinishReasonEnum::contentFilter();
								break;
							default:
								$finish_reason = \WordPress\AiClient\Results\Enums\FinishReasonEnum::stop();
						}

						$candidates[] = new \WordPress\AiClient\Results\DTO\Candidate( $ai_message, $finish_reason );
					}

					$usage       = $body['usage'] ?? [];
					$token_usage = new \WordPress\AiClient\Results\DTO\TokenUsage(
						(int) ( $usage['prompt_tokens'] ?? 0 ),
						(int) ( $usage['completion_tokens'] ?? 0 ),
						(int) ( $usage['total_tokens'] ?? 0 )
					);

					$additional_data = $body;
					unset( $additional_data['id'], $additional_data['choices'], $additional_data['usage'] );

					return new \WordPress\AiClient\Results\DTO\GenerativeAiResult(
						(string) ( $body['id'] ?? '' ),
						$candidates,
						$token_usage,
						$this->providerMetadata,
						$this->modelMetadata,
						$additional_data
					);
				}
			}
		}

		// Provider: wraps the model, directory, and availability.
		if ( ! class_exists( 'PcpAiProvider' ) ) {
			class PcpAiProvider extends \WordPress\AiClient\Providers\ApiBasedImplementation\AbstractApiProvider {
				protected static function baseUrl() : string {
					return (string) ( $GLOBALS['_pcp_ai_base_url'] ?? 'https://api.openai.com/v1' );
				}

				protected static function createProviderMetadata() : \WordPress\AiClient\Providers\DTO\ProviderMetadata {
					return new \WordPress\AiClient\Providers\DTO\ProviderMetadata(
						'plugin-check-ai',
						'Plugin Check AI',
						\WordPress\AiClient\Providers\Enums\ProviderTypeEnum::cloud(),
						null,
						\WordPress\AiClient\Providers\Http\Enums\RequestAuthenticationMethod::apiKey()
					);
				}

				protected static function createProviderAvailability() : \WordPress\AiClient\Providers\Contracts\ProviderAvailabilityInterface {
					return new PcpAiProviderAvailability();
				}

				protected static function createModelMetadataDirectory() : \WordPress\AiClient\Providers\Contracts\ModelMetadataDirectoryInterface {
					return new PcpAiStaticModelDirectory(
						(string) ( $GLOBALS['_pcp_ai_model_id'] ?? 'gpt-4o' )
					);
				}

				protected static function createModel(
					\WordPress\AiClient\Providers\Models\DTO\ModelMetadata $modelMetadata,
					\WordPress\AiClient\Providers\DTO\ProviderMetadata $providerMetadata
				) : \WordPress\AiClient\Providers\Models\Contracts\ModelInterface {
					return new PcpAiTextModel( $modelMetadata, $providerMetadata );
				}
			}
		}

		try {
			$registry = \WordPress\AiClient\AiClient::defaultRegistry();
			$registry->registerProvider( PcpAiProvider::class );
			$registry->setProviderRequestAuthentication(
				PcpAiProvider::class,
				new \WordPress\AiClient\Providers\Http\DTO\ApiKeyRequestAuthentication( $_pcp_ai_key )
			);
		} catch ( \Exception $e ) {
			WP_CLI::warning( 'PLUGIN_CHECK_AI: provider registration failed — ' . $e->getMessage() );
		}
	}
);
