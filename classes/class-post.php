<?php

/**
 * Class Es_Post.
 */
abstract class Es_Post extends Es_Entity
{
    /**
     * @inheritdoc
     */
    public function get_entity()
    {
        return get_post( $this->getID() );
    }

    /**
     * @inheritdoc
     */
    public function get_field_value( $field, $single = true )
    {
        return get_post_meta( $this->getID(), $this->get_entity_prefix() . $field, $single );
    }

    /**
     * @inheritdoc
     */
    public function save_field_value( $field, $value )
    {
        update_post_meta( $this->getID(), $this->get_entity_prefix() . $field, $value );
    }
}
