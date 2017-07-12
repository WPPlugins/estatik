<?php do_action( 'es_before_properties_details_tab' ); ?>

<?php ( new Es_Data_Manager_Term_Item( 'es_status' ) )->render(); ?>
<?php ( new Es_Data_Manager_Term_Item( 'es_category' ) )->render(); ?>
<?php ( new Es_Data_Manager_Term_Item( 'es_type' ) )->render(); ?>
<?php ( new Es_Data_Manager_Term_Item( 'es_rent_period' ) )->render(); ?>

<?php do_action( 'es_after_properties_details_tab' ); ?>
