<?php

namespace MessageHandlerBundle\Controller;

use MessageHandlerBundle\Controller\BaseController;
use MessageHandlerBundle\Entity\Message;
use MessageHandlerBundle\Form\MessageType;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View as FOSView;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class ReverseController
 * @RouteResource("reverse")
 * @package MessageHandlerBundle\Controller
 */
class ReverseController extends BaseController
{

    /**
     * Create a XML Reverse Response.
     *
     *
     * @View(statusCode=200, serializerEnableMaxDepthChecks=true)
     * @param Request $request
     * @ApiDoc(
     *     section="Reverse" ,
     *     description="Post Reverse",
     *     views = { "default" },
     *     statusCodes = {
     *         200 = "Returned when successful",
     *         400 = "Returned when Bad Request"
     *     },
     *     input="MessageHandlerBundle\Form\MessageType"
     * )
     *
     * @return Response
     */
    public function postAction(Request $request)
    {
        /** @var Message $entity */
        $entity = new Message();

        $form = $this->createForm(new MessageType(), $entity, array('method' => $request->getMethod()));
        $this->removeExtraFields($request, $form);
        $form->handleRequest($request);

        try{

            $xml = $entity->getXml();

            if (trim($xml) == '')
                return $this->createNack(null,'The requested message is empty.');

            $xmlContent = new \SimpleXMLElement($xml);

            if ($form->isValid()) {

                $type = trim((string)$xmlContent->header->type);
                if ($type != 'reverse_request')
                    return $this->createNack($xmlContent,'The requested message has an invalid type.');

                $xmlReverseResponse = $this->createReverseResponse($xmlContent);

                $response = $this->createResponse();
                return $this->render('MessageHandlerBundle:Default:response.xml.twig',array('xml'=>$xmlReverseResponse->asXML()),$response);
            }
            else
                return $this->createNack($xmlContent,'The requested message is not recognized.');

        }
        catch (\Exception $e)
        {
            return $this->createNack(null,$e->getMessage());
        }


    }
}
