<?php

namespace Drupal\language_lighter\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\language\Entity\ContentLanguageSettings;
use Drupal\language\Form\ContentLanguageSettingsForm;

/**
 * Class LanguageLighterForm.
 */
class LanguageLighterForm extends ContentLanguageSettingsForm {
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
}
