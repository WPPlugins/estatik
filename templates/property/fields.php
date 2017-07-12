<ul class="es-property-fields">
    <?php if ( $fields = Es_Property_Single_Page::get_single_fields_data() ) : ?>
        <?php foreach ( $fields as $name => $value ) : ?>
            <?php if ( ! empty( $value ) ) : ?>
                <li><strong><?php echo $name; ?>: </strong><?php echo $value; ?></li>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</ul>
