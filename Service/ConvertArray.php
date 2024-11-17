<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Service;

use Magento\Framework\Exception\LocalizedException;

class ConvertArray
{
    /**
     * @param array $array
     * @param string $rootName
     * @return \SimpleXMLElement
     * @throws LocalizedException
     */
    public function assocToXml(array $array, $rootName = '_')
    {
        if (empty($rootName) || is_numeric($rootName)) {
            throw new LocalizedException(
                new \Magento\Framework\Phrase(
                    "The root element can't be empty or use numbers. Change the element and try again."
                )
            );
        }

        $xmlStr = <<<XML
<?xml version='1.0' encoding='UTF-8' standalone='yes'?>
<$rootName></$rootName>
XML;
        $xml = new \SimpleXMLElement($xmlStr);
        foreach (array_keys($array) as $key) {
            if (is_numeric($key)) {
                throw new LocalizedException(
                    new \Magento\Framework\Phrase('An error occurred. Use non-numeric array root keys and try again.')
                );
            }
        }
        return self::_assocToXml($array, $rootName, $xml);
    }

    /**
     * Function, that actually recursively transforms array to xml
     *
     * @param array $array
     * @param string $rootName
     * @param \SimpleXMLElement $xml
     * @return \SimpleXMLElement
     * @throws LocalizedException
     */
    private function _assocToXml(array $array, $rootName, \SimpleXMLElement $xml)
    {
        $hasNumericKey = false;
        $hasStringKey = false;
        foreach ($array as $key => $value) {
            if (!is_array($value)) {
                if (is_string($key)) {
                    if ($key === $rootName) {
                        throw new LocalizedException(
                            new \Magento\Framework\Phrase(
                                "An associative key can't be the same as its parent associative key. "
                                . "Verify and try again."
                            )
                        );
                    }
                    $hasStringKey = true;
                    $xml->addChild($key, htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
                } elseif (is_int($key)) {
                    $hasNumericKey = true;
                    $xml->addChild($key, $value);
                }
            } elseif (is_array($value)
                && in_array('@attributes', array_keys($value), 1)
                && in_array('@value', array_keys($value), 1)
            ) {
                if (is_array($value['@value'])) {
                    self::_assocToXml($value['@value'], $key, $xml->{$key});
                } else {
                    $xml->addChild($key, $value['@value']);
                }
                foreach ($value['@attributes'] as $aKey => $aValue) {
                    if(!is_null($aValue)) {
                        $xml->{$key}->addAttribute($aKey, $aValue);
                    }
                }
            } elseif (is_array($value)
                && in_array('@array', array_keys($value), 1)
            ) {
                $xml->addChild($key);
                foreach ($value['@array']['@values'] as $aVal) {
                    if (!is_array($aVal)) {
                        $xml->{$key}->addChild($value['@array']['@key'], $aVal);
                    } else {
                        $newEl = $xml->{$key}->addChild($value['@array']['@key']);
                        self::_assocToXml($aVal, $value['@array']['@key'], $newEl);
                    }
                }
            } elseif (is_array($value) && $key === '@attributes') {
                foreach ($value as $aKey => $aValue) {
                    $xml->addAttribute($aKey, $aValue);
                }
            } else {
                $xml->addChild($key);
                self::_assocToXml($value, $key, $xml->{$key});
            }
        }
        if ($hasNumericKey && $hasStringKey) {
            throw new LocalizedException(
                new \Magento\Framework\Phrase(
                    "Associative and numeric keys can't be mixed at one level. Verify and try again."
                )
            );
        }
        return $xml;
    }
}
