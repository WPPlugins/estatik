<?php

/**
 * Class Es_Dashboard_Page
 */
class Es_Dashboard_Page extends Es_Object
{
    /**
     * Add actions for dashboard page.
     */
    public function actions()
    {
        add_action( 'admin_enqueue_scripts', array( $this , 'enqueue_styles' ) );
    }

    /**
     * Enqueue styles for dashboard page.
     *
     * @return void
     */
    public function enqueue_styles()
    {
        $vendor = 'admin/assets/css/vendor/';

        wp_register_style( 'es-scroll-style', ES_PLUGIN_URL . $vendor . 'jquery.mCustomScrollbar.css' );
        wp_enqueue_style( 'es-scroll-style' );

        wp_register_style( 'es-admin-slick-style', ES_PLUGIN_URL . $vendor . 'slick.css' );
        wp_register_style( 'es-admin-slick-theme-style', ES_PLUGIN_URL . $vendor . 'slick-theme.css' );

        wp_enqueue_style( 'es-admin-slick-style' );
        wp_enqueue_style( 'es-admin-slick-theme-style' );
    }

    /**
     * Render dashboard page.
     *
     * @return void
     */
    public static function render()
    {
        $template = apply_filters( 'es_dashboard_page_template_path', ES_ADMIN_TEMPLATES . '/dashboard/dashboard.php' );

        if ( file_exists( $template ) ) {
            include_once( $template );
        }
    }

    /**
     * Return shortcodes list for dashboard page.
     *
     * @return array
     */
    public static function get_shortcodes_list()
    {
        return apply_filters( 'es_get_shortcodes_list', array(
            '[es_my_listing layout="list | 3_col | 2_col"]',
            '[es_my_listing sort="recent | highest_price | lowest_price | most_popular"] ',
            '[es_my_listing  prop_id="1,2,5,6...n"]',
            '[es_my_listing category="category name"]',
            '[es_my_listing status="status name"]',
            '[es_my_listing type="type name"]',
            '[es_my_listing rent_period="rent period 1"]',
            '[es_property_map show="all"] (PRO)',
            '[es_property_map type="your type"] (PRO)',
            '[es_property_map category="your category"] (PRO)',
            '[es_property_map status="your status"] (PRO)',
            '[es_property_map rent_period="your period"] (PRO)',
            '[es_property_map limit=20] (PRO)',
            '[es_property_map ids="1,2,3,4,5"] (PRO)',
            '[es_property_map address="your address string"] (PRO)',
            '[es_property_slideshow] (PRO)',
            '[es_city city="city name"] (PRO)',
            '[es_state state="state name"] (PRO)',
            '[es_country country="country name"] (PRO)',
            '[es_labels label="label_name"] (PRO)',
            '[es_features feature="a,b,c,d"] (PRO)',
            '[es_featured_props] ',
            '[es_latest_props] ',
            '[es_cheapest_props]',
            '[es_agents] (PRO)',
            '[es_listing_agent name="agent username"] (PRO)',
            '[es_subscription_table] (PRO)',
            '[es_register] (PRO)',
        ) );
    }

