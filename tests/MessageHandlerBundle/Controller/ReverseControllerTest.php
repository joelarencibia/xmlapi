<?php

namespace Tests\MessageHandlerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Functional Tests for ReverseRequests
 */
class ReverseControllerTest extends WebTestCase
{


    /**
     * Testing the behavior on an empty request
     */
    public function testReverseOnEmptyRequest()
    {
        $client = static::createClient();

        $crawler = $client->request('POST', '/reverses');

        $this->assertContains(
            '<nack><header>empty</header><body><error><code>400</code><message>The requested message is empty.</message></error></body></nack>',
             $client->getResponse()->getContent());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'text/xml; charset=UTF-8'
            ),
            'the "Content-Type" header is "text/xml; charset=UTF-8"' // optional message shown on failure
        );
    }



    /**
     * Testing the behavior on an request with all possible tags
     */
    public function testReverseOnRequestWithAllTags()
    {
        $client = static::createClient();

        $xml=<<<END
<?xml version="1.0" encoding="UTF-8"?>
<reverse_request>
  <header>
    <type>reverse_request</type>
    <sender>VOICEWORKS</sender>
    <recipient>DEMO</recipient>
    <reference>reverse_request_12345</reference>
    <timestamp>2013-12-19T16:45:10.950+01:00</timestamp>
  </header>
  <body>
    <string>Hello!</string>
  </body>
</reverse_request>
END;

        $crawler = $client->request('POST', '/reverses', array('xml' => $xml));

        $this->assertContains(
            '<sender>DEMO</sender>',
            $client->getResponse()->getContent());

        $this->assertContains(
            '<recipient>VOICEWORKS</recipient>',
            $client->getResponse()->getContent());

        $this->assertContains(
            '<string>Hello!</string>',
            $client->getResponse()->getContent());

        $this->assertContains(
            '<reverse>!olleH</reverse>',
            $client->getResponse()->getContent());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'text/xml; charset=UTF-8'
            ),
            'the "Content-Type" header is "text/xml; charset=UTF-8"' // optional message shown on failure
        );
    }


    /**
     * Testing the behavior on an request with the elements reference and timestamp missing
     */
    public function testReverseOnRequestWithMissingTags()
    {
        $client = static::createClient();

        $xml=<<<END
<?xml version="1.0" encoding="UTF-8"?>
<reverse_request>
  <header>
    <type>reverse_request</type>
    <sender>VOICEWORKS</sender>
    <recipient>DEMO</recipient>
  </header>
  <body>
    <string>Hello!</string>
  </body>
</reverse_request>
END;

        $crawler = $client->request('POST', '/reverses', array('xml' => $xml));

        $this->assertContains(
            '<sender>DEMO</sender>',
            $client->getResponse()->getContent());

        $this->assertContains(
            '<recipient>VOICEWORKS</recipient>',
            $client->getResponse()->getContent());

        $this->assertContains(
            '<string>Hello!</string>',
            $client->getResponse()->getContent());

        $this->assertContains(
            '<reverse>!olleH</reverse>',
            $client->getResponse()->getContent());

        $this->assertContains(
            '<timestamp>',
            $client->getResponse()->getContent());


        $this->assertTrue(
            strpos($client->getResponse()->getContent(),'<reference>') == false,
            "the element reference is not present on responses when it wasn't present on requests" // optional message shown on failure
        );

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'text/xml; charset=UTF-8'
            ),
            'the "Content-Type" header is "text/xml; charset=UTF-8"' // optional message shown on failure
        );
    }


    /**
     * Testing the behavior on an request with a bad XML Structure
     */
    public function testReverseOnRequestWithBadXMLStructure()
    {
        $client = static::createClient();

        $xml=<<<END
<?xml version="1.0" encoding="UTF-8"?>
<reverse_request>
  <header>
    <type>reverse_request</type>
    <sender>VOICEWORKS</sender>
    <recipient>DEMO
    <reference>reverse_request_12345</reference>
    <timestamp>2013-12-19T16:45:10.950+01:00</timestamp>
  </header>
  <body>
    <string>Hello!</string>
  </body>
</reverse_request>
END;

        $crawler = $client->request('POST', '/reverses', array('xml' => $xml));

        $this->assertContains(
            '<nack><header>empty</header><body><error><code>400</code><message>String could not be parsed as XML</message></error></body></nack>',
            $client->getResponse()->getContent());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'text/xml; charset=UTF-8'
            ),
            'the "Content-Type" header is "text/xml; charset=UTF-8"' // optional message shown on failure
        );
    }


    /**
     * Testing the behavior on an request with an invalid type
     */
    public function testReverseOnRequestWithInvalidType()
    {
        $client = static::createClient();

        $xml=<<<END
<?xml version="1.0" encoding="UTF-8"?>
<ping_request>
  <header>
    <type>ping_request</type>
    <sender>VOICEWORKS</sender>
    <recipient>DEMO</recipient>
    <reference>ping_request_12345</reference>
    <timestamp>2013-12-19T16:45:10.950+01:00</timestamp>
  </header>
  <body>
    <echo>Hello!</echo>
  </body>
</ping_request>
END;

        $crawler = $client->request('POST', '/reverses', array('xml' => $xml));

        $this->assertContains(
            '<body><error><code>400</code><message>The requested message has an invalid type.</message></error></body>',
            $client->getResponse()->getContent());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'text/xml; charset=UTF-8'
            ),
            'the "Content-Type" header is "text/xml; charset=UTF-8"' // optional message shown on failure
        );
    }

}
