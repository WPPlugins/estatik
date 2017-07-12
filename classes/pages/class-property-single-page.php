<?php

/**
 * Class Es_Property_Single_Page
 */
class Es_Property_Single_Page extends Es_Object
{
    /**
     * Adding actions for single property page.
     */
    public function actions()
    {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
        add_action( 'es_property_single_features', array( $this, 'single_features' ) );
        add_action( 'es_single_gallery', array( $this, 'single_gallery' ) );
        add_action( 'es_single_fields', array( $this, 'single_fields' ) );
        add_action( 'es_single_tabs', array( $this, 'single_tabs' ) );
        add_action( 'es_map', array( $this, 'map' ) );
        add_action( 'es_single_info', array( $this, 'single_info' ) );
        add_action( 'es_single_top_button', array( $this, 'single_top_button' ) );
    }

    /**
     * Adding filters for single property page.
     *
     * @return void
     */
    public function filters()
    {
        add_filter( 'es_global_js_variables', array( $this, 'global_js_variables' ), 10, 1 );
        add_filter( 'the_content', array( $this, 'the_content' ) );
        add_filter( 'the_title', array( $this, 'the_title' ) );
    }

    /**
     * Enqueue scripts for single property page.
     *
     * @return void
     */
    public function enqueue_scripts()
    {
        global $post_type, $es_settings;

        $custom = 'assets/js/custom/';

        $property = es_get_property( null );

        if ( $property::get_post_type_name() == $post_type && is_single() ) {

            $deps = array ( 'jquery', 'es-front-script' );

            if ( ! empty( $es_settings->google_api_key ) ) {
                $deps[] = 'es-admin-map-script';
            }

            wp_register_script( 'es-front-single-script', ES_PLUGIN_URL . $custom . 'front-single.js', $deps );

            wp_enqueue_script( 'es-front-single-script' );

            wp_register_script( 'es-slick-script', 'http://cdn.jsdelivr.net/jquery.slick/1.6.0/slick.min.js', array (
                'jquery', 'es-front-script',
            ) );

            wp_enqueue_script( 'es-slick-script' );

            wp_localize_script( 'es-front-single-script', 'Estatik', Estatik::register_js_variables() );
        }
    }

    /**
     * Enqueue styles for single property page.
     *
     * @return void
     */
    public function enqueue_styles()
    {
        global $post_type;

        $custom = 'assets/css/custom/';

        $property = es_get_property( null );

        if ( $property::get_post_type_name() == $post_type && is_single() ) {
            wp_register_style( 'es-slick-style', 'http://cdn.jsdelivr.net/jquery.slick/1.6.0/slick.css' );
            wp_enqueue_style( 'es-slick-style' );

            wp_register_style( 'es-front-single-style', ES_PLUGIN_URL . $custom . 'front-single.css' );
            wp_enqueue_style( 'es-front-single-style' );
        }
    }

    /**
     * Property global javascript variables.
     *
     * @param $data
     * @return mixed
     */
    public function global_js_variables( $data )
    {
        global $post, $es_property;

        $property = es_get_property( null );

        if ( is_single() && $post->post_type == $property::get_post_type_name() ) {

            // Add property coordinates for google maps.
            $data['property'] = array(
                'lat' => (float) $es_property->latitude,
                'lon' => (float) $es_property->longitude,
            );
        }

        return $data;
    }

    /**
     * Return single property page tabs.
     *
     * @return mixed|void
     */
    public static function get_tabs()
    {
        return apply_filters( 'es_property_tabs', array(
            'es-info' => __( 'Basic facts', 'es-plugin' ),
            'es-map' => __( 'Neighborhood', 'es-plugin' ),
            'es-features' => __( 'Features', 'es-plugin' ),
        ) );
    }

    /**
     * Render features list.
     *
     * @return void
     */
    public function single_features()
    {
        $data = self::get_features_data();

        $template = apply_filters( 'es_features_list_template_path', ES_TEMPLATES . '/property/features-list.php' );

        if ( ! empty ( $data ) ) {
            foreach ( $data as $features_list_title => $features_list ) {
                include $template;
            }
        }
    }

