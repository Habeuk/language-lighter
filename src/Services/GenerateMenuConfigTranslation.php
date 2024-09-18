<?php

namespace Drupal\language_lighter\Services;

use Drupal\Core\Controller\ControllerBase;

/**
 *
 * @author stephane
 *        
 */
class GenerateMenuConfigTranslation extends ControllerBase {
  
  /**
   * Permet de generer la configuration pour la traduction des menus.
   *
   * @param string $menu_id
   */
  function createConfigTranslate(string $menu_id) {
    $id = 'menu_link_content.' . $menu_id;
    $configTranslate = \Drupal\language\Entity\ContentLanguageSettings::load($id);
    if (!$configTranslate) {
      $values = [
        'id' => $id,
        'target_entity_type_id' => 'menu_link_content',
        'status' => true,
        'target_bundle' => $menu_id,
        'third_party_settings' => [
          'content_translation' => [
            'enabled' => true,
            'bundle_settings' => [
              'untranslatable_fields_hide' => 0
            ]
          ]
        ]
      ];
      $configTranslate = \Drupal\language\Entity\ContentLanguageSettings::create($values);
      $configTranslate->save();
    }
    return $configTranslate;
  }
}
