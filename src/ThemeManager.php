<?php

namespace Drupal\value;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Extends the core ThemeManager.
 */
class ThemeManager extends \Drupal\Core\Theme\ThemeManager {

  /**
   * {@inheritdoc}
   */
  public function render($hook, array $variables) {
    $variables = $this->buildValues($hook, $variables);
    return parent::render($hook, $variables);
  }

  /**
   * Preprocesses variables for rendering.
   *
   * @param $hook
   * @param $variables
   *
   * @return
   */
  protected function buildValues($hook, $variables) {
    // We want ContentEntity only for now.
    if ((is_string($hook)) && (isset($variables["#$hook"])) && ($entity = $variables["#$hook"]) && ($entity instanceof ContentEntityInterface)) {
      $variables['#_value']["_{$entity->bundle()}"] = \Drupal::service('serializer')
        ->normalize($entity, 'value');
    }

    return $variables;
  }
}
