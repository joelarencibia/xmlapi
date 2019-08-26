<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 24/8/2019
 * Time: 07:08
 */

namespace MessageHandlerBundle\Validator;


/**
 * Class XMLValidator
 * @package MessageHandlerBundle\Service
 */
class XMLValidator
{
    /**
     * @var int
     */
    public $feedErrors = 0;
    /**
     * Formatted libxml Error details
     *
     * @var array
     */
    public $errorDetails;
    /**
     * Validation Class constructor Instantiating DOMDocument
     *
     * @param \DOMDocument $handler [description]
     */
    public function __construct()
    {
        $this->handler = new \XMLReader();
    }
    /**
     * @param \libXMLError object $error
     *
     * @return string
     */
    private function libxmlDisplayError($error)
    {
        $errorString = "Error $error->code in $error->file (Line:{$error->line}):";
        $errorString .= trim($error->message);
        return $errorString;
    }
    /**
     * @return array
     */
    private function libxmlDisplayErrors()
    {
        $errors = libxml_get_errors();
        $result    = [];
        foreach ($errors as $error) {
            $result[] = $this->libxmlDisplayError($error);
        }
        libxml_clear_errors();
        return $result;
    }
    /**
     * Validate Incoming Feeds against Listing Schema
     *
     * @param resource $feeds
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function validateFeeds($feeds, $feedSchema)
    {
        if (!class_exists('XMLReader')) {
            throw new \DOMException("'XMLReader' class not found!");
            return false;
        }
        if (!file_exists($feedSchema)) {
            throw new \Exception('Schema is Missing, Please add schema to feedSchema property');
            return false;
        }

        libxml_disable_entity_loader( false );
        $this->handler->XML($feeds);
        $this->handler->setSchema($feedSchema);
        libxml_use_internal_errors(true);
        while($this->handler->read()) {
            if (!$this->handler->isValid()) {
                $this->errorDetails = $this->libxmlDisplayErrors();
                $this->feedErrors   = 1;
            } else {
                return true;
            }
        };

        return false;
    }
    /**
     * Display Error if Resource is not validated
     *
     * @return array
     */
    public function displayErrors()
    {
        return $this->errorDetails;
    }
}