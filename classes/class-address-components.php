<?php

/**
 * Class ES_Address_Components
 */
class ES_Address_Components
{
    /**
     * Check if address component is exists.
     *
     * @param $long_name
     * @param $type
     * @return null|string
     */
    public static function check_component( $long_name, $type )
    {
        global $wpdb;

        return $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id
                    FROM " . $wpdb->prefix . "address_components  
                    WHERE `long_name` = %s 
                    AND `type` = %s LIMIT 1", array( $long_name, $type ) )
        );
    }

    /**
     * Save / update component into table.
     *
     * @param $data
     * @return int
     */
    public static function save_component( $data )
    {
        global $wpdb;

        $data->type = $data->types[0];

        $data = (array) $data;
        unset( $data['types'] );

        if ( $id = static::check_component( $data['long_name'], $data['type'] ) ) {
            $wpdb->update( $wpdb->prefix . 'address_components', $data, array( 'id' => $id ) );
        } else {
            $wpdb->insert( $wpdb->prefix . 'address_components', $data );
        }

        return empty( $id ) ? $wpdb->insert_id : $id;
    }

    /**
     * Return address component using ID.
     *
     * @param $id
     * @return array|null|object|void
     */
    public static function get_component( $id )
    {
        global $wpdb;
        return $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "address_components WHERE id = '$id'");
    }

    /**
     * Save property component to the database.
     *
     * @param $property_id
     * @param $component_id
     */
    public static function save_property_component( $property_id, $component_id )
    {
        if ( $component = static::get_component( $component_id ) ) {
            update_post_meta( $property_id, '_address_component_' . $component->type, $component_id );
        }
    }

    /**
     * Get component from post meta.
     *
     * @param $property_id
     * @param $type
     * @return array|null|object|void
     */
    public static function get_property_component( $property_id, $type )
    {
        $component_meta = get_post_meta( $property_id, '_address_component_' . $type, true );
        return empty( $component_meta ) ? null : static::get_component( $component_meta );
    }

    /**
     * Get list of components using component type.
     *
     * @param $type
     * @return array|null|object
     */
    public static function get_component_list( $type ) {
        global $wpdb;
        return $wpdb->get_results(
            $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "address_components WHERE `type` = %s 
                             GROUP BY long_name ORDER BY long_name", array( $type ) )
        );
    }

    /**
     * Return google data using address string or coordinates.
     *
     * @param array|string $data
     *    If passed array - array( lat => n, lng => m )
     *    If passed string - it's simple address string.
     *
     * @return stdClass|null
     */
    public static function get_google_components( $data ) {
        // Generate google url using coordinates.
        if ( is_array( $data ) && isset( $data[0] ) && isset( $data[1] ) ) {
            $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.trim( $data[0] ).','.trim( $data[1] ).'&sensor=false';
            // Generate google url using address string.
        } else if ( is_string( $data ) ) {
            $url = "http://maps.google.com/maps/api/geocode/json?sensor=false&address=" . str_replace( ' ', '+', $data );
        }

        // Get google data.
        if ( ! empty( $url ) ) {
            $json = @file_get_contents( $url );

            return json_decode( $json );
        }

        return null;
    }
}
