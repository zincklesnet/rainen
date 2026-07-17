<?php
/**
 * Comment Template
 *
 * The comment template displays an individual comment. This can be overwritten by templates specific
 * to the comment type (comment.php, comment-{$comment_type}.php, comment-pingback.php,
 * comment-trackback.php) in a child theme.
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

global $post, $comment;

?>
<li
	<?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
	<article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
		<footer class="comment-meta">
			<div class="comment-author vcard">
				<?php
				if ( 0 !== (int) $args['avatar_size'] ) {
					echo get_avatar( $comment, $args['avatar_size'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_avatar() returns safe markup.
				}
				?>
				<?php
				$comment_author = get_comment_author_link( $comment );

				if ( '0' === $comment->comment_approved ) {
					$comment_author = esc_html( get_comment_author( $comment ) );
				}

				printf(
					/* translators: %s: Comment author link. */
					wp_kses_post( __( '%s <span class="says">says:</span>', 'reign' ) ),
					wp_kses_post( sprintf( '<b class="fn">%s</b>', $comment_author ) )
				);
				?>
			</div><!-- .comment-author -->

			<div class="comment-metadata">
				<?php
				printf(
					'<a href="%s"><time datetime="%s">%s</time></a>',
					esc_url( get_comment_link( $comment, $args ) ),
					esc_attr( get_comment_time( 'c' ) ),
					sprintf(
						/* translators: 1: Comment date, 2: Comment time. */
						esc_html__( '%1$s at %2$s', 'reign' ),
						esc_html( get_comment_date( '', $comment ) ),
						esc_html( get_comment_time() )
					)
				);

				edit_comment_link( __( 'Edit', 'reign' ), ' <span class="edit-link">', '</span>' );
				?>
			</div><!-- .comment-metadata -->

			<?php if ( '0' === $comment->comment_approved ) : ?>
			<em class="comment-awaiting-moderation"></em>
			<?php endif; ?>
		</footer><!-- .comment-meta -->

		<div class="comment-content">
			<?php comment_text(); ?>
		</div><!-- .comment-content -->
		
		<div class="comment-content-actions">
			<div class="comment-content-action" >
				<?php

				do_action( 'reign_before_comment_replay', get_comment_ID(), $comment );

				if ( '1' === $comment->comment_approved ) {
					comment_reply_link(
						array_merge(
							$args,
							array(
								'add_below' => 'div-comment',
								'depth'     => $depth,
								'max_depth' => $args['max_depth'],
								'before'    => '<div class="reply">',
								'after'     => '</div>',
							)
						)
					);
				}

				do_action( 'reign_after_comment_replay', get_comment_ID(), $comment );

				?>
								
			</div>
		</div>
	</article><!-- .comment-body -->
</li><!-- .comment -->
