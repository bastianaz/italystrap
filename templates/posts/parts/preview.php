<?php
/**
 * The template used for displaying preview message
 *
 * @package ItalyStrap
 * @since 1.0.0
 * @since 4.0.0 Code refactoring.
 */
declare(strict_types=1);

namespace ItalyStrap;

if ( ! \is_preview() ) {
	return;
}

?><div <?php HTML\get_attr_e( 'preview', [] ); ?>><?php echo \wp_kses_post( \__( '<strong>Note:</strong> You are previewing this post. This post has not yet been published.', 'italystrap' ) ); ?></div>
