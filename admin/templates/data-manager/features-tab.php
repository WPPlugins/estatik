<?php do_action( 'es_before_features_tab' ); ?>

<?php ( new Es_Data_Manager_Term_Item( 'es_feature' ) )->render(); ?>
<?php ( new Es_Data_Manager_Term_Item( 'es_amenities' ) )->render(); ?>

<?php do_action( 'es_after_features_tab' ); ?>
