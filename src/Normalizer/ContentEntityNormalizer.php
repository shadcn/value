<?php

namespace Drupal\value\Normalizer;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\serialization\Normalizer\EntityNormalizer;

class ContentEntityNormalizer extends EntityNormalizer {

  /**
   * {@inheritdoc}
   */
  protected $supportedInterfaceOrClass = [ContentEntityInterface::class];

  /**
   * {@inheritdoc}
   */
  public function normalize($object, $format = NULL, array $context = []) {
    /** @var ContentEntityInterface $object */
    $attributes = parent::normalize($object, $format, $context);

    // Add the canonical url.
    $attributes['url'] = '';
    if ($object->hasLinkTemplate('canonical') && $object->id()) {
      $attributes['url'] = $object->toUrl()->toString();
    }

    return $attributes;
  }
}
