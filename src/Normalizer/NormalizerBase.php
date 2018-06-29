<?php

namespace Drupal\value\Normalizer;

use Drupal\serialization\Normalizer\NormalizerBase as SerializationNormalizerBase;

abstract class NormalizerBase extends SerializationNormalizerBase {

  /**
   * {@inheritdoc}
   */
  protected $format = 'value';
}
