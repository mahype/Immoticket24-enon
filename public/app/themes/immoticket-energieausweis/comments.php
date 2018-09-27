<?php
/**
 * The comments template for our theme.
 *
 * @package immoticketenergieausweis
 */
if ( post_password_required() ) {
  return;
}
?>

<div id="comments" class="comments-area">

  <?php // You can start editing here -- including this comment! ?>

  <?php if ( have_comments() ) : ?>
    <h2 class="comments-title">
      <?php
        printf( _nx( '%1$s Kommentar zu &ldquo;%2$s&rdquo;', '%1$s Kommentare zu &ldquo;%2$s&rdquo;', get_comments_number(), 'comments title', 'immoticketenergieausweis' ),
          number_format_i18n( get_comments_number() ), '<span>' . get_the_title() . '</span>' );
      ?>
    </h2>

    <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
    <nav id="comment-nav-above" class="comment-navigation" role="navigation">
      <h1 class="screen-reader-text"><?php _e( 'Kommentar-Navigation', 'immoticketenergieausweis' ); ?></h1>
      <div class="nav-previous"><?php previous_comments_link( __( '&larr; Ältere Kommentare', 'immoticketenergieausweis' ) ); ?></div>
      <div class="nav-next"><?php next_comments_link( __( 'Neuere Kommentare &rarr;', 'immoticketenergieausweis' ) ); ?></div>
    </nav><!-- #comment-nav-above -->
    <?php endif; // check for comment navigation ?>

    <ol class="comment-list">
      <?php
        wp_list_comments( array(
          'style'      => 'ol',
          'short_ping' => true,
        ) );
      ?>
    </ol><!-- .comment-list -->

    <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
    <nav id="comment-nav-below" class="comment-navigation" role="navigation">
      <h1 class="screen-reader-text"><?php _e( 'Kommentar-Navigation', 'immoticketenergieausweis' ); ?></h1>
      <div class="nav-previous"><?php previous_comments_link( __( '&larr; Ältere Kommentare', 'immoticketenergieausweis' ) ); ?></div>
      <div class="nav-next"><?php next_comments_link( __( 'Neuere Kommentare &rarr;', 'immoticketenergieausweis' ) ); ?></div>
    </nav><!-- #comment-nav-below -->
    <?php endif; // check for comment navigation ?>

  <?php endif; // have_comments() ?>

  <?php if ( ! comments_open() && '0' != get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
    <p class="no-comments"><?php _e( 'Kommentare sind geschlossen.', 'immoticketenergieausweis' ); ?></p>
  <?php endif; ?>

  <?php
  $commenter = wp_get_current_commenter();

  $req = get_option( 'require_name_email' );
  $required_attr = ' aria-required="true" required';
  $required_indicator = ' <span class="required">*</span>';

  add_action( 'comment_form_top', 'immoticketenergieausweis_comment_form_top', 1 );
  add_action( 'comment_form', 'immoticketenergieausweis_comment_form_bottom', 1 );

  comment_form( array(
    'fields'            => array(
      'author'            => '<div class="comment-form-author form-group"><label class="control-label col-sm-3" for="author">' . __( 'Name', 'immoticketenergieausweis' ) . ( $req ? $required_indicator : '' ) . '</label><div class="col-sm-9"><input type="text" id="author" name="author" class="form-control" value="' . esc_attr( $commenter['comment_author'] ) . '"' . ( $req ? $required_attr : '' ) . '></div></div>',
      'email'             => '<div class="comment-form-email form-group"><label class="control-label col-sm-3" for="email">' . __( 'Email', 'immoticketenergieausweis' ) . ( $req ? $required_indicator : '' ) . '</label><div class="col-sm-9"><input type="email" id="email" name="email" class="form-control" value="' . esc_attr( $commenter['comment_author_email'] ) . '"' . ( $req ? $required_attr : '' ) . '></div></div>',
      'url'               => '<div class="comment-form-url form-group"><label class="control-label col-sm-3" for="url">' . __( 'Website', 'immoticketenergieausweis' ) . '</label><div class="col-sm-9"><input type="url" id="url" name="url" class="form-control" value="' . esc_attr( $commenter['comment_author_url'] ) . '"></div></div>',
    ),
    'comment_field'     => '<div class="comment-form-comment form-group"><label class="control-label col-sm-3" for="comment">' . __( 'Kommentar', 'immoticketenergieausweis' ) . $required_indicator . '</label><div class="col-sm-9"><textarea id="comment" name="comment" class="form-control" rows="8"' . $required_attr . '></textarea></div></div>',
    'submit_field'      => '<div class="form-submit form-group"><div class="col-sm-9 col-sm-offset-3">%1$s %2$s</div></div>',
    'submit_button'     => '<button type="submit" id="%2$s name="%1$s" class="%3$s">%4$s</button>',
    'class_submit'      => 'submit btn btn-primary',
    'format'            => 'html5',
  ) );

  remove_action( 'comment_form_top', 'immoticketenergieausweis_comment_form_top', 1 );
  remove_action( 'comment_form', 'immoticketenergieausweis_comment_form_bottom', 1 );
  ?>

</div><!-- #comments -->
