<?php

declare(strict_types=1);

namespace Drupal\department_of_eudaimonia\Plugin\ConfigAction;

use Drupal\content_moderation\Plugin\WorkflowType\ContentModerationInterface;
use Drupal\Core\Config\Action\Attribute\ConfigAction;
use Drupal\Core\Config\Action\ConfigActionException;
use Drupal\Core\Config\Action\ConfigActionPluginInterface;
use Drupal\Core\Config\ConfigManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\workflows\WorkflowInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Adds arbitrary entity-type/bundle pairs to a Content Moderation workflow.
 *
 * Core's `add_moderation` action only has derivatives for entity types that
 * have a bundle entity type (e.g. `addNodeTypes` for node). Canvas pages are a
 * bundle-less content entity, so this action fills that gap — letting a recipe
 * moderate `canvas_page` (or any bundle-less entity) at apply time, after the
 * entity's module is installed but before its moderated content is imported.
 *
 * Example usage in recipe.yml:
 * @code
 * config:
 *   actions:
 *     workflows.workflow.editorial:
 *       addModerationEntityTypes:
 *         canvas_page: [canvas_page]
 * @endcode
 */
#[ConfigAction(
  id: 'addModerationEntityTypes',
  admin_label: new TranslatableMarkup('Add entity types to a Content Moderation workflow'),
  entity_types: ['workflow'],
)]
final class AddModerationEntityTypes implements ConfigActionPluginInterface, ContainerFactoryPluginInterface {

  public function __construct(
    private readonly ConfigManagerInterface $configManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static($container->get(ConfigManagerInterface::class));
  }

  /**
   * {@inheritdoc}
   *
   * @param string $configName
   *   The workflow config name.
   * @param mixed $value
   *   A map of entity_type_id => list of bundles to moderate. For a bundle-less
   *   entity, use the entity type id as the (single) bundle.
   */
  public function apply(string $configName, mixed $value): void {
    $workflow = $this->configManager->loadConfigEntityByName($configName);
    assert($workflow instanceof WorkflowInterface);

    $plugin = $workflow->getTypePlugin();
    if (!$plugin instanceof ContentModerationInterface) {
      throw new ConfigActionException("The addModerationEntityTypes config action only works with Content Moderation workflows.");
    }

    assert(is_array($value));
    foreach ($value as $entity_type_id => $bundles) {
      foreach ((array) $bundles as $bundle) {
        $plugin->addEntityTypeAndBundle((string) $entity_type_id, (string) $bundle);
      }
    }
    $workflow->save();
  }

}
