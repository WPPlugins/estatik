<?php

/**
 * @file functions.php
 *
 * Implements functions for plugin.
 */

/**
 * Return current URL.
 *
 * @return string|void
 */
function es_get_current_url() {
    return set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
}

/**
 * Return estatik plugin logo path.
 *
 * @return mixed|void
 */
function es_logo_url() {
    return apply_filters( 'es_logo_url', ES_PLUGIN_URL . '/admin/assets/images/logo.png' );
}

/**
 * Return array of classes for list.
 *
 * @return mixed|void
 */
function es_get_list_classes() {
    global $es_settings;

    $list[] = 'es-listing';
    $list[] = 'es-layout-' . $es_settings->listing_layout;
    $list[] = 'hentry';

    $template = get_option( 'template' );

    if ( 'rectangulum' == $template ) {
        $list[] = 'entry-content';
    }

    return apply_filters( 'es_get_list_classes', $list);
}

/**
 * Display list class string.
 *
 * @return void
 */
function es_the_list_classes() {
    $classes = es_get_list_classes();
    echo ! empty( $classes ) ? implode(' ', $classes) : '';
}

/**
 * Return create property page uri.
 *
 * @return mixed|void
 */
function es_admin_property_add_uri() {
    return apply_filters( 'es_admin_property_list_uri', 'post-new.php?post_type=' . Es_Property::get_post_type_name() );
}

/**
 * Return data manager page uri.
 *
 * @return mixed|void
 */
function es_admin_data_manager_uri() {
    return apply_filters( 'es_admin_property_list_uri', 'admin.php?page=es_data_manager' );
}

/**
 * Return data manager page uri.
 *
 * @return mixed|void
 */
function es_admin_settings_uri() {
    return apply_filters( 'es_admin_property_list_uri', 'admin.php?page=es_settings' );
}

/**
 * Return Estatik admin listings URI.
 *
 * @return string
 */
function es_admin_property_list_uri() {
    return apply_filters( 'es_admin_property_list_uri', 'edit.php?post_type=' . Es_Property::get_post_type_name() );
}

/**
 * @retrun void
 */
function es_migration_set_executed() {
    update_option( 'es_migration_already_executed', true );
}

/**
 * @return bool
 */
function es_migration_already_executed() {
    return apply_filters( 'es_migration_already_executed', get_option( 'es_migration_already_executed' ) );
}

/**
 * @return array
 */
function es_need_migrate() {
    return Es_Property_Migration::get_prop_ids();
}

/**
 * Return estatik logo markup.
 *
 * @return string;
 */
function es_get_logo() {
    ob_start();

    do_action( 'es_before__logo' );
    echo "<div class='es-logo clearfix'><img src='" . es_logo_url() . "'><br>
            <span class='es-version'>" . __( 'Ver', 'es-plugin' ) . ". " . Estatik::getVersion() .  "</span></div>";
    do_action( 'es_after_logo');

    return ob_get_clean();
}

/**
 * Return property field value.
 *
 * @param $field
 * @param int $post
 * @return mixed|null|void
 */
function es_get_the_property_field( $field, $post = 0 ) {
    $post = get_post( $post );

    if ( ! empty( $post->ID ) ) {
        $es_property = es_get_property( $post->ID );

        $result = $es_property->{$field};

        if ( is_numeric( $result ) ) {
            $result = $result == (int) $result ? (int) $result : $result;
        }

        return apply_filters( 'es_get_the_' . $field, $result );
    }

    return null;
}

/**
 * Render property field.
 *
 * @param $field
 * @param string $before
 * @param string $after
 */
function es_the_property_field( $field, $before = '', $after = '' ) {
    $result = es_get_the_property_field( $field );

    echo ! empty( $result ) ? $before . $result . $after : null;
}

/**
 * Return bathrooms formatted string.
 *
 * @param int $post
 * @return null|string
 */
