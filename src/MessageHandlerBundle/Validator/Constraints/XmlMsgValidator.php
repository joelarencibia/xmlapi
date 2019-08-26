<?php

namespace MessageHandlerBundle\Validator\Constraints;

use MessageHandlerBundle\Validator\XMLValidator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


class XmlMsgValidator extends ConstraintValidator {


    /** @var XMLValidator  */
    private $validator;

    public function __construct()
    {
        $this->validator = new XMLValidator();
    }


    public function validate($value, Constraint $constraint)
    {
        try{
            if ( trim($value) == "" ) {
                $this->context->buildViolation('Empty XML Message')
                    ->addViolation();
                return false;
            }

            $feedSchema = $this->selectXSD($value);

            $xmlOK= $this->validator->validateFeeds($value,$feedSchema);

            if (!$xmlOK) {
                $this->context->buildViolation("XML not Ok.")
                    ->addViolation();
            }
        }
        catch (\Exception $e)
        {
            $this->context->buildViolation('The requested message is not recognized.')
                ->addViolation();
            return false;
        }

    }

    public function selectXSD($xml){
        $xmlContent = new \SimpleXMLElement($xml);

        $type = trim((string)$xmlContent->header->type);
        return "file://".__DIR__."/../../Resources/xsds/$type.xsd";
    }
}
