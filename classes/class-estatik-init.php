<?php

/**
 * Class Estatik
 */
class Estatik
{
    /**
     * Plugin instance.
     *
     * @var Estatik
     */
    protected static $_instance;
    /**
     * Plugin version.
     *
     * @var string
     */
    protected static $_version = '3.0.1';

    /**
     * Estatik constructor.
     */
    protected function __construct()
    {
        $this->activation();
        $this->deactivation();
        $this->actions();
        $this->init();
        $this->filters();
    }

    /**
     * Return plugin version.
     *
     * @return string
     */
    public static function getVersion()
    {
        return static::$_version;
    }

    /**
     * Return plugin instance.
     *
     * @return Estatik
     */
    protected static function getInstance()
    {
        return is_null( static::$_instance ) ? new Estatik() : static::$_instance;
    }

    /**
     * Initialize plugin.
     *
     * @return void
     */
    public static function run()
    {
        self::class_loader();
        static::$_instance = static::getInstance();
    }

    /**
     * Return registered image size.
     *
     * @return array
     */
    public static function get_image_sizes()
    {
        return array(
            'es-image-size-list' => array( 370, 220, true ),
            'es-image-size-overlay' => array( 370, 260, true ),
            'es-image-size-gallery' => array( 536, 370, true ),
            'es-thumbnail' => array( 150, 150, true ),
            'es-image-size-gallery-thumb' => array( 84, 48, true ),
            'es-image-size-pdf' => array( 720, 250, true ),
            'es-agent-size' => array( 190, 250, true ),
            'es-image-size-2_col' => array( 536, 370, true ),
            'es-image-size-3_col' => array( 536, 370, true ),
            'es-image-size-gallery-widget' => array( 650, 386, true ),
        );
    }

    /**
     * Execute on plugin activate action.
     *
     * @return void
     */
    public function activation()
    {
        $sizes = self::get_image_sizes();

        foreach ( $sizes as $name => $data ) {
            add_image_size( $name, $data[0], $data[1], $data[2] );
        }
    }

    /**
     * Execute on plugin deactivate action.
     *
     * @return void
     */
    public function deactivation() {}

    /**
     * Add plugin actions.
     *
     * @return void
     */
    public function actions()
    {
        add_action( 'wp', array( $this, 'set_global_vars' ) );
        add_action( 'init', array( $this, 'register_post_types' ) );
        add_action( 'init', array( $this, 'register_taxonomies' ) );
        add_action( 'admin_menu', array( $this, 'register_admin_pages' ) );
        add_action( 'es_after_content', array( $this, 'powered' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ) );

        add_action( 'save_post', array( 'Es_Property', 'save' ), 10, 2 );
        add_action( 'init', array( 'Es_Settings_Page', 'save' ) );
    }

    /**
     * Add filters to the wp functionality.
     *
     * @return void
     */
    public function filters() {
        if ( ! is_admin() ) {
            add_filter( 'body_class', array( $this, 'body_class' ) );
        }

        add_filter( 'image_resize_dimensions', array( $this, 'thumbnail_upscale' ), 10, 6 );
    }

    /**
     * Add class to the body.
     *
     * @param $classes
     * @return array
     */
    public function body_class( $classes )
    {
        global $es_settings;

        return array_merge( $classes, array( 'es-theme-' . $es_settings->theme_style ) );
    }

    /**
     * Crop image fix.
     *
     * @param $default
     * @param $orig_w
     * @param $orig_h
     * @param $new_w
     * @param $new_h
     * @param $crop
     * @return array|null
     */
    public function thumbnail_upscale( $default, $orig_w, $orig_h, $new_w, $new_h, $crop ){
        if ( !$crop ) return null; // let the wordpress default function handle this

        $aspect_ratio = $orig_w / $orig_h;
        $size_ratio = max($new_w / $orig_w, $new_h / $orig_h);

        $crop_w = round($new_w / $size_ratio);
        $crop_h = round($new_h / $size_ratio);

        $s_x = floor( ($orig_w - $crop_w) / 2 );
        $s_y = floor( ($orig_h - $crop_h) / 2 );

        return array( 0, 0, (int) $s_x, (int) $s_y, (int) $new_w, (int) $new_h, (int) $crop_w, (int) $crop_h );
    }