function es_get_the_formatted_bathrooms( $post = 0 ) {
    $value = es_get_the_property_field( 'bathrooms', $post );

    if ( ! empty( $value ) ) {
        // _n function doesn't work :(
        return $value == 1 ? sprintf( __( '%s bath', 'es-plugin' ), $value  ) :  sprintf( __( '%s baths', 'es-plugin' ), $value );
    }
}

/**
 * Display formatter bath string.
 *
 * @see es_get_the_formatted_bathrooms()
 *
 * @param string $before
 * @param string $after
 * @param bool $display_empty
 *
 * @return void|null|string
 */
function es_the_formatted_bathrooms( $before = '', $after = '', $display_empty = false ) {
    $value = es_get_the_formatted_bathrooms();

    if ( $display_empty || ( ! empty( $value ) ) ) {
        echo $before . $value . $after;
    }
}

/**
 * Return beds formatted string.
 *
 * @param int $post
 * @return null|string
 */
function es_get_the_formatted_bedrooms( $post = 0 ) {
    $value = es_get_the_property_field( 'bedrooms', $post );

    if ( ! empty( $value ) ) {
        // _n function doesn't work :(
        return $value == 1 ? sprintf( __( '%s bed', 'es-plugin' ), $value  ) :  sprintf( __( '%s beds', 'es-plugin' ), $value );
    }
}

/**
 * Display formatter beds string.
 *
 * @see es_get_the_formatted_bathrooms()
 *
 * @param string $before
 * @param string $after
 * @param bool $display_empty
 *
 * @return void|null|string
 */
function es_the_formatted_bedrooms( $before = '', $after = '', $display_empty = false ) {
    $value = es_get_the_formatted_bedrooms();

    if ( $display_empty || ( ! empty( $value ) ) ) {
        echo $before . $value . $after;
    }
}

/**
 * Display property title using {title_address} setting.
 *
 * @param string $before
 * @param string $after
 */
function es_the_title( $before = '', $after = '' ) {
    $result = es_get_the_title();
    echo ! empty( $result ) ? $before . $result . $after : null;
}

/**
 * Return address or title of the property using {title_address} setting.
 *
 * @param int $post
 * @return string
 */
function es_get_the_title( $post = 0 ) {
    global $es_settings;

    if ( $es_settings->title_address == 'address' ) {
        return es_get_the_property_field( 'address', $post );
    } else {
        return get_the_title( $post );
    }
}

/**
 * Display property address string.
 *
 * @param string $before
 * @param string $after
 */
function es_the_address( $before = '', $after = '' ) {
    global $es_settings;

    $result = es_get_the_property_field( 'address' );
    echo ! empty( $result ) && $es_settings->show_address ? $before . $result . $after : null;
}

/**
 * Display price using currency settings.
 *
 * @param string $before
 * @param string $after
 */
function es_the_formatted_price( $before = '', $after = '' ) {

    /** @var Es_Settings_Container $es_settings */
    global $es_settings;

    // Get property price.
    $price = es_get_the_property_field( 'price' );
    $call = es_get_the_property_field( 'call_for_price' );

    if ( $call && $es_settings->show_price ) {
        $result = '<span class="es-price">' . __( 'Call for price', 'es-plugin' ) . '</span>';
        echo $before . apply_filters( 'es_the_formatted_price', $result, $price, $call ) . $after;
        return;
    }

    // Get position of the currency.
    $position = $es_settings->currency_position;
    // Get currency name using currency code.
    $currency = $es_settings->get_label( 'currency', $es_settings->currency );
    // Get price format.
    $format = $es_settings->price_format;

    $price_temp = ! $price ? 0 : $price;

    $sup = !empty($format[0]) ? $format[0] : null;
    $dec = !empty($format[1]) ? $format[1] : null;

    $dec_num = $sup == ' ' ? 0 : 2;

    $price_temp = number_format($price_temp, $dec_num, $dec, $sup);
    $price_temp = $position == 'after' ? $price_temp . ' ' . $currency : $currency . ' ' . $price_temp;

    $result = '<span class="es-price">' . $price_temp . '</span>';

    echo ! empty( $price ) && $es_settings->show_price ? $before . apply_filters( 'es_the_formatted_price', $result, $price_temp ) . $after : null;
}

