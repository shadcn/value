<?php

namespace Drupal\value\Normalizer;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\serialization\Normalizer\EntityNormalizer;

class ContentEntityNormalizer extends EntityNormalizer {

  /**
   * {@inheritdoc}
   */
  public function normalize($object, $format = NULL, array $context = []) {
    $attributes = parent::normalize($object, $format, $context);

    return $attributes;
  }

  /**
   * {@inheritdoc}
   */
  public function supportsNormalization($data, $format = NULL) {
    return $format === 'value' && $data instanceof ContentEntityInterface;
  }
}
