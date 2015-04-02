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
    $url = 'http://example.com/site/dir/file.patch';
    $request = $this->getMock('Guzzle\Http\Message\RequestInterface');
    $request->expects($this->once())
      ->method('setResponseBody')
      ->with('test/dir/file.patch')
      ->will($this->returnSelf());
    $http_client = $this->getMock('Guzzle\Http\ClientInterface');
    $http_client->expects($this->once())
      ->method('get')
      ->with($url)
      ->will($this->returnValue($request));
    $fetch = new TestFetch([], 'fetch', []);
    $fetch->setValidate('test/dir');
    $fetch->setHttpClient($http_client);
    $fetch->run($this->job, [['url' => $url]]);
  }
}

class TestFetch extends Fetch {
  use TestSetupValidateDirTrait;

  function setHttpClient(ClientInterface $http_client) {
    $this->httpClient = $http_client;
  }
}
