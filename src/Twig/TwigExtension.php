<?php

namespace Drupal\value\Twig;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

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
   * TwigExtension constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, ModuleHandlerInterface $module_handler) {
    $this->entityTypeManager = $entity_type_manager;
    $this->moduleHandler = $module_handler;
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

    // |image_style
    if ($this->moduleHandler->moduleExists('image')) {
      $filters[] = new \Twig_SimpleFilter('image_style', [$this, 'imageStyle']);
    }

    return $filters;
  }

  /**
   * Returns safe markup for rendering HTML.
   *
   * @param string $text
   *   The escaped text to render.
   *
   * @return array
   *   A renderable array.
   */
  public function markup($text) {
    return [
      '#markup' => $text,
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

    return rtrim(mb_strimwidth($string, 0, $length, '', 'UTF-8')).$suffix;
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
  public function words($string, $length = 100, $suffix = '...')
  {
    preg_match('/^\s*+(?:\S++\s*+){1,'.$length.'}/u', $string, $matches);

    if (! isset($matches[0]) || mb_strlen($string) === mb_strlen($matches[0])) {
      return $string;
    }

    return rtrim($matches[0]).$suffix;
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
   */
  public function imageStyle($uri, $image_style) {
    $storage = $this->entityTypeManager->getStorage('image_style');
    /** @var \Drupal\image\ImageStyleInterface $style */
    if ($style = $storage->load($image_style)) {
      return $style->buildUrl($uri);
    }

    return NULL;
  }
}
