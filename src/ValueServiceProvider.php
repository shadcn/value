<?php

namespace Drupal\value;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Symfony\Component\DependencyInjection\Reference;

class ValueServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    $container->getDefinition('theme.manager')
      ->setClass(ThemeManager::class)
    ->setArguments([new Reference('app.root'), new Reference('theme.negotiator'), new Reference('theme.initialization'), new Reference('module_handler'), new Reference('language_manager')]);
  }

}
