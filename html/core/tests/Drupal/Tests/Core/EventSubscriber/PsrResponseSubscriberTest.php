<?php

namespace Drupal\Tests\Core\EventSubscriber;

use Drupal\Tests\UnitTestCase;
use Drupal\Core\EventSubscriber\PsrResponseSubscriber;

/**
 * @coversDefaultClass \Drupal\Core\EventSubscriber\PsrResponseSubscriber
 * @group EventSubscriber
 */
class PsrResponseSubscriberTest extends UnitTestCase {

  /**
   * The tested path root subscriber.
   *
   * @var \Drupal\Core\EventSubscriber\PsrResponseSubscriber
   */
  protected $psrResponseSubscriber;

  /**
   * The tested path root subscriber.
   *
   * @var \Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $httpFoundationFactoryMock;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    $factory = $this->getMockBuilder('Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface')
      ->disableOriginalConstructor()
      ->getMock();
    $factory
      ->expects($this->any())
      ->method('createResponse')
      ->willReturn($this->createMock('Symfony\Component\HttpFoundation\Response'));

    $this->httpFoundationFactoryMock = $factory;

    $this->psrResponseSubscriber = new PsrResponseSubscriber($this->httpFoundationFactoryMock);
  }

  /**
   * Tests altering and finished event.
   *
   * @covers ::onKernelView
   */
  public function testConvertsControllerResult() {
    $event = $this->createEventMock($this->createMock('Psr\Http\Message\ResponseInterface'));
    $event
      ->expects($this->once())
      ->method('setResponse')
      ->with($this->isInstanceOf('Symfony\Component\HttpFoundation\Response'));
    $this->psrResponseSubscriber->onKernelView($event);
  }

  /**
   * Tests altering and finished event.
   *
   * @covers ::onKernelView
   */
  public function testDoesNotConvertControllerResult() {
    $event = $this->createEventMock([]);
    $event
      ->expects($this->never())
      ->method('setResponse');
    $this->psrResponseSubscriber->onKernelView($event);
    $event = $this->createEventMock(NULL);
    $event
      ->expects($this->never())
      ->method('setResponse');
    $this->psrResponseSubscriber->onKernelView($event);
  }

  /**
   * Sets up an alias event that return $controllerResult.
   *
   * @param mixed $controller_result
   *   The return Object.
   *
   * @return \Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent|\PHPUnit\Framework\MockObject\MockObject
   *   A mock object to test.
   */
  protected function createEventMock($controller_result) {
    $event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent')
      ->disableOriginalConstructor()
      ->getMock();
    $event
      ->expects($this->once())
      ->method('getControllerResult')
      ->willReturn($controller_result);
    return $event;
  }

}