    /**
     * Initialize entity classes using specific conditions.
     *
     * @return void
     */
    public function init()
    {
        global $pagenow, $es_settings;

        $es_settings = new Es_Settings_Container();

        // Initialize classes for admin panel.
        if ( is_admin() ) {

            // Initialize admin properties list page.
            if ( $pagenow == 'edit.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] == Es_Property::get_post_type_name() ) {
                Es_Property_List_Page::init();
            }

            // Initialize update pro page.
            if ( $pagenow == 'admin.php' && isset( $_GET['page'] ) && $_GET['page'] == 'es_pro' ) {
                Es_Upgrade_Pro_Page::init();
            }

			// Initialize dashboard page.
			if ( $pagenow == 'admin.php' && isset( $_GET['page'] ) && $_GET['page'] == 'es_dashboard' ) {
				Es_Dashboard_Page::init();
			}

            Es_Migration_Page::init();
            Es_Property_Metabox::init();
            Es_Data_Manager_Page::init();

        } else {
            // Initialize template loader class.
            Es_Template_Loader::init();
            Es_Archive_Sorting::init();
            Es_Property_Archive_Page::init();
            Es_Property_Single_Page::init();
        }

        Es_Shortcodes::init();
    }

    /**
     * Install action. Triggering on plugin activation.
     *
     * @return void
     */
    public static function install()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'address_components';
        $charset_collate = '';

        if ( ! empty ( $wpdb->charset ) )
            $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";

        if ( ! empty ( $wpdb->collate ) )
            $charset_collate .= " COLLATE $wpdb->collate";

