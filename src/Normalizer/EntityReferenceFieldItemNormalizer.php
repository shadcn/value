<?php

namespace Drupal\value\Normalizer;

use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;

class EntityReferenceFieldItemNormalizer extends NormalizerBase {

  /**
   * {@inheritdoc}
   */
  protected $supportedInterfaceOrClass = [EntityReferenceItem::class];

  /**
   * {@inheritdoc}
   */
  public function normalize($object, $format = NULL, array $context = []) {
    $entity = $object->get('entity')->getValue();
    $attributes = $this->serializer->normalize($entity, $format, $context);
    return $attributes;
  }
}
