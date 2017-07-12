<?php

/**
 * @var Es_Settings_Container $es_settings
 */

get_header(); $template = get_option( 'template' ); ?>

<?php do_action( 'es_before_content' ); ?>

    <div class="es-wrap">

        <header class="page-header">
            <h1 class="page-title">
                <?php echo ! empty( $title ) ? $title : __( 'Properties', 'es-plugin' ); ?>
            </h1>
        </header><!-- .page-header -->

        <?php do_action( 'es_before_content_list' ); ?>

        <div class="<?php es_the_list_classes(); ?>">
            <?php do_action( 'es_archive_sorting_dropdown' ); ?>

        <?php if ( have_posts() ) : ?>
                <ul>
                    <?php while ( have_posts() ) : the_post();
                        load_template( ES_TEMPLATES . 'content-archive.php', false );
                    endwhile; ?>
                </ul>
            <?php else: ?>
                <p style="font-size: 14px;"><?php _e( 'Nothing to display.', 'es-plugin' ); ?></p>
            <?php endif; ?>
        </div>

        <?php do_action( 'es_after_content_list' ); ?>
    </div>

    <?php the_posts_pagination( array(
        'prev_next' => false,
        'show_all'     => false,
        'end_size'     => 1,
        'mid_size'     => 1,
        'screen_reader_text' => ' ',
    ) ); ?>

<?php do_action( 'es_after_content' ); ?>

<?php get_footer();
