<?php

namespace Drupal\value\Normalizer;

use Drupal\file\FileInterface;
use Drupal\file\Plugin\Field\FieldType\FileItem;

class FileFieldItemNormalizer extends FieldItemNormalizer {

  /**
   * {@inheritdoc}
   */
  protected $supportedInterfaceOrClass = [FileItem::class];

  /**
   * {@inheritdoc}
   */
  public function normalize($object, $format = NULL, array $context = []) {
    $attributes = parent::normalize($object, $format, $context);

    /** @var FileInterface $file */
    if ($file = $object->get('entity')->getValue()) {
      $attributes['uri'] = $file->getFileUri();
      $attributes['url'] = file_create_url($attributes['uri']);
    }

    return $attributes;
  }
}
