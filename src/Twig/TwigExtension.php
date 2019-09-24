<?php

namespace Drupal\value\Twig;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class TwigExtension extends \Twig_Extension {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * @var \Symfony\Component\Serializer\SerializerInterface
   */
  protected $serializer;

  /**
   * TwigExtension constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   * @param \Symfony\Component\Serializer\SerializerInterface $serializer
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, ModuleHandlerInterface $module_handler, SerializerInterface $serializer) {
    $this->entityTypeManager = $entity_type_manager;
    $this->moduleHandler = $module_handler;
    $this->serializer = $serializer;
  }

  /**
   * {@inheritdoc}
   */
  public function getFilters() {
    $filters = [
      // |markup.
      new \Twig_SimpleFilter('markup', [$this, 'markup']),

      // |truncate
      new \Twig_SimpleFilter('truncate', [$this, 'truncate']),

      // |words
      new \Twig_SimpleFilter('words', [$this, 'words']),
    ];

    // Array filters.
    $filters[] = new \Twig_SimpleFilter('pick', [$this, 'arrayPick']);
    $filters[] = new \Twig_SimpleFilter('rename_keys', [
      $this,
      'arrayRenameKeys',
    ]);
    $filters[] = new \Twig_SimpleFilter('where', [$this, 'arrayWhere']);

    // |image_style
    if ($this->moduleHandler->moduleExists('image')) {
      $filters[] = new \Twig_SimpleFilter('image_style', [$this, 'imageStyle']);
    }

    return $filters;
  }

  /**
   * {@inheritdoc}
   */
  public function getFunctions() {
    return [
      new \Twig_SimpleFunction('value', [$this, 'value']),
    ];
  }

  /**
   * Returns safe markup for rendering HTML.
   *
   * @param string $text
   *   The escaped text to render.
   *
   * @param null $format
   *   The text format to use for processing.
   *
   * @return array
   *   A renderable array.
   */
  public function markup($text, $format = NULL) {
    $value = $text;

    // If an array is passed, use its value.
    if (is_array($text) && isset($text['value'])) {
      $value = $text['value'];
    }

    if ($format) {
      return [
        '#type' => 'processed_text',
        '#text' => $value,
        '#format' => $format,
      ];
    }

    return [
      '#markup' => $value,
    ];
  }

  /**
   * Truncates the number of characters in a string.
   *
   * @param $value
   * @param int $length
   * @param string $suffix
   *
   * @return string
   */
  public function truncate($string, $length = 100, $suffix = '...') {
    if (mb_strwidth($string, 'UTF-8') <= $length) {
      return $string;
    }

    return rtrim(mb_strimwidth($string, 0, $length, '', 'UTF-8')) . $suffix;
  }

  /**
   * Truncates the number of words in a string.
   *
   * @param $string
   * @param int $length
   * @param string $suffix
   *
   * @return string
   */
  public function words($string, $length = 100, $suffix = '...') {
    preg_match('/^\s*+(?:\S++\s*+){1,' . $length . '}/u', $string, $matches);

    if (!isset($matches[0]) || mb_strlen($string) === mb_strlen($matches[0])) {
      return $string;
    }

    return rtrim($matches[0]) . $suffix;
  }

  /**
   * Returns the URL of an image in the image_style specified.
   *
   * @param $uri
   *   The URI of the image.
   * @param $image_style
   *   The image style.
   *
   * @return null|string
   *   The URL of the image with the style or NULL if not found.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function imageStyle($uri, $image_style) {
    $storage = $this->entityTypeManager->getStorage('image_style');
    /** @var \Drupal\image\ImageStyleInterface $style */
    if ($style = $storage->load($image_style)) {
      return $style->buildUrl($uri);
    }

    return NULL;
  }

  /**
   * @param array $field
   *   An entity reference field array with target_id and target_type.
   *
   * @return array|null
   *   The normalized entity array or null.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function value(array $field) {
    // Check for target_id and target_type.
    if (empty($field['target_id']) || empty($field['target_type'])) {
      // TODO: Throw exception?
      trigger_error('Missing target_id or target_type values.');
    }

    if ($entity = $this->entityTypeManager->getStorage($field['target_type'])->load($field['target_id'])) {
      return $this->serializer->normalize($entity, 'value');
    }

    return NULL;
  }

  /**
   * Get a subset from an array based on a keys array.
   *
   * @param $array
   * @param array|string $keys
   *
   * @return array
   */
  public function arrayPick($array, $keys) {
    if (!is_array($keys)) {
      $keys = [$keys];
    }

    return array_intersect_key($array, array_flip((array) $keys));
  }

  /**
   * Renames the keys in an array.
   *
   * @param $array
   * @param $keys
   *
   * @return array
   */
  public function arrayRenameKeys($array, $keys) {
    if (is_array($array) && is_array($keys)) {
      $renamed = [];
      foreach ($array as $key => $value) {
        $key = array_key_exists($key, $keys) ? $keys[$key] : $key;
        $renamed[$key] = is_array($value) ? $this->arrayRenameKeys($value, $keys) : $value;
      }
      return $renamed;
    }

    return $array;
  }

  /**
   * Filters an array.
   *
   * @param $array
   * @param $value
   * @param string $operator
   *
   * @return array
   */
  public function arrayWhere($array, $value, $operator = '=') {
    return array_filter($array, function ($_value) use ($value, $operator) {
      switch ($operator) {
        case '=':
          return $_value == $value;
        case '!=':
          return $_value != $value;
        case '<':
          return $_value < $value;
        case '>':
          return $_value > $value;
        case '<=':
          return $_value <= $value;
        case '>=':
          return $_value >= $value;
        case '===':
          return $_value === $value;
        case '!==':
          return $_value !== $value;
        default:
      }

      return FALSE;
    });
  }
}
