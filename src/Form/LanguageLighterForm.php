<?php

namespace Drupal\language_lighter\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\language\Entity\ContentLanguageSettings;

/**
 * Class LanguageLighterForm.
 */
class LanguageLighterForm extends FormBase {

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Drupal\Core\Entity\EntityTypeBundleInfoInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  protected $entityTypeBundleInfo;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->entityTypeManager = $container->get('entity_type.manager');
    $instance->entityTypeBundleInfo = $container->get('entity_type.bundle.info');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'language_lighter_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $entity_type_id = $this->getRouteMatch()->getParameter('entity_type_id');
    $bundle_id = $this->getRouteMatch()->getParameter('bundle');


    $entity_type = $this->entityTypeManager->getDefinition($entity_type_id);
    $bundle = $this->entityTypeBundleInfo->getBundleInfo($entity_type_id)[$bundle_id] ?? null;


    if (!$entity_type instanceof ContentEntityTypeInterface || !$entity_type->hasKey('langcode') || !isset($bundle)) {
      return [];
    }



    $default[$entity_type_id] = [FALSE];
    $label = $entity_type->getLabel() ?: $entity_type_id;
    $config = ContentLanguageSettings::loadByEntityTypeBundle($entity_type_id, $bundle_id);

    if (!$config->isDefaultConfiguration()) {
      $default[$entity_type_id] = $entity_type_id;
    }

    $form = [
      '#labels' => [
        $entity_type_id => $label
      ],
      '#attached' => [
        'library' => [
          'language/drupal.language.admin',
        ],
      ],
      '#attributes' => [
        'class' => 'language-content-settings-form',
      ],
    ];

    $form['entity_types'] = [
      '#title' => $this->t('Custom language settings'),
      '#type' => 'checkboxes',
      '#options' => [
        $entity_type_id => $label
      ],
      '#default_value' => $default,
    ];

    $form['settings'] = ['#tree' => TRUE];
    $form['settings'][$entity_type_id] = [
      '#title' => $label,
      '#type' => 'container',
      '#entity_type' => $entity_type_id,
      '#theme' => 'language_content_settings_table',
      '#bundle_label' => $entity_type->getBundleLabel() ?? $label,
      '#states' => [
        'visible' => [
          ':input[name="entity_types[' . $entity_type_id . ']"]' => ['checked' => TRUE],
        ],
      ],
    ];
    // dd($bundle_id);
    $form['settings'][$entity_type_id][$bundle_id]['settings'] = [
      '#type' => 'item',
      '#label' => $bundle['label'],
      'language' => [
        '#type' => 'language_configuration',
        '#entity_information' => [
          'entity_type' => $entity_type_id,
          'bundle' => $bundle_id,
        ],
        '#default_value' => $config,
      ],
    ];
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save configuration'),
      '#button_type' => 'primary',
    ];
    // dump($form);
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
    $settings = $form_state;
    dd($settings);
    $entity_types = $form_state->getValue('entity_types');
    foreach ($form_state->getValue('settings') as $entity_type => $entity_settings) {
      foreach ($entity_settings as $bundle => $bundle_settings) {
        $config = ContentLanguageSettings::loadByEntityTypeBundle($entity_type, $bundle);
        if (empty($entity_types[$entity_type])) {
          $bundle_settings['settings']['language']['language_alterable'] = FALSE;
        }
        $config->setDefaultLangcode($bundle_settings['settings']['language']['langcode'])
          ->setLanguageAlterable($bundle_settings['settings']['language']['language_alterable'])
          ->save();
      }
    }
    $this->messenger()->addStatus($this->t('Settings successfully updated.'));
  }
}
