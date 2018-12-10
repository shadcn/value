<?php

namespace Drupal\value;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Theme\ThemeInitializationInterface;
use Drupal\Core\Theme\ThemeManager as CoreThemeManager;
use Drupal\Core\Theme\ThemeNegotiatorInterface;

/**
 * Extends the core ThemeManager.
 */
class ThemeManager extends CoreThemeManager {

  /**
   * The variable prefix for Twig templates.
   */
  public static $PREFIX = '_';

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * {@inheritdoc}
   */
  public function __construct($root, ThemeNegotiatorInterface $theme_negotiator, ThemeInitializationInterface $theme_initialization, ModuleHandlerInterface $module_handler, LanguageManagerInterface $language_manager) {
    parent::__construct($root, $theme_negotiator, $theme_initialization, $module_handler);
    $this->languageManager = $language_manager;
  }

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
    if ($entity = $this->getEntity($hook, $variables)) {
      $variables['#_value'][static::$PREFIX . $entity->bundle()] = \Drupal::service('serializer')
        ->normalize($entity, 'value');
    }

    return $variables;
  }

  /**
   * Finds the entity in context from the variables array.
   *
   * @param $hook
   * @param $variables
   *
   * @return \Drupal\Core\Entity\ContentEntityInterface|null
   */
  protected function getEntity($hook, $variables) {
    if (is_string($hook)) {
      $entity = NULL;

      // Find the entity based on the hook.
      // TODO: Refactor this into plugins?
      switch ($hook) {
        case 'block':
          if (isset($variables['content']["#block_content"])) {
            $entity = $variables['content']["#block_content"];
          }
          break;
        default:
          if (isset($variables["#$hook"])) {
            $entity = $variables["#$hook"];
          }
          break;
      }

      // Comment.
      if (strpos($hook, 'comment') === 0) {
        $entity = $variables["#comment"];
      }

      // We want ContentEntity only for now.
      if ($entity instanceof ContentEntityInterface) {
        // Check for entity translation.
        /** @var ContentEntityInterface $entity */
        $langcode = $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_CONTENT)->getId();
        if ($entity->hasTranslation($langcode)) {
          return $entity->getTranslation($langcode);
        }

        return $entity;
      }
    }

    return NULL;
  }
}
