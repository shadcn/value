<?php

namespace Drupal\value\Normalizer;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\serialization\Normalizer\ComplexDataNormalizer;
use Symfony\Component\Serializer\SerializerAwareTrait;

class FieldItemNormalizer extends ComplexDataNormalizer {

  use SerializerAwareTrait;

  /**
   * {@inheritdoc}
   */
  public function normalize($object, $format = NULL, array $context = []) {
    $attributes = parent::normalize($object, $format, $context);
    return (count($attributes) == 1 && (isset($attributes['value']))) ? $attributes['value'] : $attributes;
  }

  /**
   * {@inheritdoc}
   */
  public function supportsNormalization($data, $format = NULL) {
    return $format === 'value' && $data instanceof FieldItemInterface;
  }
}
