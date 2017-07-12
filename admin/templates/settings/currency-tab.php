<?php
/**
 * @var Es_Settings_Container $es_settings
 */
?>

<?php if ( $data = $es_settings::get_setting_values( 'currency' ) ) : $name = 'currency'; $label = __( 'Default currency', 'es-plugin' ); ?>
    <?php include( 'fields/dropdown.php' ); ?>
<?php endif; ?>

<?php if ( $data = $es_settings::get_setting_values( 'price_format' ) ) : $name = 'price_format'; $label = __( 'Price format', 'es-plugin' ); ?>
    <?php include( 'fields/dropdown.php' ); ?>
<?php endif; ?>

<?php if ( $data = $es_settings::get_setting_values( 'currency_position' ) ) : $name = 'currency_position'; $label = __( 'Currency sign place', 'es-plugin' ); ?>
    <?php include( 'fields/radio-list.php' ); ?>
<?php endif;
