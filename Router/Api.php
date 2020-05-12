<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Router;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;

class Api implements RouterInterface
{
    /**
     * @var \Qinvoice\Connect\Model\Call
     */
    private $call;

    /**
     * Api constructor.
     * @param \Magento\Framework\App\ResponseFactory $responseFactory
     */
    public function __construct(
        \Qinvoice\Connect\Model\Call $call
    ) {
        $this->call = $call;
    }

    public function match(RequestInterface $request)
    {
        if ($request->getParam('qc') !== null) {
            return $this->call;
        }
    }
}
