<?php
/**
 * Reign\Customizer_Framework\Controls\Number
 *
 * Plain numeric input. Stores a bare number (e.g. 286, 3, 20).
 * choices.min, choices.max, choices.step configure the input bounds.
 *
 * Unlike Slider (range + number + unit composite) and Dimension (number +
 * unit dropdown), this control binds the visible <input type="number">
 * directly to the setting via $this->link() — no companion JS handler or
 * hidden synchroniser input is required.
 *
 * @package Reign */

namespace Reign\Customizer_Framework\Controls;

defined( 'ABSPATH' ) || exit;

/**
 * Number
 */
class Number extends \WP_Customize_Control {

	/**
	 * @var string
	 */
	public $type = 'buddyx-number';

	/**
	 * Render the control content.
	 */
	public function render_content() {
		$min  = $this->choices['min'] ?? '';
		$max  = $this->choices['max'] ?? '';
		$step = $this->choices['step'] ?? 1;
		?>
		<label class="buddyx-number">
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>
			<?php if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
			<?php endif; ?>
			<input
				type="number"
				class="buddyx-number-input"
				<?php if ( '' !== $min ) : ?>min="<?php echo esc_attr( (string) $min ); ?>"<?php endif; ?>
				<?php if ( '' !== $max ) : ?>max="<?php echo esc_attr( (string) $max ); ?>"<?php endif; ?>
				step="<?php echo esc_attr( (string) $step ); ?>"
				value="<?php echo esc_attr( (string) $this->value() ); ?>"
				<?php $this->link(); ?>
			/>
		</label>
		<?php
	}
}
