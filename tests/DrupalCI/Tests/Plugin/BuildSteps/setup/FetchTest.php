<?php

/**
 * @file
 * Contains \DrupalCI\Tests\Plugin\BuildSteps\setup\FetchTest.
 */

namespace DrupalCI\Tests\Plugin\BuildSteps\setup;

use DrupalCI\Plugin\BuildSteps\setup\Fetch;
use DrupalCI\Tests\DrupalCITestCase;
use Guzzle\Http\ClientInterface;

class FetchTest extends DrupalCITestCase {

  function testRun() {
    $file = 'file.patch';
    $url = 'http://example.com/site/dir/' . $file;
    $dir = 'test/dir';

    $request = $this->getMock('Guzzle\Http\Message\RequestInterface');
    $request->expects($this->once())
      ->method('setResponseBody')
      ->with("$dir/$file")
      ->will($this->returnSelf());
    $request->expects($this->once())
      ->method('send');

    $http_client = $this->getMock('Guzzle\Http\ClientInterface');
    $http_client->expects($this->once())
      ->method('get')
      ->with($url)
      ->will($this->returnValue($request));

    $fetch = new TestFetch([], 'fetch', []);
    $fetch->setValidate($dir);
    $fetch->setHttpClient($http_client);
    $fetch->run($this->job, [['url' => $url]]);
  }
}

class TestFetch extends Fetch {
  use TestSetupBaseTrait;

  function setHttpClient(ClientInterface $http_client) {
    $this->httpClient = $http_client;
  }
}
