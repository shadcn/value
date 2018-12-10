<?php

namespace Drupal\value\Normalizer;

use Drupal\Core\Entity\TranslatableInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\field\Entity\FieldConfig;

class EntityReferenceFieldItemNormalizer extends NormalizerBase {

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(LanguageManagerInterface $language_manager) {
    $this->languageManager = $language_manager;
  }

  /**
   * {@inheritdoc}
   */
  protected $supportedInterfaceOrClass = [EntityReferenceItem::class];

  /**
   * {@inheritdoc}
   */
  public function normalize($object, $format = NULL, array $context = []) {
    $attributes = [];
    /** @var \Drupal\Core\Entity\EntityInterface $entity */
    if ($entity = $object->get('entity')->getValue()) {

      // Check for entity translation.
      $langcode = $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_CONTENT)->getId();
      if ($entity instanceof TranslatableInterface && $entity->hasTranslation($langcode)) {
        $entity = $entity->getTranslation($langcode);
      }

      $attributes = [
        'target_type' => $entity->getEntityTypeId(),
        'target_uuid' => $entity->uuid(),
        'target_id' => $entity->id(),
      ];

      $definition = $object->getFieldDefinition();
      if ($definition instanceof FieldConfig) {
        $id = $definition->id();
        $key = "{$id}-{$entity->id()}";

        if (!isset($context['depths'][$key])) {
          $context['depths'][$key] = [];
          $attributes = $this->serializer->normalize($entity, $format, $context);
        }
      }
    }

    return $attributes;
  }
}
