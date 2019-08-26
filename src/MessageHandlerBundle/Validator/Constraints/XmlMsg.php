<?php

namespace MessageHandlerBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;



/**
 * @Annotation
 */
class XmlMsg extends Constraint{
    public $message = 'Invalid XML Message';
}

