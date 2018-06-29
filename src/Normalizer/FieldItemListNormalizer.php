<?php

namespace Drupal\value\Normalizer;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\field\FieldConfigInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class FieldItemListNormalizer extends SerializerAwareNormalizer implements NormalizerInterface {

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
   * {@inheritdoc}
   */
  public function supportsNormalization($data, $format = NULL) {
    return $format === 'value' && $data instanceof FieldItemListInterface;
  }

  /**
   * Returns the cardinality for the field definition.
   */
  protected function getCardinality($object) {
    if ($definition = $object->getFieldDefinition()) {

      // Handle FieldConfig.
      if ($definition instanceof FieldConfigInterface) {
        $definition = $definition->getFieldStorageDefinition();
      }

      return $definition->getCardinality();
    }

    return 0;
  }

}
