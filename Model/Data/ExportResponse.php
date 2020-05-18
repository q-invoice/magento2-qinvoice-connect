<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */
namespace Qinvoice\Connect\Model\Data;

class ExportResponse extends BaseResponse implements \Qinvoice\Connect\Api\Data\ExportResponseInterface
{
    private $data;

    /**
     * @inheritDoc
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param \Qinvoice\Connect\Api\Data\ExportProductDataInterface[] $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }
}