/**
 * Return property categories.
 *
 * @param $before
 * @param $sep
 * @param $after
 */
function es_the_categories( $before = '', $sep = ', ', $after = '' ) {
    the_terms( 0, 'es_category', $before, $sep, $after );
}

/**
 * @return mixed|void
 */
function es_get_standard_label_names() {
    return  apply_filters( 'es_install_standard_labels', array(
        '#00cbf0' => __( 'Featured', 'es-plugin' ),
        '#ff9600' => __( 'Hot', 'es-plugin'),
        '#2bbe0e' => __( 'Open House', 'es-plugin' ),
        '#9e9e9e' => __( 'Foreclosure', 'es-plugin' ),
    ) );
}

/**
 * @param $term_id
 * @param string $color
 * @return mixed|string
 */
function es_get_the_label_color(  $term_id, $color = '#9e9e9e' ) {
    $meta = get_term_meta( $term_id, 'es_color', true );
    return ! empty( $meta ) ? get_term_meta( $term_id, 'es_color', true ) : $color;
}

/**
 * Return property status list.
 *
 * @param string $before
 * @param string $sep
 * @param string $after
 * @param bool $echo
 * @return string
 */
function es_the_status_list( $before = '', $sep = ', ', $after = '', $echo = true ) {
    ob_start();
    the_terms( 0, 'es_status', $before, $sep, $after );
    $result = ob_get_clean();

    if ( $echo ) {
        echo $result;
    } else {
        return $result;
    }
}

/**
 * Overridden the_content function.
 * For now the_content function uses for execute [es_single] shortcode.
 * Use this function instead of the_content.
 *
 * @param null $more_link_text
 * @param bool $strip_teaser
 */
function es_the_content( $more_link_text = null, $strip_teaser = false ) {
    $content = get_the_content( $more_link_text, $strip_teaser );

    /**
     * Filters the post content.
     *
     * @since 0.71
     *
     * @param string $content Content of the current post.
     */
    $content = apply_filters( 'es_the_content', $content );
    $content = str_replace( ']]>', ']]&gt;', $content );
    echo $content;
}

add_filter( 'es_the_content', 'wptexturize'                       );
add_filter( 'es_the_content', 'convert_smilies',               20 );
add_filter( 'es_the_content', 'wpautop'                           );
add_filter( 'es_the_content', 'shortcode_unautop'                 );
add_filter( 'es_the_content', 'prepend_attachment'                );
add_filter( 'es_the_content', 'wp_make_content_images_responsive' );

/**
 * Return property status list.
 *
 * @param $before
 * @param $sep
 * @param $after
 * @return string
 */
function es_the_rent_period( $before = '', $sep = ', ', $after = '', $echo ) {
    ob_start();
    the_terms( 0, 'es_rent_period', $before, $sep, $after );
    $result = ob_get_clean();

    if ( $echo ) {
        echo $result;
    } else {
        return $result;
    }
}

/**
 * Return property status list.
 *
 * @param $before
 * @param $sep
 * @param $after
 */
function es_the_amenities( $before = '', $sep = ', ', $after = '' ) {
    the_terms( 0, 'es_amenities', $before, $sep, $after );
}

/**
 * Return list of terms of amenities taxonomy.
 *
 * @return array|false|WP_Error
 */
function es_get_the_amenities( $post = 0 ) {
    return get_the_terms( $post, 'es_amenities' );
}

/**
 * @param int $post
 * @return array|false|WP_Error
 */
function es_get_the_features( $post = 0 ) {
    return get_the_terms( $post, 'es_feature' );
}

