<?php

namespace Drupal\value\Normalizer;

use Drupal\Core\Url;
use Drupal\link\LinkItemInterface;

class LinkFieldItemNormalizer extends FieldItemNormalizer {

  /**
   * {@inheritdoc}
   */
  protected $supportedInterfaceOrClass = [LinkItemInterface::class];

  /**
   * {@inheritdoc}
   */
  public function normalize($object, $format = NULL, array $context = []) {
    $attributes = parent::normalize($object, $format, $context);

    // Add a url attribute.
    if (isset($attributes['uri'])) {
      $attributes['url'] = Url::fromUri($attributes['uri'])->toString();
    }

    return $attributes;
  }
}
