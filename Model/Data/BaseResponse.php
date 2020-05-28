<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */
namespace Qinvoice\Connect\Model\Data;

class BaseResponse implements \Qinvoice\Connect\Api\Data\BaseResponseInterface
{
    /**
     * @var array
     */
    protected $response;

    /**
     * @inheritDoc
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param \Qinvoice\Connect\Api\Data\ResponseDataInterface $resonse
     * @return $this
     */
    public function setResponse(\Qinvoice\Connect\Api\Data\ResponseDataInterface $resonse)
    {
        $this->response = $resonse;
        return $this;
    }
}
