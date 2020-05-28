<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */
namespace Qinvoice\Connect\Model\Data;

use Qinvoice\Connect\Service\ModuleVersion;

class ApiResponseData implements \Qinvoice\Connect\Api\Data\ResponseDataInterface
{
    /**
     * @var string
     */
    private $version;

    /**
     * @var int
     */
    private $code;

    /**
     * @var string
     */
    private $message;
    /**
     * @var ModuleVersion
     */
    private $moduleVersion;

    /**
     * ApiResponseData constructor.
     * @param ModuleVersion $moduleVersion
     */
    public function __construct(
        ModuleVersion $moduleVersion
    ) {
        $this->moduleVersion = $moduleVersion;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        if ($this->version === null) {
            try {
                $this->version = $this->moduleVersion->get();
            } catch (\Exception $e) {
                $this->version = "Cannot read module version";
            }
        }
        return $this->version;
    }

    /**
     * @param string $version
     * @return $this
     */
    public function setVersion(string $version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @param int $code
     * @return $this
     */
    public function setCode(int $code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message)
    {
        $this->message = $message;
        return $this;
    }
}