/**
 * Display list of property types.
 *
 * @param string $before
 * @param string $sep
 * @param string $after
 * @param bool $echo
 * @return void|string
 */
function es_the_types( $before = '', $sep = ', ', $after = '', $echo = true ) {
    ob_start();
    the_terms( 0, 'es_type', $before, $sep, $after );
    $result = ob_get_clean();

    if ( $echo ) {
        echo $result;
    } else {
        return $result;
    }
}

/**
 * Render property formatted area field with units.
 *
 * @param string $before
 * @param string $after
 * @param bool $echo
 *
 * @return void|string
 */
function es_the_formatted_area( $before = '', $after = '', $echo = true ) {
    /** @var Es_Settings_Container $es_settings */
    global $es_settings;

    $es_property = es_get_property( null );
    $fields = $es_property::get_fields();

    $result = es_get_the_property_field( 'area' );
    $unit = ! empty( $fields['area']['units'] ) ? es_get_the_property_field( $fields['area']['units'] ) : null;
    $unit = $unit ? $unit : $es_settings->unit;
    $unit = $unit ? $es_settings->get_label( 'unit', $unit) : null;

    $result = ! empty( $result ) ?
        $before . apply_filters( 'es_the_formatted_area', $result . ' ' . $unit, $result ) . $after : null;

    if ( $echo ) {
        echo $result;
    } else {
        return $result;
    }
}

/**
 * @param string $size
 * @return string
 */
