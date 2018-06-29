<?php

namespace Drupal\value\Normalizer;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\serialization\Normalizer\ComplexDataNormalizer;

class FieldItemNormalizer extends ComplexDataNormalizer {

  /**
   * {@inheritdoc}
   */
  protected $format = 'value';

  /**
   * {@inheritdoc}
   */
  protected $supportedInterfaceOrClass = [FieldItemInterface::class];

  /**
   * {@inheritdoc}
   */
  public function normalize($object, $format = NULL, array $context = []) {
    $attributes = parent::normalize($object, $format, $context);
    return (count($attributes) == 1 && (isset($attributes['value']))) ? $attributes['value'] : $attributes;
  }
}
