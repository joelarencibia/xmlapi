<?php

namespace MessageHandlerBundle\Controller;

use FOS\RestBundle\Util\Codes;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BaseController
 *
 * Provides methods for FORM and Security
 * @package AppBundle\Controller
 */
class BaseController extends Controller
{

  /**************************************
   * FORMS
   *************************************/

  /**
   * Create a form without a name
   *
   * @param null $type
   * @param null $data
   * @param array $options
   *
   * @return Form|FormInterface
   */
  public function createForm($type = null, $data = null, array $options = array())
  {
    $form = $this->container->get('form.factory')->createNamed(
      null, //since we're not including the form name in the request, set this to null
      $type,
      $data,
      $options
    );

    return $form;
  }

  /**
   * Get rid on any fields that don't appear in the form
   *
   * @param Request $request
   * @param Form $form
   */
  protected function removeExtraFields(Request $request, Form $form)
  {
    $data = $request->request->all();
    $children = $form->all();
    $data = array_intersect_key($data, $children);
    $request->request->replace($data);
  }

    /**
     * @return Response
     */
  protected function createResponse()
  {
      $response = new Response();
      $response->setStatusCode(Codes::HTTP_OK);
      $response->headers->add(array('Content-Type'=>'text/xml'));

      return $response;
  }


  protected function createNack(\SimpleXMLElement $xmlContent = null, $error = ''){

      $xmlNack = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><nack></nack>');

      if ($xmlContent){
          $xmlNack->addChild('header');
          foreach ($xmlContent->header->children() as $child) {
              $xmlNack->header->addChild($child->getName(),$child);
          }
          $xmlNack->addChild('body');
          $xmlNack->body->addChild('error');
          $xmlNack->body->error->addChild('code','400');
          $xmlNack->body->error->addChild('message',$error);
      }
      else{
          $xmlNack->addChild('header', 'empty');
          $xmlNack->addChild('body');
          $xmlNack->body->addChild('error');
          $xmlNack->body->error->addChild('code','400');
          $xmlNack->body->error->addChild('message',$error);
      }



      $response = $this->createResponse();
      return $this->render('MessageHandlerBundle:Default:response.xml.twig',array('xml'=>$xmlNack->asXML()),$response);

  }

    protected function createPingResponse(\SimpleXMLElement $xmlContent){

        $xmlPingResponse = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><ping_response></ping_response>');

        $sender = trim((string)$xmlContent->header->sender);
        $recipient = trim((string)$xmlContent->header->recipient);
        $xmlPingResponse->header->type = 'ping_response';
        $xmlPingResponse->header->sender = $recipient;
        $xmlPingResponse->header->recipient = $sender;
        if ($xmlContent->header->reference)
        {
            $reference= trim((string)$xmlContent->header->reference);
            $xmlPingResponse->header->reference = $reference;
        }

        $date = \DateTime::createFromFormat('U.u',microtime(true));
        $date->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        $xmlPingResponse->header->timestamp = $date->format('Y-m-d\TH:i:s.vP');

        $echo = trim((string)$xmlContent->body->echo);
        $xmlPingResponse->body->echo = $echo;

        return $xmlPingResponse;

    }


    protected function createReverseResponse(\SimpleXMLElement $xmlContent){

        $xmlReverseResponse = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><reverse_response></reverse_response>');

        $sender = trim((string)$xmlContent->header->sender);
        $recipient = trim((string)$xmlContent->header->recipient);
        $xmlReverseResponse->header->type = 'reverse_response';
        $xmlReverseResponse->header->sender = $recipient;
        $xmlReverseResponse->header->recipient = $sender;
        if ($xmlContent->header->reference)
        {
            $reference= trim((string)$xmlContent->header->reference);
            $xmlReverseResponse->header->reference = $reference;
        }

        $date = \DateTime::createFromFormat('U.u',microtime(true));
        $date->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        $xmlReverseResponse->header->timestamp = $date->format('Y-m-d\TH:i:s.vP');

        $stringElem = trim((string)$xmlContent->body->string);
        $xmlReverseResponse->body->string = $stringElem;
        $xmlReverseResponse->body->addChild('reverse', strrev($stringElem));

        return $xmlReverseResponse;

    }


}
