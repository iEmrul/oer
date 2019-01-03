<?php


namespace Drupal\oer\Form;

use Drupal\Core\Form\FormBase;
use \Drupal\Core\Form\FormStateInterface;
use \Drupal\Core\Database\Connection;
use \Drupal\Core\Datetime\DateFormatterInterface;
use \Drupal\Core\Cache\CacheTagsInvalidatorInterface;

class OERAddForm extends FormBase {
  
  /**
   * @var \Drupal\Core\Database\Connection
   */
  private $connection;
  
  /**
   * @var \Drupal\Core\Datetime\DateFormatterInterface 
   */
  private $dateFormatter;
  
  /**
   *
   * @var \Drupal\Core\Cache\CacheTagsInvalidatorInterface
   */
  private $cacheTagInvalidator;
  
  function __construct(Connection $connection, DateFormatterInterface $dateFormatter, CacheTagsInvalidatorInterface $cacheTagInvalidator) {
    $this->connection = $connection;
    $this->dateFormatter = $dateFormatter;
    $this->cacheTagInvalidator = $cacheTagInvalidator;
  }

  public static function create(\Symfony\Component\DependencyInjection\ContainerInterface $container) {
    return new static(
      $container->get('database'),
      $container->get('date.formatter'),
      $container->get('cache_tags.invalidator')
    );
  }

  public function getFormId() {
    // Unique ID of the form
    return 'oer_add_form';
  }
  
  public function buildForm(array $form, FormStateInterface $form_state) {
    
    $form['element_id'] = [
      '#type' => 'value',
      '#value' => NULL,
    ];
    
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#required' => TRUE,
      '#default_value' => NULL,
    ];
    $form['type'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Type'),
      '#required' => TRUE,
      '#default_value' => NULL,
    ];
    $default_birthdate = NULL;
    if (!empty($animal)) {
      $default_birthdate = \Drupal\Core\Datetime\DrupalDateTime::createFromTimestamp($animal->birthdate, 'UTC');      
    }
    $form['birthdate'] = [
      '#type' => 'datetime',
      '#title' => $this->t('Birth Date'),
      '#default_value' => $default_birthdate,
      '#date_time_element' => 'none',
    ];
    $form['weight'] = [
      '#type' => 'number',
      '#field_suffix' => $this->t('kg'),
      '#required' => TRUE,
      '#default_value' => !empty($animal) ? $animal->weight : NULL,
    ];
    $form['description'] = [
      '#type' => 'textarea',
      '#rows' => 10,
      '#title' => $this->t('Description'),
      '#default_value' => !empty($animal) ? $animal->description : NULL,
    ];

    
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit Form'),
    ];
    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    

    
  }

  
  public function submitForm(array &$form, FormStateInterface $form_state) {
    ksm($form_state->getValues());
    $animal = [
      'name' => $form_state->getValue('name'),
      'type' => $form_state->getValue('type'),
      'birthdate' => $form_state->getValue('birthdate')->format('U'),
      'weight' => $form_state->getValue('weight'),
      'description' => $form_state->getValue('description'),
    ];
    $this->connection->insert('element_add')
          ->fields($animal)
          ->execute();
  }
}