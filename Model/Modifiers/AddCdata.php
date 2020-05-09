<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */
namespace Qinvoice\Connect\Model\Modifiers;

/**
 * Trait AddCdata
 */
trait AddCdata
{
    /**
     * @param $string
     * @return string
     */
    public function addCDATA($string)
    {
        return sprintf('<![CDATA[%s]]>', $string);
    }
}
