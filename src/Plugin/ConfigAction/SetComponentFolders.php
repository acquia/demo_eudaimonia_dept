<?php

declare(strict_types=1);

namespace Drupal\department_of_eudaimonia\Plugin\ConfigAction;

use Drupal\canvas\Entity\Folder;
use Drupal\Core\Config\Action\Attribute\ConfigAction;
use Drupal\Core\Config\Action\ConfigActionPluginInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Organises Canvas components into named category folders.
 *
 * Canvas auto-assigns every newly created JS component to a folder named
 * "Other" (see \Drupal\canvas\Entity\Component::postSave() /
 * ComponentSourceBase::determineDefaultFolder()). That makes shipping named
 * folder *config* impossible — importing a folder that lists a component that is
 * already in "Other" violates the one-folder-per-item constraint.
 *
 * This action runs as PHP *after* the components exist and moves each component
 * into its target folder (removing it from whatever folder it currently lives
 * in first), so the invariant is never violated. It is a global operation and
 * ignores $configName; anchor it on any config entity that exists at apply time
 * (e.g. the header page_region).
 *
 * Example usage in recipe.yml:
 * @code
 * config:
 *   actions:
 *     canvas.page_region.canvas_stark.header:
 *       setComponentFolders:
 *         'Forms and Inputs': ['js.uswds_text_input', 'js.uswds_checkbox']
 *         'Data and Tables': ['js.uswds_table', 'js.uswds_table_row']
 * @endcode
 */
#[ConfigAction(
  id: 'setComponentFolders',
  admin_label: new TranslatableMarkup('Organise Canvas components into named folders'),
  entity_types: ['page_region'],
)]
final class SetComponentFolders implements ConfigActionPluginInterface, ContainerFactoryPluginInterface {

  private const string CONFIG_ENTITY_TYPE_ID = 'component';

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static();
  }

  /**
   * {@inheritdoc}
   *
   * @param string $configName
   *   Ignored — this is a global operation.
   * @param mixed $value
   *   A map of folder name => list of component IDs to place in it.
   */
  public function apply(string $configName, mixed $value): void {
    assert(is_array($value));
    foreach ($value as $folder_name => $item_ids) {
      $folder = Folder::loadByNameAndConfigEntityTypeId((string) $folder_name, self::CONFIG_ENTITY_TYPE_ID);
      if (!$folder instanceof Folder) {
        $folder = Folder::create([
          'name' => $folder_name,
          'configEntityTypeId' => self::CONFIG_ENTITY_TYPE_ID,
          'status' => TRUE,
          'weight' => 0,
        ]);
        $folder->save();
      }
      foreach ((array) $item_ids as $item_id) {
        // Canvas auto-assigns new components to "Other"; move them out first so
        // the one-folder-per-item constraint is never violated.
        $current = Folder::loadByItemAndConfigEntityTypeId($item_id, self::CONFIG_ENTITY_TYPE_ID);
        if ($current instanceof Folder && $current->id() !== $folder->id()) {
          $current->removeItem($item_id)->save();
        }
      }
      $folder->addItems(array_values((array) $item_ids))->save();
    }
  }

}
