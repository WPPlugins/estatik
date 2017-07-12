<?php

/**
 * @var Es_Settings_Container $es_settings
 */

?>

<div class="es-settings-field"><label><span class="es-settings-label"><?php _e( 'Powered by link', 'es-plugin' ); ?>:</span>
    <input type="hidden" name="es_settings[powered_by_link]" value="0"/>
    <input type="checkbox" <?php checked( (bool)$es_settings->powered_by_link, true ); ?> name="es_settings[powered_by_link]" value="1" class="es-switch-input">
</label></div>

<div class="es-settings-field"><label><span class="es-settings-label"><?php _e( 'Number of listings per page', 'es-plugin' ); ?>:</span>
    <input type="number" value="<?php echo $es_settings->properties_per_page; ?>" min="1" name="es_settings[properties_per_page]">
</label></div>

<div class="es-settings-field"><label><span class="es-settings-label"><?php _e( 'Show price', 'es-plugin' ); ?>:</span>
    <input type="hidden" name="es_settings[show_price]" value="0"/>
    <input type="checkbox" <?php checked( (bool)$es_settings->show_price, true ); ?> name="es_settings[show_price]" value="1" class="es-switch-input">
</label></div>

<?php if ( $data = $es_settings::get_setting_values( 'title_address' ) ) : $name = 'title_address'; $label = __( 'Title / Address', 'es-plugin' ) ?>
    <?php include( 'fields/radio-list.php' ); ?>
<?php endif; ?>

<div class="es-settings-field"><label><span class="es-settings-label"><?php _e( 'Show Address', 'es-plugin' ); ?>:</span>
        <input type="hidden" name="es_settings[show_address]" value="0">
    <input type="checkbox" <?php checked( (bool)$es_settings->show_address, true ); ?> name="es_settings[show_address]" value="1" class="es-switch-input">
</label></div>

<div class="es-settings-field"><label><span class="es-settings-label"><?php _e( 'Hide main title', 'es-plugin' ); ?>:</span>
        <input type="hidden" name="es_settings[hide_main_title]" value="0">
        <input type="checkbox" <?php checked( (bool)$es_settings->hide_main_title, true ); ?> name="es_settings[hide_main_title]" value="1" class="es-switch-input">
    </label></div>

<div class="es-settings-field"><label><span class="es-settings-label"><?php _e( 'Labels', 'es-plugin' ); ?>:</span>
        <input type="hidden" name="es_settings[show_labels]" value="0">
        <input type="checkbox" <?php checked( (bool)$es_settings->show_labels, true ); ?> name="es_settings[show_labels]" value="1" class="es-switch-input">
    </label></div>

<div class="es-settings-field"><label><span class="es-settings-label"><?php _e( 'Date added', 'es-plugin' ); ?>:</span>
        <input type="hidden" name="es_settings[date_added]" value="0"/>
    <input type="checkbox" <?php checked( (bool)$es_settings->date_added, true ); ?> name="es_settings[date_added]" value="1" class="es-switch-input">
</label></div>

<?php if ( $data = $es_settings::get_setting_values( 'date_format' ) ) : $name = 'date_format'; $label = __( 'Date format', 'es-plugin' ); ?>
    <?php include( 'fields/dropdown.php' ); ?>
<?php endif; ?>

<?php if ( $data = $es_settings::get_setting_values( 'theme_style' ) ) : $name = 'theme_style'; $label = __( 'Theme style', 'es-plugin' ); ?>
    <?php include( 'fields/dropdown.php' ); ?>
<?php endif; ?>

<div class="es-settings-field"><label><span class="es-settings-label"><?php _e( 'Google map API key', 'es-plugin' ); ?>:</span>
    <input type="text" value="<?php echo $es_settings->google_api_key; ?>" name="es_settings[google_api_key]">
</label></div>

<input type="hidden" name="es_settings[thumbnail_attachment_id]" value="<?php echo $es_settings->thumbnail_attachment_id; ?>">
