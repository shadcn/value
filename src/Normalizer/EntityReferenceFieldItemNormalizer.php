<?php

namespace Drupal\value\Normalizer;

use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;
use Drupal\field\Entity\FieldConfig;

class EntityReferenceFieldItemNormalizer extends NormalizerBase {

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
