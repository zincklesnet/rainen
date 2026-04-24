<?php
/**
 * View: Latest Past Event
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/v2/latest-past/event.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 5.1.0
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 */

$container_classes = array( 'tribe-common-g-row', 'tribe-events-calendar-latest-past__event-row' );
$container_classes['tribe-events-calendar-latest-past__event-row--featured'] = $event->featured;

$event_classes = tribe_get_post_class( array( 'tribe-events-calendar-latest-past__event', 'tribe-common-g-row', 'tribe-common-g-row--gutters' ), $event->ID );
?>
<div class="rg-tribe-events-calendar-event-wrapper rg-tribe-events-calendar-latest-past-event-wrapper">
	<div <?php tribe_classes( $container_classes ); ?>>

		<div class="tribe-events-calendar-latest-past__event-wrapper tribe-common-g-cols">

			<article <?php tribe_classes( $event_classes ); ?>>

				<?php $this->template( 'latest-past/event/featured-image', array( 'event' => $event ) ); ?>

				<div class="tribe-events-calendar-latest-past__event-details rg-tribe-events-calendar-event-details tribe-common-g-col">

					<header class="tribe-events-calendar-latest-past__event-header">
						<div class="tribe-event-schedule-short">
							<?php $this->template( 'latest-past/event/date-tag', array( 'event' => $event ) ); ?>
						</div>
						<div class="tribe-event-schedule-long">
							<div class="rg-tribe-events-single-heading">
								<?php $this->template( 'latest-past/event/date', array( 'event' => $event ) ); ?>
								<?php $this->template( 'latest-past/event/title', array( 'event' => $event ) ); ?>
								<?php $this->template( 'latest-past/event/venue', array( 'event' => $event ) ); ?>
							</div>
							<?php $this->template( 'latest-past/event/cost', array( 'event' => $event ) ); ?>
						</div>
					</header>

					<?php $this->template( 'latest-past/event/description', array( 'event' => $event ) ); ?>

				</div>
			</article>

		</div>

	</div>
</div>
