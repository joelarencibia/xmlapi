<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 24/8/2019
 * Time: 05:41
 */

namespace MessageHandlerBundle\Entity;

use MessageHandlerBundle\Validator\Constraints\XmlMsg;

/**
 * Class Message
 * @package MessageHandlerBundle\Entity
 */
class Message
{
    /**
     * xml
     * @var string
     * @XmlMsg()
     */
    private $xml;

    /**
     * @return string
     */
    public function getXml()
    {
        return $this->xml;
    }

    /**
     * @param string $xml
     */
    public function setXml($xml)
    {
        $this->xml = $xml;
    }


}