<?php

namespace Drupal\oer\Controller;

use Drupal\Core\Controller\ControllerBase;

class OERController extends ControllerBase {
  
  /**
   *
   * @var \Drupal\Core\Database\Connection
   */
  private $connection;
  
  function __construct(\Drupal\Core\Database\Connection $connection) {
    $this->connection = $connection;
  }

  public static function create(\Symfony\Component\DependencyInjection\ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }


  public function home() {
    return [
      '#theme' => 'home',
    ];
  }
  
}