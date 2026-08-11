<?php

declare(strict_types=1);

namespace Drupal\department_of_eudaimonia\Plugin\ConfigAction;

use Drupal\Core\Config\Action\Attribute\ConfigAction;
use Drupal\Core\Config\Action\ConfigActionPluginInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Sets media entity references in Canvas page-region component inputs by UUID.
 *
 * Canvas page regions are *config*, so a media reference must be a resolved
 * integer target ID — a plain UUID/CANVAS_ENTITY_REFERENCE is only resolved for
 * *content*. This action resolves each media UUID to its integer target ID at
 * apply time (the referenced media must already exist — ship it via a content
 * sub-recipe that runs first) and writes the reference directly to raw config,
 * bypassing the calculateDependencies() assertion that fires when a config
 * entity references content whose IDs are not predictable.
 *
 * Example usage in recipe.yml:
 * @code
 * config:
 *   actions:
 *     canvas.page_region.canvas_stark.header:
 *       setCanvasFileReferences:
 *         89b6c750-66e1-4a56-9602-913bbca845df:
 *           headerIcon: 'aecb5692-68cc-46c3-bcde-a4e1468173aa'
 *         21c94ce6-909e-4614-b9d6-f78db0bb7b7d:
 *           icon: '0baf5e6c-0fde-458f-8a15-78935f1731e6'
 * @endcode
 */
#[ConfigAction(
  id: 'setCanvasFileReferences',
  admin_label: new TranslatableMarkup('Set Canvas component media references by UUID'),
  entity_types: ['page_region'],
)]
final class SetCanvasFileReferences implements ConfigActionPluginInterface, ContainerFactoryPluginInterface {

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly EntityRepositoryInterface $entityRepository,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $container->get(ConfigFactoryInterface::class),
      $container->get(EntityRepositoryInterface::class),
    );
  }

  /**
   * {@inheritdoc}
   *
   * @param string $configName
   *   The config name of the Canvas page_region entity.
   * @param mixed $values
   *   An array keyed by component UUID, each value being an array of
   *   input_name => media_uuid pairs to resolve and set.
   */
  public function apply(string $configName, mixed $values): void {
    $config = $this->configFactory->getEditable($configName);
    $component_tree = $config->get('component_tree') ?? [];

    foreach ($values as $component_uuid => $inputs) {
      // Component tree keys may be plain UUIDs or path-prefixed
      // (e.g. "0:<uuid>"); match on the trailing UUID.
      $tree_key = $this->findTreeKey($component_tree, $component_uuid);
      if ($tree_key === NULL) {
        continue;
      }
      foreach ($inputs as $input_name => $media_uuid) {
        $media = $this->entityRepository->loadEntityByUuid('media', $media_uuid);
        if ($media === NULL) {
          continue;
        }
        $component_tree[$tree_key]['inputs'][$input_name] = [
          'target_id' => (string) $media->id(),
        ];
      }
    }

    $config->set('component_tree', $component_tree)->save();
  }

  /**
   * Finds the component_tree key ending in the given UUID.
   */
  private function findTreeKey(array $component_tree, string $component_uuid): ?string {
    if (isset($component_tree[$component_uuid])) {
      return $component_uuid;
    }
    foreach (array_keys($component_tree) as $key) {
      if (str_ends_with((string) $key, ':' . $component_uuid) || str_ends_with((string) $key, $component_uuid)) {
        return $key;
      }
    }
    return NULL;
  }

}
