# Department of Eudaimonia

A Drupal 11 **site template**, delivered as a module that bundles a set of modular **recipes** plus a
few small helper plugins. Its centerpiece is a library of **USWDS (U.S. Web Design System) Canvas JS
code components** — banners, headers, footers, accordions, cards, forms, alerts, process lists, and
more — ready to compose pages in [Drupal Canvas](https://www.drupal.org/project/canvas). Pages render
on Canvas's own `canvas_stark` theme so the components' USWDS styling isn't overridden.

It depends only on Drupal core and contributed modules/themes.

## What it installs

- **Drupal Canvas** + the full USWDS "Eud-Kit" component library, organised into category folders,
  with a pre-built header/footer (banner, `.gov`/HTTPS notice, nav, language switcher).
- **Content types:** Article, Program, Person, with demo content.
- **Media** (image, video, document, remote video) + focal point, media library, image styles.
- **Editorial workflow** (content moderation) for Articles, Persons and Canvas Pages, + Scheduler.
- **JSON:API** layer (jsonapi, jsonapi_extras, consumers, OAuth) used by Canvas / the Canvas CLI.
- **AI authoring:** AI core + OpenAI provider, AI CKEditor, content suggestions, image alt-text,
  agents/chatbot, and "Generate" buttons on the Article form.
- **Themes:** Canvas Stark (default, front-end) and Gin (admin).

## Structure

This is a `drupal-module` (`acquia/department_of_eudaimonia`) that ships:

```
department_of_eudaimonia.info.yml
src/Plugin/ConfigAction/     SetCanvasFileReferences, SetComponentFolders, AddModerationEntityTypes
recipes/
  department_of_eudaimonia/  main Site recipe (composes the sub-recipes below)
  eud_common  eud_media  eud_api  eud_canvas
  eud_ai  eud_ai_content  eud_ai_chatbot  eud_canvas_ai
  eud_person  eud_article  eud_program
  eud_icons  eud_uswds
```

The three config-action plugins do things a config-only recipe cannot: wire the banner's media icons
into a config page-region (`setCanvasFileReferences`), organise components into named folders despite
Canvas's auto-foldering (`setComponentFolders`), and moderate the bundle-less `canvas_page` entity
(`addModerationEntityTypes`).

## Usage

```bash
composer require acquia/department_of_eudaimonia
drush recipe web/modules/contrib/department_of_eudaimonia/recipes/department_of_eudaimonia
drush cache:rebuild
```

**DDEV:** `ddev drush` runs from the docroot, so pass the absolute container path:
```bash
ddev drush recipe /var/www/html/web/modules/contrib/department_of_eudaimonia/recipes/department_of_eudaimonia
```

You can also apply individual sub-recipes (e.g. just the components) by pointing `drush recipe` at that
sub-recipe directory.

## Post-install setup

- **AI features** need an **OpenAI API key** configured for `ai_provider_openai` (Configuration →
  AI → Providers) before the "Generate" buttons and chatbot do anything. Install works without it.
- **Canvas CLI / OAuth API:** the shipped `default_client` consumer has **no secret** (none is shipped
  for security). Generate one at `/admin/config/services/consumer`, and generate the OAuth keys with
  `drush simple-oauth:generate-keys` (or the Simple OAuth settings page).
- Content is English (`en`). If your site's default language isn't `en`, the demo content lives under
  the `/en/…` path prefix (set `en` as the default language for plain URLs).

## What's intentionally excluded

See [docs/EXCLUSIONS.md](docs/EXCLUSIONS.md) for the full ledger (Acquia DAM, MCP tools, and two
alpha/beta-only modules were left out to keep installs clean on standard-stability sites).

## License

GPL-2.0-or-later.
