<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Api;

use Qinvoice\Connect\Model\Document;

/**
 * Interface ModifierInterface
 * @package Qinvoice\Connect\Api
 */
interface ModifierInterface
{
    /**
     * @param Document $document
     * @return Document
     */
    public function modify(Document $document);
}
