
API Description
========================

MessageHandlerBundle contains the code for the ficticious XML API

The class BaseController implements the common logic to the other two controllers: PingController and ReverseController

PingController handles messages of type ping_request. Its method 
postAction takes as argument a Request object containing a ping_request XML and return a Response object containing a ping_response XML. The Request object encapsulates a HTTP Request with method POST and a body containing an argument of type text/xml named xml. The Response object encapsulates a HTTP Response containing an xml on its body 

ReverseController handles messages of type reverse_request. Its method postAction takes as argument a Request object containing a reverse_request XML and return a Response object containing a reverse_response XML. The Request object encapsulates a HTTP Request with method POST and a body containing an argument of type text/xml named xml. The Response object encapsulates a HTTP Response containing an xml on its body


Entry Points
========================

Requests must be sent to the routes /pings and /reverses. 

The route /pings handles requests of type ping_request. If you send a reverse_request to the route /pings you'll get a nack XML reporting an invalid type of request.

The route /reverses handles requests of type reverse_request. If you send a ping_request to the route /pings you'll get a nack XML reporting an invalid type of request.

When sending a request to the routes /pings or /reverses, be sure of setting the request method to POST and the argument in the request body is named xml.


Swagger UI
========================

The routes / and /api/doc provide a Swagger UI that allow users to visualize and interact with the API's resources.


Tests
========================

The classes PingControllerTest and ReverseControllerTest implements functional tests. The functional tests may be running by executing the command phpunit:

``` 
phpunit -c app/ tests/MessageHandlerBundle/Controller/PingControllerTest.php
```
``` 
 phpunit -c app/ tests/MessageHandlerBundle/Controller/ReverseControllerTest.php
``` 

