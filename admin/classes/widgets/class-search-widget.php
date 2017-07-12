<?php

/**
 * Class Es_Search_Widget
 */
class Es_Search_Widget extends Es_Widget
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct( 'es_search_widget' , __( 'Estatik Search', 'es-plugin' ) );
    }

    /**
     * Return layouts of this widget.
     *
     * @return array
     */
    public static function get_layouts()
    {
        return apply_filters( 'es_get_search_widget_layouts', array( 'horizontal', 'vertical' ) );
    }

    /**
     * Return search fields.
     *
     * @return array
     */
    public static function get_widget_fields()
    {
        return apply_filters( 'es_get_widget_fields', array(
            'price',
            'bedrooms',
            'bathrooms',
            'floors',
            'area',
            'lot_size',
            'country',
            'state',
            'city',
            'street',
            'address',
        ) );
    }

    /**
     * Function for register widget.
     *
     * @return void
     */
    public static function register()
    {
        register_widget( 'Es_Search_Widget' );
    }

    /**
     * Render field for search widget.
     *
     * @param $name
     * @return string|boolean
     */
    public static function render_field( $name ) {
        $property = es_get_property( null );

        // If input data is empty.
        if ( empty( $name ) || ! $field = $property::get_field_info( $name ) ) return false;

        // Field content string;
        $content = null;
        // Current field value.
        $value = isset( $_GET['es_search'][ $name ] ) ? $_GET['es_search'][ $name ] : null;
        // Field options.
        $options = ' ';

        if ( empty( $field['options']['id'] ) ) {
            $field['options']['id'] = 'es-search-' . $name . '-input';
        }

        // Set value as data attribute for ajax fields.
        if ( ! empty( $value ) && ! is_array( $value ) ) {
            $field['options']['data-value'] = $value;
            $field['options']['value'] = $value;
        } else {
			$field['options']['value'] = '';
		}

        if ( ! empty( $field['options'] ) ) {
            if ( ! empty( $field['search_range_mode'] ) ) {
                unset( $field['options']['value'] );
            }

            foreach ( $field['options'] as $key => $option ) {
                $options .= $key . '="' . $option . '" ';
            }
        }

        // Generate label if empty.
        if ( isset( $field['label'] ) && $field['label'] == false ) {
			$field['label'] = '';
        } else {
			$field['label'] = '<div class="es-field__label">
				<label for="' . $field['options']['id'] . '">' . __( Es_Html_Helper::generate_label( $name ), 'es-plugin' ) . '</label></div>';
        }

        $field['type'] = ! empty( $field['search_range_mode'] ) ? 'text' : $field['type'];

        $class_unit = null;

        if ( ! empty( $field['units'] ) ) {
            $class_unit = 'es-field__wrap--units';
        }

        if ( empty( $field['template'] ) ) {
            $content .= '<div class="es-field__wrap ' . $class_unit . '">';
        }

        switch ( $field['type'] ) {
            case 'list':
                $content .= '<select name="es_search[' . $name . ']" ' . $options .'>';
                if ( ! empty( $field['values'] ) ) {
                    foreach ( $field['values'] as $value => $label ) {
                        $content .= '<option value="' . $value . '" ' . selected( $value, $field['options']['value'], false ) . '>' . $label . '</option>';
                    }
                }

                $content .= '</select>';
                break;

            case 'radio':
            case 'checkbox':
                $content .= '<input type="' . $field['type'] . '" name="es_search[' . $name . ']" ' . $options . ' ' . checked( $value, $field['options']['value'], false ) . '/>';
                break;

            default:
                if ( ! empty( $field['search_range_mode'] ) ) {
                    $min = ! empty( $value['min'] ) ? $value['min'] : '';
                    $max = ! empty( $value['max'] ) ? $value['max'] : '';
                    $content .= '<div class="es-field__range"><input type="' . $field['type'] . '" placeholder="min" name="es_search[' . $name . '][min]" ' . $options .' value="' . $min . '"/>';
                    $content .= '<input type="' . $field['type'] . '" placeholder="max" name="es_search[' . $name . '][max]" ' . $options .' value="' . $max . '"/>';
                } else {
                    $content .= '<input type="' . $field['type'] . '" name="es_search[' . $name . ']" ' . $options .'/>';
                }
        }

        if ( ! empty( $field['units'] ) ) {
            $content .= self::render_field( $field['units'] );
        }

        if ( ! empty( $field['search_range_mode'] ) ) {
            $content .= '</div>';
        }

        if ( empty( $field['template'] ) ) {
            $content .= '</div>';
        }

        $content = $field['label'] . $content;

        return apply_filters( 'es_search_render_field', $content, $field, $name );
    }

    /**
     * Return location items for search widget.
     *
     * @return void
     */
    public static function get_location_items()
    {
        $response = null;

        if ( !empty( $_POST['status'] ) && $_POST['status'] == 'initialize' ) {
            $response = ES_Address_Components::get_component_list( $_POST['type'] );
        } else if ( !empty( $_POST['status'] ) && $_POST['status'] == 'dependency' ) {
            $response = Es_Search_Location::get_related_location( $_POST['type'], $_POST['val'] );
        }

        $response = apply_filters( 'es_search_get_location_items_response', $response );

        wp_die( json_encode( $response ) );
    }

    /**
     * Customize standard search query.
     *
     * @param WP_Query $query
     * @return void
     */
    public static function pre_get_posts( $query )
    {
        // If query is search.
        if ( ! empty( $_GET['es_search'] ) && is_array( $_GET['es_search'] ) && $query->is_search && ! is_admin() ) {
            $meta_query = array();

            $property = es_get_property( null );

            foreach ( $_GET['es_search'] as $field => $value ) {
                if ( empty( $value ) ) continue;

                switch ( $field ) {
                    case 'country':
                        $meta_query[] = array(
                            'key' => '_address_component_country',
                            'value' => $value,
                            'compare' => '=',
                        );
                        break;

                    case 'neighborhood':
                        $meta_query[] = array(
                            'key' => '_address_component_neighborhood',
                            'value' => $value,
                            'compare' => '=',
                        );
                        break;

                    case 'city':
                        $meta_query[] = array(
                            'key' => '_address_component_locality',
                            'value' => $value,
                            'compare' => '=',
                        );
                        break;

                    case 'state':
                        $meta_query[] = array(
                            'key' => '_address_component_administrative_area_level_1',
                            'value' => $value,
                            'compare' => '=',
                        );
                        break;

                    case 'street':
                        $meta_query[] = array(
                            'key' => '_address_component_route',
                            'value' => $value,
                            'compare' => '=',
                        );
                        break;

                    case 'featured':
                    case 'hot':
                    case 'foreclosure':
                    case 'open_house':
                        $meta_query[] = array(
                            'key' => $property->get_entity_prefix() . $field,
                            'value' => $value,
                            'compare' => '=',
                        );
                        break;

                    case 'bedrooms':
                    case 'bathrooms':
                    case 'area':
                    case 'lot_size':
                    case 'floors':
                    case 'price':

                        if ( $field == 'price' ) {
                            $meta_query[] = array( 'compare' => '=', 'key' => 'es_property_call_for_price', 'value' => 0 );
                        }

                        $field_key = $property->get_entity_prefix() . $field;

                        $field_info = $property::get_field_info( $field );

                        if ( ! empty( $field_info['units'] ) ) {
                            if ( ! empty( $_GET['es_search'][ $field_info['units'] ] ) ) {
                                $field_key = $property->get_entity_prefix() . $field . '_' . $_GET['es_search'][ $field_info['units'] ];
                            }
                        }

                        if ( ! empty( $value['min'] ) ) {
                            $meta_query[] = array(
                                'key' => $field_key,
                                'value' => (int)$value['min'],
                                'compare' => '>=',
                                'type' => 'NUMERIC',
                            );
                        }

                        if ( ! empty( $value['max'] ) ) {
                            $meta_query[] = array(
                                'key' => $field_key,
                                'value' => (int)$value['max'],
                                'compare' => '<=',
                                'type' => 'NUMERIC',
                            );
                        }

                        break;

                    case 'address':
                        // Create array from address string using delimiters.
                        if ( $output = preg_split( "/[,\s]/", $value ) ) {

                            if ( $output ) {
                                $ids = array();

                                foreach ( $output as $key => $address_part ) {
                                    if ( empty( $address_part ) ) continue;
                                    $ids = array_merge( $ids, $property::find_by_address( $address_part ) );
                                }

                                if ( ! empty( $ids ) ) {
                                    $query->set( 'post__in', $ids );
                                }
                            }
                        }
                        break;
                }
            }

            if ( $meta_query ) {
                $query->set( 'meta_query', $meta_query );
            }

        }
    }

    /**
     * @inheritdoc
     */
    protected function get_widget_template_path()
    {
        return ES_PLUGIN_PATH . '/admin/templates/widgets/es-search-widget.php';
    }

    /**
     * @return string
     */
    protected function get_widget_form_template_path()
    {
        return ES_PLUGIN_PATH . '/admin/templates/widgets/es-search-widget-form.php';
    }
}

add_action( 'widgets_init', array( 'Es_Search_Widget', 'register' ) );
add_action( 'wp_ajax_nopriv_es_get_location_items', array( 'Es_Search_Widget', 'get_location_items' ) );
add_action( 'wp_ajax_es_get_location_items', array( 'Es_Search_Widget', 'get_location_items' ) );
add_action( 'pre_get_posts', array( 'Es_Search_Widget', 'pre_get_posts' ), 1 );
