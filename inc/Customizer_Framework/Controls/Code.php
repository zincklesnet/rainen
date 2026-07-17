<?php
/**
 * Reign\Customizer_Framework\Controls\Code
 *
 * Customizer control for editing raw code (CSS / HTML / JavaScript) with
 * syntax highlighting via WP core's `wp.codeEditor` (CodeMirror wrapper).
 * Mirrors Kirki's `\Kirki\Field\Code` for byte-identical value parity with
 * pre-migration saves.
 *
 * Field args:
 *   choices.language  string  CodeMirror mode. Accepts 'css'|'html'|'js'|'json'
 *                             (anything wp_get_code_editor_settings() recognises).
 *                             Default: 'html'.
 *
 * @package reign
 * @since 8.0.0
 */

namespace Reign\Customizer_Framework\Controls;

defined( 'ABSPATH' ) || exit;

/**
 * Code
 */
class Code extends \WP_Customize_Control {

	/**
	 * Control type identifier (registered with wp.customize.controlConstructor).
	 *
	 * @var string
	 */
	public $type = 'reign-code';

	/**
	 * Language passed to wp.codeEditor (css|html|js|json).
	 *
	 * @var string
	 */
	public $language = 'html';

	/**
	 * Constructor — pull language out of choices into a typed property.
	 *
	 * @param \WP_Customize_Manager $manager Customizer bootstrap.
	 * @param string                $id      Control ID.
	 * @param array                 $args    Control args.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
		if ( isset( $this->choices['language'] ) && is_string( $this->choices['language'] ) ) {
			$this->language = sanitize_key( $this->choices['language'] );
		}
	}

	/**
	 * Enqueue WP core's code editor for this control.
	 *
	 * Returns array (not just enqueues) because wp_enqueue_code_editor()
	 * yields the settings object the JS init needs.
	 */
	public function enqueue() {
		$settings = wp_enqueue_code_editor( array( 'type' => $this->mime_type_for_language() ) );
		if ( false === $settings ) {
			return; // Code editor disabled by user preference; falls back to plain textarea.
		}
		wp_localize_script( 'reign-customizer-controls', '_reignCodeEditor_' . md5( $this->id ), $settings );
	}

	/**
	 * Translate the `language` arg to a MIME type wp_enqueue_code_editor expects.
	 */
	private function mime_type_for_language(): string {
		switch ( $this->language ) {
			case 'css':
				return 'text/css';
			case 'js':
			case 'javascript':
				return 'application/javascript';
			case 'json':
				return 'application/json';
			case 'html':
			default:
				return 'text/html';
		}
	}

	/**
	 * Pass language + setting ID into the Underscore template.
	 */
	public function json() {
		$json             = parent::json();
		$json['language'] = $this->language;
		$json['link']     = $this->get_link();
		$json['value']    = $this->value();
		$json['id']       = $this->id;
		return $json;
	}

	/**
	 * Server-side render fallback (used when JS is disabled).
	 *
	 * Customizer always enqueues JS so this is mostly defensive — but the
	 * textarea here is what wp.codeEditor wraps when JS does run.
	 */
	public function render_content() {
		?>
		<label>
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>
			<?php if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description">
					<?php echo wp_kses_post( $this->description ); ?>
				</span>
			<?php endif; ?>
			<textarea
				class="reign-code-editor large-text code"
				rows="8"
				data-reign-code-language="<?php echo esc_attr( $this->language ); ?>"
				data-reign-code-settings-key="_reignCodeEditor_<?php echo esc_attr( md5( $this->id ) ); ?>"
				<?php $this->link(); ?>
			><?php echo esc_textarea( (string) $this->value() ); ?></textarea>
		</label>
		<?php
	}
}
