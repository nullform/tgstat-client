<?php

namespace Nullform\TGStatClient\Models;

/**
 * The base class for all models used in the TGStat API.
 *
 * @package Nullform\TGStatClient
 */
abstract class AbstractModel
{
    /**
     * @param \stdClass $obj Data to fill in the object properties.
     * @see AbstractModel::fill()
     */
    public function __construct(?\stdClass $obj = null)
    {
        if (!empty($obj)) {
            $this->fill($obj);
        }
    }

    /**
     * Filling the object properties with the properties from stdClass instance.
     *
     * @param \stdClass $obj
     */
    public function fill(?\stdClass $obj): void
    {
        if (is_object($obj)) {
            $object_vars = get_object_vars($obj);

            if (!empty($object_vars)) {
                foreach ($object_vars as $var => $value) {
                    if (property_exists($this, $var)) {
                        $this->$var = $value;
                    }
                }
            }
        }
    }
}