<?php

/**
 * Class Es_Html_Helper
 */
class Es_Html_Helper
{
    /**
     * @param $id
     * @param $field
     * @return null|string
     * @throws Exception
     */
    public static function render_field( $id, $field )
    {
        $content = null;
        global $post_ID;
        $options = ' ';

        // Check for field type.
        if ( empty( $field['type'] ) ) {
            throw new Exception( __( "Field type parameter can't be empty", 'es-plugin' ) );
        }

        // Generate label if empty.
        if ( empty( $field['label'] ) || ( isset( $field['label'] ) && $field['label']  !== false ) ) {
            $field['label'] = static::generate_label( $id );
        } else {
            $field['label'] = ! empty( $field['label'] ) ? $field['label'] : '';
        }

        if ( empty( $field['template'] ) ) {
            $field['label'] = ! empty( $field['label'] ) ? __( $field['label'], 'es-plugin' ) : '';
            $content = "<div class='es-field es-field-" . $field['type'] . " es-field-" . $id . "'><div class='es-field__label'>" . $field['label'] . "</div><div class='es-field__content'>";
//            $content = '<p class="property-data-field es-field-type-' . $field['type'] . '"><label><span class="es-settings-label">' . $field['label'] . '</span>';
        }

        $property = es_get_property( $post_ID );

        $value = $property->$id;

        $field['options']['value'] = ! empty( $field['options']['value'] ) ? $field['options']['value'] : $value;

        if (empty($field['options']['id'])) {
            $field['options']['id'] = 'es-' . $id . '-input';
        }

        if ( ! empty( $field['options'] ) ) {
            foreach ( $field['options'] as $key => $option ) {
                if ( $key == 'value' && is_array( $option ) ) continue;
                $options .= $key . '="' . $option . '" ';
            }
        }

        switch ( $field['type'] ) {
            case 'list':
                if ( ! empty( $field['values'] ) ) {
                    $content .= '<select name="property[' . $id . ']" ' . $options .'>';
                    foreach ( $field['values'] as $value => $label ) {
                        $content .= '<option value="' . $value . '" ' . selected( $value, $field['options']['value'], false ) . '>' . $label . '</option>';
                    }
                    $content .= '</select>';
                } else {
                    return;
                }

                break;

            case 'custom':
                include( $field['template'] );

                break;

            case 'radio':
            case 'checkbox':
                $content .= '<input type="' . $field['type'] . '" name="property[' . $id . ']" id="es-' . $id .'-input"' . $options . ' ' . checked( $value, $field['options']['value'], false ) . '/>';

                break;

            default:
                $content .= '<input type="' . $field['type'] . '" name="property[' . $id . ']" id="es-' . $id .'-input"' . $options .'/>';
        }

        if ( ! empty( $field['units'] ) ) {
            if ( ! empty( $property  ) ) {
                $fields = $property::get_fields();
                $content .= self::render_field( $field['units'], $fields[ $field['units'] ] );
            }
        }

        if ( empty( $field['template'] ) ) {
            $content .= '</div></div>';
        }

        return apply_filters( 'es_render_field', $content, $id, $field );
    }

    /**
     * Generate label for field using field id.
     *
     * @param $name
     * @return mixed
     */
    public static function generate_label( $name )
    {
        return str_replace( '_', ' ', ucfirst( $name ) );
    }
}
