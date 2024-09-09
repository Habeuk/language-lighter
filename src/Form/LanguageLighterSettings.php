<?php

namespace Drupal\language_lighter\Form;

use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LanguageLighterSettings.
 */
class LanguageLighterSettings extends ConfigFormBase {

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;


  /**
   * The entity bundle info.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  protected $entityTypeBundleInfo;

  /**
   * Constructs an \Drupal\views\Plugin\views\argument_validator\Entity object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity type bundle info.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, EntityTypeBundleInfoInterface $entity_type_bundle_info) {
    $this->entityTypeManager = $entity_type_manager;
    $this->entityTypeBundleInfo = $entity_type_bundle_info;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('entity_type.bundle.info')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'language_lighter_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return  ['language_lighter.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('language_lighter.settings')->get("entities_light_lang");
    // dd($config);
    $entity_types = $this->entityTypeManager->getDefinitions();
    $bundles = $this->entityTypeBundleInfo->getAllBundleInfo();

    $labels = [];
    $default = [];
    foreach ($entity_types as $entity_type_id => $entity_type) {
      if (!$entity_type instanceof ContentEntityTypeInterface || !$entity_type->hasKey('langcode') || !isset($bundles[$entity_type_id])) {
        continue;
      }
      $labels[$entity_type_id] = $entity_type->getLabel() ?: $entity_type_id;
      $default[$entity_type_id] = $config[$entity_type_id] ?? FALSE;
    }


    $form['entities_light_lang'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Entities'),
      '#description' => $this->t('Entities that use Language Lighter module'),
      '#options' => $labels,
      '#default_value' => $default,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    foreach ($form_state->getValues() as $key => $value) {
      // @TODO: Validate fields.
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Display result.
    $lightLangSetting = $form_state->getValue('entities_light_lang');

    $configId = $this->getEditableConfigNames()[0];
    $this->config($configId)
      ->set(
        'entities_light_lang',
        $form_state->getValue('entities_light_lang')
      )
      ->save();
    parent::submitForm($form, $form_state);
  }
}
