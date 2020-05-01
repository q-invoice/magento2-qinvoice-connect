<?php

namespace Qinvoice\Connect\Model;

use Magento\Framework\DataObject;

class Document
{
    const ROOT_NAME = "request";

    /**
     * @return array
     */
    public function toArray()
    {
        $arr = [];
        $arr['items'] = [
            "name" => "Cocococ!",
            "price" => "Blablabla!",
            "@attributes" => [
                "name" => "coll name"
            ],
        ];
        return $arr;
    }
}
