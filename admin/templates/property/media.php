<?php
/**
 * @var Es_Property $property
 */
?>

<table class='form-table'>
    <tr>
        <td>
            <?php if ( get_theme_support( 'post-thumbnails' ) ): ?>

            <a class='gallery-add button' href='#' data-uploader-title='<?php _e( 'Add image(s) to property', 'es-plugin' ); ?>' data-uploader-button-text='<?php _e( 'Add image(s)', 'es-plugin' ); ?>'>
                <?php _e( 'Add image(s)', 'es-plugin' ); ?>
            </a>

            <ul id='es-media-list'>
                <?php $images = $property->gallery; ?>
                <?php if (!empty($images)): ?>
                    <?php foreach ($images as $key => $value): $image = wp_get_attachment_image_src( $value ); ?>
                        <li>
                            <input type='hidden' name='property[gallery][<?php echo $key; ?>]' value='<?php echo $value; ?>'>
                            <div class="image-preview-wrap">
                                <a class='remove-image' href='#'><i class="fa fa-times-circle" aria-hidden="true"></i></a>
                                <a href="#" class="drag-image"><i class="fa fa-arrows" aria-hidden="true"></i></a>
                                <img class='image-preview' src='<?php echo $image[0]; ?>'>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>

            <?php else: ?>
                <p><?php echo sprintf( wp_kses( __( 'Your theme has no <a href="%s" target="_blank">post thumbnail support</a>.', 'es-plugin' ), array(
                        'a' => array( 'href' => array(), 'target' => array() ) ) ), 'https://codex.wordpress.org/Function_Reference/get_theme_support' ); ?></p>
            <?php endif; ?>
        </td>
    </tr>
</table>
