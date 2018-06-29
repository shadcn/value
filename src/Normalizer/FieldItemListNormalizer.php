<?php

namespace Drupal\value\Normalizer;

use Drupal\Core\Field\FieldConfigBase;
use Drupal\Core\Field\FieldItemListInterface;

class FieldItemListNormalizer extends NormalizerBase {

  /**
   * {@inheritdoc}
   */
  protected $supportedInterfaceOrClass = [FieldItemListInterface::class];

  /**
   * {@inheritdoc}
   */
  public function normalize($object, $format = NULL, array $context = []) {
    $attributes = [];
    foreach ($object as $fieldItem) {
      $attributes[] = $this->serializer->normalize($fieldItem, $format, $context);
    }

    // Return array for multi value only.
    return $this->getCardinality($object) == 1 ? reset($attributes) : $attributes;
  }

  /**
   * Returns the cardinality for the field definition.
   */
  protected function getCardinality($object) {
    if ($definition = $object->getFieldDefinition()) {

      // Handle FieldConfig.
      if ($definition instanceof FieldConfigBase) {
        $definition = $definition->getFieldStorageDefinition();
      }

      return $definition->getCardinality();
    }

    return 0;
  }

}