    /**
     * Return features data.
     *
     * @return array
     */
    public static function get_features_data()
    {
        $data = array();

        if ( $features = es_get_the_features() ) {
            $data[ __( 'Features', 'es-plugin' ) ] = $features;
        }

        if ( $features = es_get_the_amenities() ) {
            $data[ __( 'Amenities', 'es-plugin' ) ] = $features;
        }

        return apply_filters( 'es_single_features_data', $data );
    }

    /**
     * Render gallery on property single page.
     *
     * @return void
     */
    public function single_gallery()
    {
        $template = apply_filters( 'es_single_gallery_template_path', ES_TEMPLATES . 'property/gallery.php' );
        include $template;
    }

    /**
     * Render property fields.
     *
     * @return void
     */
    public function single_fields()
    {
        $template = apply_filters( 'es_single_gallery_fields_path', ES_TEMPLATES . 'property/fields.php' );
        include $template;
    }

    /**
     * Return fields for render.
     *
     * @return mixed|void|array
     */
    public static function get_single_fields_data()
    {
        global $es_property;
        $custom = $es_property->get_custom_data();

        $data = array(
            __( 'Date added', 'es-plugin' ) => es_the_date('', '', false),
            __( 'Area size', 'es-plugin' ) => es_the_formatted_area( '', ' ', false ),
            __( 'Lot size', 'es-plugin' ) => es_the_formatted_lot_size( '', ' ', false ),
            __( 'Rent period', 'es-plugin' ) => es_the_rent_period('', ' ', '', false),
            __( 'Type', 'es-plugin' ) => es_the_types('', ' ', '', false),
            __( 'Status', 'es-plugin' ) => es_the_status_list('', ' ', '', false),
            __( 'Bedrooms', 'es-plugin' ) => es_get_the_property_field( 'bedrooms' ),
            __( 'Bathrooms', 'es-plugin' ) => es_get_the_property_field( 'bathrooms' ),
            __( 'Floors', 'es-plugin' ) => es_get_the_property_field( 'floors' ),
            __( 'Year built', 'es-plugin' ) => es_get_the_property_field( 'year_built' ),
        );

        // Include custom fields.
        if ( ! empty( $custom ) ) {
            foreach ( $custom as $value ) {
                $data[ __( key( $value ), 'es-plugin' ) ] = __( reset($value), 'es-plugin' );
            }
        }

        return apply_filters( 'es_single_fields_data', $data );
    }

    /**
     * Render single property tabs.
     *
     * @return void
     */
    public function single_tabs()
    {
        $template = apply_filters( 'es_single_tabs_template_path', ES_TEMPLATES . 'property/tabs.php' );
        include $template;
    }

    /**
     * Render single property map.
     *
     * @return void
     */
    public function map()
    {
        es_the_map();
    }

    /**
     * Render single property info.
     *
     * @return void
     */
    public function single_info()
    {
        do_action( 'es_single_gallery' );
        do_action( 'es_single_fields' );
    }

    /**
     * Render Top button.
     *
     * @return void
     */
    function single_top_button()
    {
        ob_start(); ?>
        <div class="es-top-arrow">
            <a href="body" class="es-top-link"><?php _e( 'To top', 'es-plugin' ); ?></a>
        </div><?php
        $result = ob_get_clean();

        echo apply_filters( 'es_single_top_button_markup', $result );
    }

    /**
     * @param $content
     * @return mixed
     */
    public function the_content( $content = null )
    {
        global $post_type;

        if ( ! empty( $post_type ) && $post_type == Es_Property::get_post_type_name() && is_single() ) {
            return do_shortcode( '[es_single]' );
        }
        return $content;
    }

    /**
     * Disable / enable main title.
     *
     * @param null $content
     * @return null|string
     */
    public function the_title( $content = null )
    {
        global $es_settings, $post_type;

        if ( ! empty( $post_type ) && $post_type == Es_Property::get_post_type_name() && is_single() ) {
            return $es_settings->hide_main_title ? '' : $content;
        }

        return $content;
    }
}
