<?php

/**
 * Class Es_Property
 *
 * @property string $address
 * @property float $price
 * @property bool $call_for_price
 * @property bool $bedrooms
 * @property bool $bathrooms
 * @property bool $floors
 * @property float $lot_size
 * @property float $area
 * @property float $year_built
 * @property float $latitude
 * @property float $longitude
 */
class Es_Property extends Es_Post
{
    /**
     * @inheritdoc
     */
    public function get_entity_prefix()
    {
        return apply_filters( 'es_property_entity_prefix', 'es_property_' );
    }

    /**
     * Save property address components.

     * @param $data
     */
    public function save_address_components( $data )
    {
        if ( ! empty( $data ) ) {
            foreach ( $data as $item ) {
                $component_id = ES_Address_Components::save_component( $item );
                if ( ! empty( $component_id ) ) {
                    ES_Address_Components::save_property_component( $this->getID(), $component_id );
                }
            }
        }
    }

    /**
     * Return custom fields data.
     *
     * @return mixed
     */
    public function get_custom_data()
    {
        return get_post_meta( $this->_id, 'es_custom_data' );
    }

    /**
     * Save property fields.
     *
     * @param $data
     */
    public function save_fields( $data )
    {
        if ( ! empty( $data ) ) {
            $units = array();
            $fields = static::get_fields();
            $data = apply_filters( 'es_before_save_property_data', $data, $this );

            // Save address components.
            if ( ! empty( $data['address_components'] ) ) {
                $this->save_address_components( json_decode( $data['address_components'] ) );
            }

            // Save another fields.
            foreach ( $fields as $key => $field ) {
                $value = isset( $data[ $key ] ) ? $data[$key] : null;

                $value = $key == 'call_for_price' && ! $value ? 0 : $value;
                $value = $key == 'video' ? esc_attr( $value ) : $value;

                $this->save_field_value( $key, $value );

                if ( ! empty( $field['units'] ) && ! empty( $value ) ) {
                   $units[ $key ] = array(
                       'units' => $fields[ $field[ 'units' ] ]['values'],
                       'value' => $value,
                       'unit' => $data[ $field[ 'units' ] ],
                   );
                }
            }

            if ( ! empty( $units ) ) {
                $this->save_units( $units );
            }
        }
    }

    /**
     * @param $units
     */
    public function save_units( array $units )
    {
        if ( ! empty( $units ) ) {
            foreach ( $units as $field => $item ) {
                if ( empty( $item['units'] ) ) continue;

                foreach ( $item['units'] as $unit => $label ) {
                    if ( $item['unit'] == $unit ) {
                        $value = $item['value'];
                    } else {
                        $func = apply_filters( 'es_prepare_unit_callback', 'es_prepare_unit', $unit, $item, $units, $this );
                        if ( function_exists( $func ) ) {
                            $value = call_user_func( $func, $item['unit'], $unit, $item['value'] );
                        }
                    }

                    if ( ! empty( $unit ) && ! empty( $value ) ) {
                        $this->save_field_value( $field . '_' . $unit, $value );
                    }
                }
            }
        }
    }

