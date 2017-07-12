<div class="es-settings-field">
    <label>
        <span class="es-settings-label"><i class="fa fa-twitter" aria-hidden="true"></i><?php _e( 'Twitter', 'es-plugin' ); ?>:</span>
        <input type="hidden" name="es_settings[share_twitter]" value="0">
        <input type="checkbox" disabled <?php checked( (bool)$es_settings->share_twitter, true ); ?> name="es_settings[share_twitter]" value="1" class="es-switch-input">
    </label>
</div>

<div class="es-settings-field">
    <label>
        <span class="es-settings-label"><i class="fa fa-facebook" aria-hidden="true"></i><?php _e( 'Facebook', 'es-plugin' ); ?>:</span>
        <input type="hidden" name="es_settings[share_facebook]" value="0">
        <input type="checkbox" disabled <?php checked( (bool)$es_settings->share_facebook, true ); ?> name="es_settings[share_facebook]" value="1" class="es-switch-input">
    </label>
</div>

    <div class="es-settings-field">
    <label><span class="es-settings-label"><i class="fa fa-google-plus" aria-hidden="true"></i><?php _e( 'Google+', 'es-plugin' ); ?>:</span>
        <input type="hidden" name="es_settings[share_google_plus]" value="0">
        <input type="checkbox" disabled <?php checked( (bool)$es_settings->share_google_plus, true ); ?> name="es_settings[share_google_plus]" value="1" class="es-switch-input">
    </label>
    </div>

        <div class="es-settings-field">
    <label>
        <span class="es-settings-label"><i class="fa fa-linkedin" aria-hidden="true"></i><?php _e( 'LinkedIn', 'es-plugin' ); ?>:</span>
        <input type="hidden" name="es_settings[share_linkedin]" value="0">
        <input type="checkbox" disabled <?php checked( (bool)$es_settings->share_linkedin, true ); ?> name="es_settings[share_linkedin]" value="1" class="es-switch-input">
    </label>
        </div>

    <div class="es-settings-field">
    <label>
        <span class="es-settings-label"><i class="fa fa-file-pdf-o" aria-hidden="true"></i><?php _e( 'PDF flyer', 'es-plugin' ); ?>:</span>
        <input type="hidden" name="es_settings[use_pdf]" value="0">
        <input type="checkbox" disabled <?php checked( (bool)$es_settings->use_pdf, true ); ?> name="es_settings[use_pdf]" value="1" class="es-switch-input">
    </label>
    </div>

<p><?php echo sprintf( wp_kses( __( 'Sharing Feature is available in <a href="%s" target="_blank">Estatik Pro</a> version only.', 'es-plugin' ), array(
        'a' => array( 'href' => array(), 'target' => array() ) ) ), esc_url( 'https://estatik.net/product/estatik-professional/' ) ); ?></p>