    /**
     * Return changelog array for dashboard page.
     *
     * @return mixed|void
     */
    public static function get_changelog_list()
    {
        return apply_filters( 'es_get_changelog_list', array(
            __( '3.0.0 (March 23, 2017)', 'es-plugin' ) => '<ul>
                <li>Property became WP_Post entity</li>
                <li>Images upload via WP Media only</li>
                <li>Numerous new shortcodes added</li>
                <li>Search with drag & drop feature improved</li>
                <li>Archive page created, can be customized using wp hooks</li>
                <li>Pagination improved</li>
                <li>Google Map improved, option to add address with lat/lng fields added (PRO only)</li>
                <li>Labels became editable (PRO only)</li>
                <li>CSV Import improved, images import via link added (PRO only)</li>
                <li>Subscriptions: recurring payments added (PRO only)</li>
                <li>Frontend management replaced by limited admin area (PRO only)</li>
                <li>Admin logo upload added (PRO only)</li>
                <li>Other fixes..</li>
            </ul>',

            __( '2.4.0 (September 26, 2016)', 'es-plugin' ) => '<ul>
                <li>Issue with Upgrade to Pro option fixed</li>
            </ul>',

            __( '2.3.1 (August 21, 2016)', 'es-plugin' ) => '<ul>
                <li>Arbitrary file upload vulnerability fixed</li>
            </ul>',

            __( '2.3.0 (August 15, 2016)', 'es-plugin' ) => '<ul>
                <li>File upload vulnerability fixed</li>
                <li>Review and removal of session_start() and ob_start()</li>
                <li>MAP API issue fixed</li>
            </ul>',

            __( '2.2.3 (March 30, 2016)', 'es-plugin' ) => '<ul>
                <li>Permalinks issue fixed</li>
                <li>Price issue > 1 mln fixed</li>
                <li>beds and baths translation fixed</li>
                <li>Search bug fixed</li>
                <li>Subscription plans added (PRO)</li>
                <li>PDF bug with currency change fixed (PRO)</li>
                <li>New shortcode to display listings of a specific agent added (PRO)</li>
                <li>Automatic/manual approval of listings added (PRO)</li>
            </ul>Please read detailed description of release <a href="http://estatik.net/estatik-simple-pro-ver-2-2-0-released/" target="_blank">here</a>.',

            __( '2.2.2 (November 21, 2015)', 'es-plugin' ) => '<ul>
                <li>View first menu ON/OFF option added</li>
                <li>Bug with currency format 99 999 fixed</li>
                <li>Popup icon in admin map returned</li>
                <li>Search results on 2,3, etc. pages fixed</li>
                <li>Some grammatical errors corrected</li>
                <li>Half baths added to front-end management page (PRO)</li>
                <li>Correct redirection for agents after logged into front-end management page fixed (PRO)</li>
            </ul>',

            __( '2.2.1 (October 22, 2015)', 'es-plugin' ) => '<ul>
                <li>Search by category fixed</li>
            </ul>',

            __( '2.2.0 (October 22, 2015)', 'es-plugin' ) => '<ul>
                <li>Map issues fixed in frontend, admin and lightbox</li>
                <li>Half bathroom option added</li>
                <li>Dark/light style mode added</li>
                <li>Search widget updated with separate Country, State and City drop-down fields</li>
                <li>New shortcode for city added [es_city city="city name"]</li>
                <li>Dimension display of Area and Lot size fields bug fixed</li>
                <li>Slashes // in new fields removed</li>
                <li>Agent phone field bug fixed</li>
                <li>Deprecated method for wp_widget updated</li>
            </ul>Please read full description of new release <a href="http://estatik.net/estatik-simple-pro-ver-2-2-0-released/" target="_blank">here</a>.',

            __( '2.1.0 (July 7, 2015)', 'es-plugin' ) => '<ul>
                <li>New shortcodes for categories added: [es_category category="for sale"],[es_category type="house"],[es_category status="open"].</li>
                <li>New shortcode for search results page added.</li>
                <li>French translation added.</li>
                <li>Google Map API option added.</li>
                <li>Search widget results page bug fixed.</li>
                <li>Description box bug with text fixed.</li>
                <li>Display of area/lot size dimensions on front-end fixed.</li>
                <li>PRO: PDF translation issue fixed.</li>
                <li>PRO: PDF display in IE and Chrome error fixed.</li>
                <li>PRO: Google Map API option added.</li>
                <li>PRO: Copying images after CSV import fixed.</li>
                </ul>Please read full description of new release <a href="http://estatik.net/estatik-2-1-release-no-more-coding-from-now/" target="_blank">here</a>.',

            __( '2.0.1', 'es-plugin' ) => '<ul>
                <li>Italian translation added</li>
                <li>Spanish translation added</li>
                <li>Arabic translation added</li>
                </ul>Please read full description of new release <a href="http://estatik.net/estatik-2-0-terrific-released-map-view-lots-of-major-fixes-done/" target="_blank">here</a>.',

            __( '2.0 (May 16, 2015)', 'es-plugin' ) => '<ul>
                <li>Safari responsive layout issue fixed.</li>
                <li>Google Map icons issue fixed.</li>
                <li>PRO - HTML editor added.</li>
                <li>PRO - Lightbox on single property page added.</li>
                <li>PRO - Tabs issue fixed.</li>
                <li>PRO - Map view shortcodes added.</li>
                <li>PRO - Map view widget added.</li>
                <li>PRO - Option to use different layouts added.</li>
                </ul>',

            __( '1.1.1', 'es-plugin' ) => '<ul>
                <li>Issue with Google Map API fixed</li>
                <li>Translation into Russian added</li>
                </ul>',

            __( '1.0.1', 'es-plugin' ) => '<ul>
                <li>jQuery conflicts fixed.</li>
                <li>Language files added.</li>
                </ul>',

            __( '1.0.0 (March 24, 2015)', 'es-plugin' ) => '<ul>
                <li>Data manager is added.</li>
                <li>Property listings shortcodes are added.</li>
                <li>Search widget is added.</li></ul>
            ',
        ) );
    }

    /**
     * Return themes list for dashboard page.
     *
     * @return mixed|void
     */
    public static function get_themes_list()
    {
        return apply_filters( 'es_get_themes_list', array() );
    }
}
