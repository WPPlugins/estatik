<?php

/**
 * Class Es_Settings_Page
 */
class Es_Settings_Page
{
    /**
     * Render settings page content.
     *
     * @return void
     */
    public static function render()
    {
        $template = apply_filters( 'es_settings_template_path', ES_ADMIN_TEMPLATES . 'settings/settings.php' );
        include_once( $template );
    }

    /**
     * Return tabs of the settings page.
     *
     * @return array
     */
    public static function get_tabs()
    {
        return apply_filters( 'es_settings_get_tabs', array(
            'general' => array(
                'label' => __( 'General', 'es-plugin' ),
                'template' => ES_ADMIN_TEMPLATES . 'settings/general-tab.php'
            ),
            'layouts' => array(
                'label' => __( 'Layouts', 'es-plugin' ),
                'template' => ES_ADMIN_TEMPLATES . 'settings/layouts-tab.php'
            ),
            'currency' => array(
                'label' => __( 'Currency', 'es-plugin' ),
                'template' => ES_ADMIN_TEMPLATES . 'settings/currency-tab.php'
            ),
            'sharing' => array(
                'label' => __( 'Sharing', 'es-plugin' ),
                'template' => ES_ADMIN_TEMPLATES . 'settings/sharing-tab.php'
            ),
        ) );
    }

    /**
     * Save settings action.
     *
     * @return void
     */
    public static function save()
    {
        if ( isset( $_POST['es_save_settings'] ) && wp_verify_nonce( $_POST['es_save_settings'], 'es_save_settings' ) ) {

            /** @var Es_Settings_Container $es_settings */
            global $es_settings;

            // Filtering and preparing data for save.
            $data = apply_filters( 'es_before_save_settings_data', $_POST['es_settings'] );

            // Before save action.
            do_action( 'es_before_settings_save', $data );

            $es_settings->save( $data );

            // After save action.
            do_action( 'es_after_settings_save', $data );
        }
    }
}
