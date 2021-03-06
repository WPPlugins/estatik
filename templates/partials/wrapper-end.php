<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$template = get_option( 'template' );

switch( $template ) {
	case 'twentyeleven' :
		echo '</div>';
		get_sidebar();
		echo '</div>';
		break;
	case 'twentytwelve' :
        echo '</div></div>';
        get_sidebar();
		break;
	case 'twentythirteen' :
		echo '</div></div>';
		break;
	case 'twentyfourteen' :
		echo '</div></div></div>';
		get_sidebar();
		break;
	case 'twentyfifteen' :
		echo '</div></div>';
		break;
	case 'twentysixteen' :
    case 'perth':
		echo '</main></div>';
        get_sidebar();
		break;
    case 'Divi' :
        echo '</div>';
        get_sidebar();
        echo '</div></div></div>';
        break;
    case 'twentyseventeen':
        echo '</div>';
        get_sidebar();
        echo '</div>';
        break;
    case 'twentyten' :
        echo '</div></div>';
        get_sidebar();
        break;
    case 'total':
        echo '</main></div>';
        get_sidebar();
        echo '</div>';
        break;
    case 'giga-store':
        echo '</div>';
        get_sidebar( 'right' );
        echo '</div>';
        break;
    case 'rectangulum':
        echo '</div>';
        get_sidebar( 'right' );
        echo '</div>';
        break;
	default :
		echo '</div></div>';
		break;
}
