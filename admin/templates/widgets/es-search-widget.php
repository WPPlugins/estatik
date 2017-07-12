<?php

/**
 * Search widget.
 *
 * @var array $instance
 * @var array $args
 * @var array $fields
 * @var Es_Search_Widget $this
 */

$fields = $this::get_widget_fields();

echo $args['before_widget']; ?>

    <div class="es-search__wrapper es-search__wrapper--<?php echo $instance['layout']; ?>">

        <?php if ( ! empty( $instance['title'] ) ) : ?>
            <?php echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title']; ?>
        <?php endif; ?>

        <form action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search" method="get">

            <?php do_action( 'es_before_search' ); ?>

            <input type="hidden" name="s"/>

            <?php foreach ( $instance['fields'] as $name ) : ?>
                <?php if ( in_array( $name, $fields ) ) : ?>
                    <div class="es-search__field es-search__field--<?php echo $name; ?>">
                        <?php echo Es_Search_Widget::render_field( $name ); ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

            <input type="hidden" name="post_type" value="<?php echo Es_Property::get_post_type_name(); ?>"/>

            <div class="es-search__buttons">
                <div class="es-button__wrap">
                    <input type="reset" class="es-button es-button-gray" value="<?php _e( 'Reset', 'es-plugin' ); ?>"/>
                </div>
                <div class="es-button__wrap">
                    <input type="submit" class="es-button es-button-orange-corner" value="<?php _e( 'Search', 'es-plugin' ); ?>"/>
                </div>
            </div>

            <?php do_action( 'es_after_search' ); ?>

        </form>

    </div>
<?php echo $args['after_widget'];