function es_get_default_thumbnail( $size = 'thumbnail' ) {
    global $es_settings;

    $attachment_id = $es_settings->thumbnail_attachment_id;

    if ( ! $es_settings->thumbnail_attachment_id ) {
        if ( ! function_exists( 'wp_handle_upload' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
            require_once( ABSPATH . 'wp-admin/includes/media.php' );
        }

        $thumbnail = ES_PLUGIN_URL . 'assets/images/thumbnail.png';
        $upload_dir = wp_upload_dir();
        $file['name'] = basename( $thumbnail );
        $file['tmp_name'] = download_url( $thumbnail );

        $file = wp_handle_sideload( $file, array( 'test_form' => false ) );

        if ( empty( $file['error'] ) ) {
            $wp_filetype = wp_check_filetype( basename( $file['file'] ), null );

            $attachment = array(
                'guid' => $upload_dir['baseurl'] . ES_DS . _wp_relative_upload_path( $file['file'] ),
                'post_mime_type' => $wp_filetype['type'],
                'post_title' => preg_replace('/\.[^.]+$/', '', basename( $file['file'] ) ),
                'post_content' => '',
                'post_status' => 'inherit'
            );

            $attachment_id = wp_insert_attachment( $attachment, $file['file'] );

            $attach_data = wp_generate_attachment_metadata( $attachment_id,  get_attached_file( $attachment_id ) );
            wp_update_attachment_metadata( $attachment_id,  $attach_data );

            $es_settings->saveOne( 'thumbnail_attachment_id', $attachment_id );
            $attachment_id = $es_settings->thumbnail_attachment_id;
        } else {
            $attachment_id = null;
        }
    }

    return wp_get_attachment_image( $attachment_id, $size );
}

/**
 * Render property formatted lot size field with units.
 *
 * @param string $before
 * @param string $after
 * @param bool $echo
 * @return mixed|null|string|void
 */
function es_the_formatted_lot_size( $before = '', $after = '', $echo = true ) {
    /** @var Es_Settings_Container $es_settings */
    global $es_settings;

    $es_property = es_get_property( null );
    $fields = $es_property::get_fields();

    $result = es_get_the_property_field( 'lot_size' );
    $unit = ! empty( $fields['lot_size']['units'] ) ? es_get_the_property_field( $fields['lot_size']['units'] ) : null;
    $unit = $unit ? $unit : $es_settings->unit;
    $unit = $unit ? $es_settings->get_label( 'unit', $unit ) : null;

    $result = ! empty( $result ) ?
        $before . apply_filters( 'es_the_formatted_lot_size', $result . ' ' . $unit, $result ) . $after : null;

    if ( $echo ) {
        echo $result;
    } else {
        return $result;
    }
}

/**
 * Return property map block.
 *
 * @return void
 */
function es_the_map() {
    echo apply_filters( 'es_the_map', '<div id="es-google-map" style="width:100%; height:300px;"></div>' );
}

/**
 * Return property added date.
 *
 * @param string $before
 * @param string $after
 * @param bool $echo
 * @return string|void
 */
function es_the_date( $before = '', $after = '', $echo = true ) {
    global $es_settings;

    if ( $es_settings->date_added ) {
        return the_date( $es_settings->date_format, $before, $after, $echo );
    }
}

/**
 * Get property.
 *
 * @param $post_id
 * @return Es_Property
 */
function es_get_property( $post_id ) {
    return apply_filters( 'es_get_property', new Es_Property( $post_id ) );
}

/**
 * @param $from
 * @param $to
 * @param $value
 *
 * @return int
 */
function es_prepare_unit( $from, $to, $value ) {

    // OH. MY. GOD. Switch in the switch. Perfect.

    switch ( $from ) {

        case 'hectares':
            switch ( $to ) {
                case 'sq_ft':
                    $value = $value * 107639.104;
                    break;

                case 'sq_m':
                    $value = $value * 10000;
                    break;

                case 'acres':
                    $value = $value * 2.4710538146717;
                    break;
            }
            break;

        case 'sq_ft':
            switch ( $to ) {
                case 'hectares':
                    $value = $value / 107639.104;
                    break;

                case 'sq_m':
                    $value = $value / 10;
                    break;

                case 'acres':
                    $value = $value / 43560;
                    break;
            }
            break;

        case 'sq_m':
            switch ( $to ) {
                case 'hectares':
                    $value = $value / 10000;
                    break;

                case 'sq_ft':
                    $value = $value * 10;
                    break;

                case 'acres':
                    $value = $value / 4046.8564300507887;
                    break;
            }
            break;

        case 'acres':
            switch ( $to ) {
                case 'hectares':
                    $value = $value / 2.4710538146717;
                    break;

                case 'sq_ft':
                    $value = $value * 43560;
                    break;

                case 'sq_m':
                    $value = $value * 4046.8564300507887;
                    break;
            }
            break;

        default:
            $value = false;
    }

    return apply_filters( 'es_prepare_unit', $value, $from, $to, $value );
}

/**
 * Return HTML based content for ajax action for calculate units.
 *
 * @return void
 */
function es_ajax_calculate_units() {

    if ( ! empty( $_POST['unit'] ) && ! empty( $_POST['val'] ) ) {
        /** @var $es_settings Es_Settings_Container */
        global $es_settings; $content = null;

        $template = "<b>{unit}: </b>{value}</br>";

        foreach ( $es_settings::get_setting_values('unit') as $key => $setting_value ) {
            $unit = es_prepare_unit( $_POST['unit'], $key, $_POST['val'] );

            if ( $unit === false ) continue;

            $content .= strtr( $template, array(
                '{value}' => $unit,
                '{unit}' => $es_settings->get_label( 'unit', $key )
            ) );
        }

        wp_die( json_encode( array(
            'status' => true,
            'content' => $content,
        ) ) );
    }
}

add_action( 'wp_ajax_es_calculate_units', 'es_ajax_calculate_units' );

/**
 * Render property main thumbnail.
 *
 * @param string $size
 */
function es_the_post_thumbnail( $size = 'thumbnail' ) {
    global $post;

    $property = es_get_property( $post->ID );
    $images = $property->gallery;

    echo ! empty( $images[0] ) ? wp_get_attachment_image( $images[0], $size ) : null;
}
