<?php
/**
 * View: Day Event
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/v2/day/event.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @since 5.0.0
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 */

$classes = tribe_get_post_class( array( 'tribe-common-g-row', 'tribe-common-g-row--gutters', 'tribe-events-calendar-day__event' ), $event->ID );

if ( ! empty( $event->featured ) ) {
	$classes[] = 'tribe-events-calendar-day__event--featured';
}
?>
<div class="rg-tribe-events-calendar-event-wrapper rg-tribe-events-calendar-day-event-wrapper">
	<article <?php tribe_classes( $classes ); ?>>
		<div class="tribe-events-calendar-day__event-content">

			<?php $this->template( 'day/event/featured-image', array( 'event' => $event ) ); ?>

			<div class="tribe-events-calendar-day__event-details rg-tribe-events-calendar-event-details">

				<header class="tribe-events-calendar-day__event-header">
					<div class="tribe-event-schedule-long">
						<div class="rg-tribe-events-single-heading">
							<?php $this->template( 'day/event/date', array( 'event' => $event ) ); ?>
							<?php $this->template( 'day/event/title', array( 'event' => $event ) ); ?>
							<?php $this->template( 'day/event/venue', array( 'event' => $event ) ); ?>
						</div>
						<?php $this->template( 'day/event/cost', array( 'event' => $event ) ); ?>
					</div>
				</header>

				<?php $this->template( 'day/event/description', array( 'event' => $event ) ); ?>

			</div>

		</div>
	</article>
</div>
