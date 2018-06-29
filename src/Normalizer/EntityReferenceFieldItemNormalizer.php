<?php

namespace Drupal\value\Normalizer;

use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class EntityReferenceFieldItemNormalizer extends SerializerAwareNormalizer implements NormalizerInterface {

  /**
   * {@inheritdoc}
   */
  public function normalize($object, $format = NULL, array $context = []) {
    $entity = $object->get('entity')->getValue();
    $attributes = $this->serializer->normalize($entity, $format, $context);
    return $attributes;
  }

  /**
   * {@inheritdoc}
   */
  public function supportsNormalization($data, $format = NULL) {
    return $format == 'value' && $data instanceof EntityReferenceItem;
  }

}
