<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Service;

use Magento\Framework\Filesystem;

class ModuleVersion
{
    const COMPOSER_FILENAME = '/composer.json';
    /**
     * @var Filesystem\DriverInterface
     */
    private $fileDriver;

    public function __construct(
        \Magento\Framework\Filesystem\Driver\File $file
    ) {
        $this->fileDriver = $file;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function get()
    {
        $moduleDir = $this->fileDriver->getParentDirectory(__DIR__);
        $file = $this->fileDriver->fileGetContents($moduleDir . self::COMPOSER_FILENAME);
        return json_decode($file, 1)['version'];
    }
}
