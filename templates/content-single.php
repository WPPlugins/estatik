<?php
/**
 * @var Es_Property $es_property
 * @var Es_Settings_Container $es_settings
 */

global $es_property, $es_settings; ?>

<?php do_action( 'es_before_single_content' ); ?>
    <div class="es-wrap">
        <ul class="es-single es-single-<?php echo $es_settings->single_layout; ?>">

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <h2>
                    <?php es_the_title( '<div class="es-title">', '</div>' ); ?>

                    <div class="es-cat-price">
                        <?php es_the_categories( '<span class="es-category-items">', '', '</span>' ) ?>
                        <?php es_the_formatted_price( '<span class="es-price">', '</span>' ); ?>
                    </div>
                </h2>

                <?php es_the_address( '<div class="es-address">', '</div>' ); ?>

                <?php do_action( 'es_single_tabs' ); ?>

                <div class="es-info" id="es-info">
                    <?php do_action( 'es_single_info' ); ?>
                </div>

                <div class="es-tabbed">

                    <?php if ( get_the_content() ) : ?>
                        <div class="es-tabbed-item es-description">
                            <h3><?php _e( 'Description', 'es-plugin' ); ?></h3>
                            <?php es_the_content(); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ( ! empty( $es_property->latitude ) && ! empty( $es_settings->google_api_key ) ) : ?>
                        <div class="es-tabbed-item es-map" id="es-map">
                            <h3><?php _e( 'View on map / Neighborhood', 'es-plugin' ); ?></h3>
                            <?php do_action( 'es_map' ); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ( Es_Property_Single_Page::get_features_data() ) : ?>
                        <div class="es-tabbed-item es-features" id="es-features">
                            <h3><?php _e( 'Features', 'es-plugin' ); ?></h3>
                            <?php do_action( 'es_property_single_features' ); ?>
                        </div>
                    <?php endif; ?>

                </div>

                <?php do_action( 'es_single_top_button' ); ?>
            </article>
        </ul>
    </div>
<?php do_action( 'es_after_single_content' );
