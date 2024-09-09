<?php

namespace Drupal\language_lighter\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LanguageLighterController.
 */
class LanguageLighterController extends ControllerBase {

  /**
   * Drupal\Core\Form\FormBuilderInterface definition.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->formBuilder = $container->get('form_builder');
    return $instance;
  }

  /**
   * Light_form.
   *
   * @return string
   *   Return Hello string.
   */
  public function light_form($entity_type_id, $bundle) {
    // dump($entity_type_id, $bundle);
    $form = $this->formBuilder->getForm('Drupal\language_lighter\Form\LanguageLighterForm');
    return $form;
  }
}