    /**
     * Save custom property fields.
     *
     * @param $data
     */
    public function save_custom_data( $data )
    {
        if ( ! empty( $data ) ) {
            delete_post_meta( $this->getID(), 'es_custom_data' );

            foreach ( $data as $key => $value ) {
                if ( ! empty( $key ) && ! empty( $value ) ) {
                    add_post_meta( $this->getID(), 'es_custom_data', array( $key => $value ), false );
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public static function get_fields()
    {
        global $es_settings;

        $fields = array(

            'price' => array(
                'type' => 'number',
                'tab' => 'es-basic-info',
                'search_range_mode' => true,
            ),

            'call_for_price' => array(
                'type' => 'checkbox',
                'tab' => 'es-basic-info',
                'options' => array( 'value' => 1, 'class' => 'es-switch-input' ),
            ),

            'bedrooms' => array(
                'type' => 'number',
                'tab' => 'es-basic-info',
                'search_range_mode' => true,
            ),

            'bathrooms' => array(
                'type' => 'number',
                'tab' => 'es-basic-info',
                'search_range_mode' => true,
                'options' => array( 'step' => 0.5 ) ,
            ),

            'floors' => array(
                'type' => 'number',
                'tab' => 'es-basic-info',
                'search_range_mode' => true,
            ),

            'area' => array(
                'type' => 'number',
                'tab' => 'es-basic-info',
                'search_range_mode' => true,
                'units' => 'area_unit',
            ),

            'area_unit' => array(
                'type' => 'list',
                'values' => $es_settings::get_setting_values( 'unit' ),
                'template' => true,
                'label' => false,
            ),

            'lot_size' => array(
                'type' => 'number',
                'tab' => 'es-basic-info',
                'search_range_mode' => true,
                'units' => 'lot_size_unit',
            ),

            'lot_size_unit' => array(
                'type' => 'list',
                'values' => $es_settings::get_setting_values( 'unit' ),
                'template' => true,
                'label' => false,
            ),

            'year_built' => array(
                'type' => 'text',
                'tab' => 'es-basic-info'
            ),

            'address' => array(
                'type' => 'text',
                'tab' => 'es-address',
                'options' => array( 'placeholder' => __( 'Address, City, ZIP', 'es-plugin' ) )
            ),

            'latitude' => array(
                'type' => 'number',
                'tab' => 'es-address', 'options' => array( 'step' => 'any' ),
            ),

            'longitude' => array(
                'type' => 'number',
                'tab' => 'es-address',
                'options' => array( 'step' => 'any' ),
            ),

            'address_components' => array(
                'type' => 'hidden',
                'tab' => 'es-address',
                'label' => false,
            ),

            'custom' => array(
                'type' => 'custom',
                'tab' => 'es-basic-info',
                'template' => ES_PLUGIN_PATH . ES_DS . 'admin' . ES_DS . 'templates' .
                    ES_DS . 'property' . ES_DS . 'custom-fields.php',
            ),

            'gallery' => array(
                'type' => 'custom',
                'tab' => 'es-media',
                'template' => ES_PLUGIN_PATH . ES_DS . 'admin' . ES_DS . 'templates' .
                    ES_DS . 'property' . ES_DS . 'media.php',
            ),

            'country' => array(
                'type' => 'list',
                'values' => array( __( 'Country', 'es-plugin' ) ),
                'options' => array(
                    'class' => 'js-es-location',
                    'data-type' => Es_Search_Location::LOCATION_COUNTRY_TYPE,
                    'disabled' => 'disabled'
                ),
            ),

            'state' => array(
                'type' => 'list',
                'values' => array( __( 'State', 'es-plugin' ) ),
                'options' => array(
                    'class' => 'js-es-location',
                    'data-type' => Es_Search_Location::LOCATION_STATE_TYPE,
                    'disabled' => 'disabled'
                ),
            ),

            'city' => array(
                'type' => 'list',
                'values' => array( __( 'City', 'es-plugin' ) ),
                'options' => array(
                    'class' => 'js-es-location',
                    'data-type' => Es_Search_Location::LOCATION_CITY_TYPE,
                    'disabled' => 'disabled'
                ),
            ),

            'neighborhood' => array(
                'type' => 'list',
                'values' => array( __( 'Neighborhood', 'es-plugin' ) ),
                'options' => array(
                    'class' => 'js-es-location',
                    'data-type' => Es_Search_Location::LOCATION_NEIGHBORHOOD_TYPE,
                    'disabled' => 'disabled'
                )
            ),

            'street' => array(
                'type' => 'list',
                'values' => array( __( 'Street', 'es-plugin' ) ),
                'options' => array(
                    'class' => 'js-es-location',
                    'data-type' => Es_Search_Location::LOCATION_STREET_TYPE,
                    'disabled' => 'disabled'
                ),
            ),
        );

        $labels = self::get_labels_list();

        if ( ! empty( $labels ) ) {
            foreach ( $labels as $term ) {
                $fields = Es_Object::push_column( array(
                    $term->slug => array(
                        'type' => 'checkbox',
                        'tab' => 'es-basic-info',
                        'options' => array(
                            'value' => 1,
                            'class' => 'es-switch-input'
                        ),
                    ),
                ), $fields, 2 );
            }
        }

        return apply_filters( 'es_property_get_fields', $fields);
    }

    /**
     * Return list of labels.
     *
     * @return array|int|WP_Error
     */
    public static function get_labels_list()
    {
        return get_terms( array( 'taxonomy' => 'es_labels', 'hide_empty' => false ) );
    }

    /**
     * Save property data. Used for save_post hook.
     *
     * @param $post_id
     * @param $post
     *
     * @return void
     */
    public static function save( $post_id, $post )
    {
        if ( $post->post_type == static::get_post_type_name() ) {
            // Initialize property object.
            $property = new static( $post_id );
            // Get property fields data from the post request.
            $data = filter_input( INPUT_POST, 'property', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
            // Save these fields.
            $property->save_fields( $data );

            // Saving custom property data fields (that created manually).
            $keys = filter_input(INPUT_POST, 'es_custom_key', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
            $values = filter_input(INPUT_POST, 'es_custom_value', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

            if ( ! empty( $keys ) && ! empty( $values ) ) {
                $property->save_custom_data( array_combine( $keys, $values ) );
            }
        }
    }

    /**
     * Return post type name.
     *
     * @return mixed|void
     */
    public static function get_post_type_name()
    {
        return apply_filters( 'es_property_post_type_name', 'properties' );
    }

    /**
     * Find properties ids using address.
     *
     * @param $address
     * @return array
     */
    public static function find_by_address( $address )
    {
        global $wpdb;

        return $wpdb->get_col( "SELECT post_id 
            FROM $wpdb->postmeta 
            WHERE meta_key = 'es_property_address' 
            AND meta_value 
            LIKE '%" . $address . "%'" );
    }
}
