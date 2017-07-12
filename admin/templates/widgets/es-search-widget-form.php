<?php

/**
 * @file Estatik search widget form file.
 *
 * @var array $instance
 * @var string $title
 * @var string $layout
 * @var string $display_type
 * @var array $pages_active
 * @var $this Es_Search_Widget
 */

$title         = ! empty( $instance['title'] )        ? $instance['title']          : null;
$layout        = ! empty( $instance['layout'] )       ? $instance['layout']         : null;
$fields_active = ! empty( $instance['fields'][0] )    ? $instance['fields']         : null;

?>

<p>
    <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Search Title', 'es-plugin' ); ?>:</label>
    <input type="text" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo $title; ?>"/>
</p>

<?php if ( $layouts = $this::get_layouts() ) : ?>
    <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'layout' ) ); ?>"><?php _e( 'Layouts', 'es-plugin' ); ?>:</label>
        <select name="<?php echo esc_attr( $this->get_field_name( 'layout' ) ); ?>" class="widefat">
            <?php foreach ( $layouts as $field ) : ?>
                <option <?php selected( $field, $layout ); ?> value="<?php echo $field; ?>"><?php echo Es_Html_Helper::generate_label( $field ); ?></option>
            <?php endforeach; ?>
        </select>
    </p>
<?php endif; ?>

<?php if ( $fields = $this::get_widget_fields() ) : ?>
    <p>
        <label for=""><?php _e( 'Add search field' ); ?>:</label>
        <select name="" id="" class="widefat js-es-field-select">
            <option value=""><?php _e( '-- Select field --', 'es-plugin' ); ?></option>
            <?php foreach ( $fields as $name ) : ?>
                <option value="<?php echo $name; ?>"><?php echo Es_Html_Helper::generate_label( $name ); ?></option>
            <?php endforeach; ?>
        </select>
    </p>
<?php endif; ?>


<ul class="es-search-widget-fields">
    <?php if ( ! empty( $fields_active ) ) : ?>
        <?php foreach( $fields_active as $field ) : if ( empty( $field ) ) continue; ?>
            <li data-field-name="<?php echo $field; ?>"><?php echo Es_Html_Helper::generate_label($field); ?>
                <a href="#" class="es-remove-field">×</a>
                <input type="hidden" name="<?php echo esc_attr( $this->get_field_name( 'fields[]' ) ); ?>" value="<?php echo $field; ?>">
            </li>
        <?php endforeach; ?>
    <?php endif; ?>
</ul>
<input type="hidden" name="<?php echo esc_attr( $this->get_field_name( 'fields[]' ) ); ?>" class="es-fields-name">

<?php do_action( 'es_widget_' . $this->id_base . '_page_access_block', $instance );