        $sql = 'CREATE TABLE IF NOT EXISTS ' . $table_name . ' (
            id int(11) NOT NULL AUTO_INCREMENT,
            long_name VARCHAR(255),
            short_name VARCHAR(255),
            `type` VARCHAR(255) NOT NULL,
            PRIMARY KEY (id)
        ); ' . $charset_collate . ';';

        register_taxonomy( 'es_labels', Es_Property::get_post_type_name() );

        $labels = es_get_standard_label_names();

        if ( taxonomy_exists( 'es_labels' ) && ! empty( $labels ) ) {
            foreach ( $labels as $color => $label ) {
                if ( ! term_exists( $label ) ) {
                    $term = wp_insert_term( $label, 'es_labels' );
                    if ( ! empty( $term['term_id'] ) ) {
                        update_term_meta( $term['term_id'], 'es_color', $color );
                    }
                }
            }
        }

        update_option( 'es_thumbnail_attachment_id', '' );

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

        do_action( 'es_plugin_after_install' );
    }

    /**
     * Load plugin files.
     *
     * @return void
     */
    public static function class_loader()
    {
        $files = apply_filters( 'es_class_loader', array(
            ES_PLUGIN_PATH . '/classes/class-object.php',
            ES_PLUGIN_PATH . '/admin/classes/class-search-location.php',
            ES_PLUGIN_PATH . '/admin/classes/class-property-metabox.php',
            ES_PLUGIN_PATH . '/classes/class-post-duplicate.php',
            ES_PLUGIN_PATH . '/classes/class-archive-sorting.php',
            ES_PLUGIN_PATH . '/classes/pages/class-property-single-page.php',
            ES_PLUGIN_PATH . '/classes/pages/class-property-archive-page.php',
            ES_PLUGIN_PATH . '/classes/shortcodes/class-shortcode.php',
            ES_PLUGIN_PATH . '/classes/shortcodes/class-my-listing-shortcode.php',
            ES_PLUGIN_PATH . '/classes/shortcodes/class-featured-props-shortcode.php',
            ES_PLUGIN_PATH . '/classes/shortcodes/class-latest-props-shortcode.php',
            ES_PLUGIN_PATH . '/classes/shortcodes/class-cheapest-props-shortcode.php',
            ES_PLUGIN_PATH . '/classes/shortcodes/class-expensive-props-shortcode.php',
            ES_PLUGIN_PATH . '/classes/shortcodes/class-category-shortcode.php',
            ES_PLUGIN_PATH . '/classes/shortcodes/class-single-shortcode.php',
            ES_PLUGIN_PATH . '/classes/class-shortcodes.php',
            ES_PLUGIN_PATH . '/admin/interfaces/es-messenger-interface.php',
            ES_PLUGIN_PATH . '/admin/classes/widgets/class-widget.php',
            ES_PLUGIN_PATH . '/admin/classes/widgets/class-search-widget.php',
            ES_PLUGIN_PATH . '/admin/classes/class-data-manager-item.php',
            ES_PLUGIN_PATH . '/admin/classes/class-data-manager-term-item.php',
            ES_PLUGIN_PATH . '/admin/classes/class-estatik-upgrade.php',
            ES_PLUGIN_PATH . '/admin/classes/pages/class-dashboard-page.php',
            ES_PLUGIN_PATH . '/admin/classes/pages/class-settings-page.php',
            ES_PLUGIN_PATH . '/admin/classes/pages/class-data-manager-page.php',
            ES_PLUGIN_PATH . '/admin/classes/migration/class-property-migration.php',
            ES_PLUGIN_PATH . '/admin/classes/pages/class-migration-page.php',
            ES_PLUGIN_PATH . '/admin/classes/pages/class-property-list-page.php',
            ES_PLUGIN_PATH . '/admin/classes/pages/class-upgrade-pro-page.php',
            ES_PLUGIN_PATH . '/admin/classes/class-messenger.php',
            ES_PLUGIN_PATH . '/classes/class-settings-container.php',
            ES_PLUGIN_PATH . '/classes/class-html-helper.php',
            ES_PLUGIN_PATH . '/classes/class-entity.php',
            ES_PLUGIN_PATH . '/classes/class-post.php',
            ES_PLUGIN_PATH . '/classes/class-property.php',
            ES_PLUGIN_PATH . '/classes/class-address-components.php',
            ES_PLUGIN_PATH . '/classes/class-template-loader.php',
            ES_PLUGIN_PATH . '/classes/class-session-storage.php',
            ES_PLUGIN_PATH . '/functions.php',
        ) );

        foreach ( $files as $file ) {
            if ( file_exists( $file ) ) {
                include $file;
            }
        }
    }

    /**
     * Enqueue scripts for frontend part.
     *
     * @return void
     */
    public function enqueue_scripts()
    {
        $custom = 'assets/js/custom/';

        global $es_settings;

        // Base front script.
        wp_register_script(
            'es-front-script',
            ES_PLUGIN_URL . $custom . 'front.js',
            array ( 'jquery', 'es-dropdown-script' )
        );

        // Custom dropDown script.
	    wp_register_script(
	        'es-dropdown-script',
            ES_PLUGIN_URL . $custom . 'es-dropdown.js',
            array ( 'jquery' )
        );

        if ( $es_settings->google_api_key ) {
            // Google map wrapper.
            wp_register_script(
                'es-admin-map-script',
                ES_PLUGIN_URL . $custom . 'map.js',
                array( 'es-admin-googlemap-api' )
            );

            // Google map API.
            wp_register_script(
                'es-admin-googlemap-api',
                'https://maps.googleapis.com/maps/api/js?key=' . $es_settings->google_api_key . '&libraries=places',
                array(),
                false,
                true
            );
        }

        // Script for dependent location dropDowns in search widget.
        wp_register_script(
            'es-front-locations-script',
            ES_PLUGIN_URL . $custom . 'locations.js',
            array ( 'jquery', 'es-dropdown-script' )
        );

        wp_enqueue_script( 'es-front-locations-script' );

        // Share plugin JS variables for locations script.
        wp_localize_script( 'es-front-locations-script', 'Estatik', static::register_js_variables() );
    }

    /**
     * Enqueue styles for frontend part.
     *
     * @return void
     */
    public function enqueue_styles()
    {
        $vendor = 'assets/css/vendor/';
        $custom = 'assets/css/custom/';

        // Register base styles for the plugin.
        wp_register_style( 'es-front-style', ES_PLUGIN_URL . $custom . 'front.css' );
        wp_enqueue_style( 'es-front-style' );

        // Register font awesome.
        wp_register_style( 'es-font-awesome', ES_PLUGIN_URL . $vendor . 'font-awesome.min.css' );
        wp_enqueue_style( 'es-font-awesome' );

        wp_register_style('jquery-ui', 'http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
        wp_enqueue_style( 'jquery-ui' );

        wp_register_style(
            'es-google-open-sans-form',
            'https://fonts.googleapis.com/css?family=Open+Sans:300,400" rel="stylesheet'
        );

        wp_enqueue_style( 'es-google-open-sans-form' );
    }

    /**
     * Enqueue admin scripts.
     *
     * @return void
     */
    public function admin_enqueue_scripts()
    {
        global $es_settings;

        $custom = 'admin/assets/js/custom/';
        $vendor = 'admin/assets/js/vendor/';
        $custom_main = 'assets/js/custom/';

        $language = str_replace('_', '-', get_locale());

        wp_register_script(
            'es-admin-scroll-script', ES_PLUGIN_URL . $vendor . 'jquery.mCustomScrollbar.concat.min.js',
            array( 'jquery' ),
            false,
            $in_footer = false
        );

        wp_register_script(
            'es-data-manager-script', ES_PLUGIN_URL . $custom . 'jquery-data-manager.js',
            array ( 'jquery', 'es-popup-script' )
        );

        wp_register_script( 'es-admin-script', ES_PLUGIN_URL . $custom . 'admin.js', array (
            'jquery', 'es-popup-script', 'es-data-manager-script', 'es-slick-admin-script', 'jquery-ui-tabs',
            'es-admin-scroll-script', 'es-admin-map-script',
        ) );

        wp_enqueue_script( 'es-admin-script' );

        wp_register_script( 'es-select2-script', ES_PLUGIN_URL . $vendor . 'select2.min.js', array ( 'jquery' ) );

        wp_register_script( 'es-checkbox-script', ES_PLUGIN_URL . $custom . 'es-checkboxes.js', array ( 'jquery' ) );
        wp_enqueue_script( 'es-checkbox-script' );

        wp_register_script( 'es-tooltipster-script', ES_PLUGIN_URL . $vendor . 'tooltipster.bundle.min.js', array ( 'jquery' ) );
        wp_enqueue_script( 'es-tooltipster-script' );

        wp_register_script( 'es-popup-script', ES_PLUGIN_URL . $custom . 'es-popup.js', array ( 'jquery' ) );

        wp_register_script( 'es-progress-script', ES_PLUGIN_URL . $vendor . 'jquery.progress.js', array ( 'jquery' ) );

        // Register slider for dashboard page.
        wp_register_script( 'es-slick-admin-script', ES_PLUGIN_URL . $vendor . 'slick.min.js', array ( 'jquery' ) );

        wp_localize_script( 'es-admin-script', 'Estatik', static::register_js_variables() );

        wp_register_script(
            'es-admin-map-script', ES_PLUGIN_URL . $custom_main . 'map.js',
            array( 'es-admin-googlemap-api' ),
            false
        );

        wp_register_script(
            'es-admin-googlemap-api',
            'https://maps.googleapis.com/maps/api/js?key=' . $es_settings->google_api_key . '&libraries=places&language='.$language,
            array(),
            false
        );
    }

    /**
     * Enqueue admin styles.
     *
     * @return void
     */
    public function admin_enqueue_styles()
    {
        $vendor = 'admin/assets/css/vendor/';
        $vendor_main = 'assets/css/vendor/';
        $custom = 'admin/assets/css/custom/';

        wp_register_style( 'es-tooltipster-style', ES_PLUGIN_URL . $vendor . 'tooltipster.bundle.min.css' );
        wp_enqueue_style( 'es-tooltipster-style' );

        wp_register_style( 'es-tooltipster-theme-style', ES_PLUGIN_URL . $vendor . 'tooltipster-sideTip-borderless.min.css' );
        wp_enqueue_style( 'es-tooltipster-theme-style' );

        wp_register_style( 'es-select2-style', ES_PLUGIN_URL . $vendor . 'select2.min.css' );
        wp_enqueue_style( 'es-select2-style' );

        wp_register_style( 'es-font-awesome', ES_PLUGIN_URL . $vendor_main . 'font-awesome.min.css' );
        wp_enqueue_style( 'es-font-awesome' );

        wp_register_style( 'jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css' );

        wp_register_style( 'es-admin-style', ES_PLUGIN_URL . $custom . 'admin.css' );
        wp_enqueue_style( 'es-admin-style' );

        wp_register_style( 'es-checkboxes-style', ES_PLUGIN_URL . $custom . 'es-checkboxes.css' );
        wp_enqueue_style( 'es-checkboxes-style' );

        wp_register_style( 'es-popup-style', ES_PLUGIN_URL . $custom . 'es-popup.css' );
        wp_enqueue_style( 'es-popup-style' );

        wp_register_style(
            'es-google-open-sans-form',
            'https://fonts.googleapis.com/css?family=Open+Sans:300,400" rel="stylesheet'
        );

        wp_enqueue_style( 'es-google-open-sans-form' );
    }

    /**
     * Register global javascript
     *
     * @return array
     */
    public static function register_js_variables()
    {
        global $es_settings;

        return apply_filters( 'es_global_js_variables', array(
            'tr' => array(
                'remove_image' => __( 'Remove image', 'es-plugin' ),
                'remove' => __( 'Remove', 'es-plugin' ),
                'yes' => __( 'Yes', 'es-plugin' ),
                'no' => __( 'No', 'es-plugin' ),
                'select_location' => __( 'Select location', 'es-plugin' ),
                'sorting' => __( 'Sort by', 'es-plugin' ),
                'confirmDeleting' => __( 'Are you sure you want to delete this item?', 'es-plugin' )
            ),
            'settings' => array(
                'pluginUrl' => ES_PLUGIN_URL,
                'layout' => $es_settings->listing_layout,
                'responsive' => array(
                    'es-layout-list' => array( 'min' => 660, 'max' =>  999999 ),
                    'es-layout-2_col' => array( 'max' => 640, 'min' =>  0 ),
                    'es-layout-3_col' => array( 'min' => 620, 'max' => 660 ),
                )
            ),
            'widgets' => array(
                'search' => array(
                    'initPriority' => array(
                        Es_Search_Location::LOCATION_COUNTRY_TYPE => array(
                            Es_Search_Location::LOCATION_STATE_TYPE
                        ),

                        Es_Search_Location::LOCATION_STATE_TYPE   => array(
                            Es_Search_Location::LOCATION_CITY_TYPE,
                        ),

                        Es_Search_Location::LOCATION_CITY_TYPE    => array(
                            Es_Search_Location::LOCATION_STREET_TYPE,
                            Es_Search_Location::LOCATION_NEIGHBORHOOD_TYPE,
                        ),

                        Es_Search_Location::LOCATION_STREET_TYPE  => array(
                            Es_Search_Location::LOCATION_NEIGHBORHOOD_TYPE,
                        ),

                        Es_Search_Location::LOCATION_NEIGHBORHOOD_TYPE => array(),
                    ),
                )
            ),
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
        ) );
    }

    /**
     * Register admin pages.
     */
    public function register_admin_pages()
    {
        $imagesPath = 'admin/assets/images/';

        add_menu_page(
            __( 'Estatik', 'es-plugin' ),
            __( 'Estatik', 'es-plugin' ),
            'manage_options',
            'es_dashboard',
            array( 'Es_Dashboard_Page', 'render' ),
            ES_PLUGIN_URL . $imagesPath .'es_menu_icon.png',
            '20.7'
        );

        add_submenu_page(
            'es_dashboard',
            __( 'Dashboard', 'es-plugin' ),
            __( 'Dashboard', 'es-plugin' ),
            'manage_options',
            'es_dashboard',
            array( 'Es_Dashboard_Page', 'render' )
        );

        add_submenu_page(
            'es_dashboard',
            __( 'My listings', 'es-plugin' ),
            __( 'My listings', 'es-plugin' ),
            'manage_options',
            es_admin_property_list_uri()
        );

        add_submenu_page(
            'es_dashboard',
            __( 'Add new property', 'es-plugin' ),
            __( 'Add new property', 'es-plugin' ),
            'manage_options',
            es_admin_property_add_uri()
        );

        add_submenu_page(
            'es_dashboard',
            __( 'Data Manager', 'es-plugin' ),
            __( 'Data Manager', 'es-plugin' ),
            'manage_options',
            'es_data_manager',
            array( 'Es_Data_Manager_Page', 'render' )
        );

        add_submenu_page(
            'es_dashboard',
            __( 'Settings', 'es-plugin' ),
            __( 'Settings', 'es-plugin' ),
            'manage_options',
            'es_settings',
            array( 'Es_Settings_Page', 'render' )
        );

        add_submenu_page(
            'es_dashboard',
            __( 'Estatik Pro', 'es-plugin' ),
            __( 'Estatik Pro', 'es-plugin' ),
            'manage_options',
            'es_pro',
            array( 'Es_Upgrade_Pro_Page', 'render' )
        );

//        if ( ! es_migration_already_executed() && es_need_migrate() ) {
            add_submenu_page(
                'es_dashboard',
                __( 'Migration', 'es-plugin' ),
                __( 'Migration', 'es-plugin' ),
                'manage_options',
                'es_migration',
                array( 'Es_Migration_Page', 'render' )
            );
//        }
    }

	/**
	 * Display powered by phrase.
     *
     * @return void
	 */
	public function powered() {
		global $es_settings;

		if ( $es_settings->powered_by_link ) {
            $template = apply_filters( 'es_powered_template', ES_TEMPLATES . 'powered.php' );
			include( $template );
		}
	}

    /**
     * Initialize plugin global vars.
     *
     * @return void
     */
    public function set_global_vars()
    {
        global $post, $es_settings, $es_property;

        // Set global plugin settings array.
        $es_settings = new Es_Settings_Container();

        // Set global property object.
        if ( ! empty( $post->post_type ) && Es_Property::get_post_type_name() == $post->post_type && is_single() ) {
            $es_property = es_get_property( $post->ID );
        }
    }

    /**
     * Register plugin post types.
     *
     * @return void
     */
    public function register_post_types()
    {
        register_post_type( Es_Property::get_post_type_name(), array(
            'label' => __( 'Property', 'es-plugin' ),
            'labels' => array(
                'name' => __( 'My listings', 'es-plugin' ),
            ),
            'public' => true,
            'show_in_menu' => false,
            'has_archive' => true,
            'supports' => array( 'title', 'editor', 'author', 'excerpt' ),
            'rewrite' => array(
                'slug' => 'property',
            ),
        ) );

        flush_rewrite_rules();
    }

    /**
     * Register plugin taxonomies.
     *
     * @return void
     */
    public function register_taxonomies()
    {
        register_taxonomy( 'es_category', Es_Property::get_post_type_name(), apply_filters( 'es_category_taxonomy_args', array(
            'labels' => array(
                'name' => __( 'Categories', 'es-plugin' ),
                'singular_name' => __( 'Category', 'es-plugin' ),
            ),
        ) ) );

        register_taxonomy( 'es_status', Es_Property::get_post_type_name(), apply_filters( 'es_status_taxonomy_args', array(
            'labels' => array(
                'name' => __( 'Status', 'es-plugin' ),
                'singular_name' => __( 'Status', 'es-plugin' ),
            ),
        ) ) );

        register_taxonomy( 'es_type', Es_Property::get_post_type_name(), apply_filters( 'es_type_taxonomy_args', array(
            'labels' => array(
                'name' => __( 'Types', 'es-plugin' ),
                'singular_name' => __( 'Type', 'es-plugin' ),
            ),
        ) ) );

        register_taxonomy( 'es_feature', Es_Property::get_post_type_name(), apply_filters( 'es_feature_taxonomy_args', array(
            'labels' => array(
                'name' => __( 'Features', 'es-plugin' ),
                'singular_name' => __( 'Feature', 'es-plugin' ),
            ),
        ) ) );

        register_taxonomy( 'es_rent_period', Es_Property::get_post_type_name(), apply_filters( 'es_rent_period_args', array(
            'labels' => array(
                'name' => __( 'Rent period', 'es-plugin' ),
                'singular_name' => __( 'Rent period', 'es-plugin' ),
            ),
        ) ) );

        register_taxonomy( 'es_amenities', Es_Property::get_post_type_name(), apply_filters( 'es_amenities_taxonomy_args', array(
            'labels' => array(
                'name' => __( 'Amenities', 'es-plugin' ),
                'singular_name' => __( 'Amenities', 'es-plugin' ),
            ),
        ) ) );

        register_taxonomy( 'es_labels', Es_Property::get_post_type_name(), apply_filters( 'es_labels_args', array(
            'labels' => array(
                'name' => __( 'Labels', 'es-plugin' ),
                'singular_name' => __( 'Label', 'es-plugin' ),
            ),
            'show_ui' => false,
        ) ) );
    }
}
