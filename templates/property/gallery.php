<?php global $es_property, $es_settings; ?>

<div class="es-gallery">
    <?php if ( $gallery = $es_property->gallery ) : ?>
        <div class="es-gallery-inner">
            <div class="es-gallery-image">
                <?php foreach ( $gallery as $value ) : ?>
                    <div style="background: "><?php echo wp_get_attachment_image( $value, 'es-image-size-gallery' ); ?></div>
                <?php endforeach; ?>
            </div>

            <div class="es-gallery-image-pager-wrap">
                <a href="#" class="slick-arrow slick-prev">1</a>
                <div class="es-gallery-image-pager">
                    <?php foreach ( $gallery as $value ) : ?>
                        <div><?php echo wp_get_attachment_image( $value, 'es-image-size-gallery-thumb' ); ?></div>
                    <?php endforeach; ?>
                </div>
                <a href="#" class="slick-arrow slick-next">2</a>
            </div>
        </div>
    <?php elseif ( $image = es_get_default_thumbnail( 'es-image-size-gallery' ) ): ?>
        <?php echo $image; ?>
    <?php endif; ?>
</div>
