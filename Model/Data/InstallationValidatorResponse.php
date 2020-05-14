<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */
namespace Qinvoice\Connect\Model\Data;

class InstallationValidatorResponse implements \Qinvoice\Connect\Api\Data\InstallationValidatorResponseInterface
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var array
     */
    private $response;

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

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
