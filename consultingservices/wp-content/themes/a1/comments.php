<?php
/*
 * Comment Template File.
 */
?>
<?php
if ( post_password_required() )
	return;
?>
<div class="clearfix"></div>
<div id="comments" class="comments-area">
	<?php if ( have_comments() ) : 	?>
    <h3 class="comments-title">
		<?php // WPCS: XSS OK.
                   /* translators: 1: comment count number */
			printf( esc_html(_n( '%1$s thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', get_comments_number(), 'a1' )),
			esc_attr(number_format_i18n( get_comments_number() )), get_the_title() ); ?>
		
	</h3>
    <ul>
		<?php wp_list_comments( array( 'short_ping' => true, 'avatar_size' => 80,) ); ?>
    </ul>
	<?php paginate_comments_links();
		endif;
		comment_form(); ?>
</div><!-- #comments .comments-area -->