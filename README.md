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

This is a **`drupal-recipe`** package (`acquia/department_of_eudaimonia`) — a main recipe that composes
modular sub-recipes:

```
department_of_eudaimonia/recipe.yml   main Site recipe (composes the sub-recipes below)
eud_common  eud_media  eud_api  eud_canvas
eud_ai  eud_ai_content  eud_ai_chatbot  eud_canvas_ai
eud_person  eud_article  eud_program
eud_icons  eud_uswds
```

It depends on a small companion module,
[`acquia/department_of_eudaimonia_helper`](https://packagist.org/packages/acquia/department_of_eudaimonia_helper),
pulled in automatically by Composer. That module provides three config-action plugins that do things a
config-only recipe cannot: wire the banner's media icons into a config page-region
(`setCanvasFileReferences`), organise components into named folders despite Canvas's auto-foldering
(`setComponentFolders`), and moderate the bundle-less `canvas_page` entity (`addModerationEntityTypes`).
The recipe enables the module and calls these actions.

## Usage

```bash
composer require acquia/department_of_eudaimonia
drush recipe recipes/department_of_eudaimonia/department_of_eudaimonia
drush cache:rebuild
```

`composer require` installs both this recipe (to `recipes/department_of_eudaimonia/`) and the helper
module (to `modules/contrib/`). Run the commands from the **project root** (where `recipes/` lives).

**DDEV:** `ddev drush` runs from the docroot, so pass the absolute container path:
```bash
ddev drush recipe /var/www/html/recipes/department_of_eudaimonia/department_of_eudaimonia
```

You can also apply individual sub-recipes by pointing `drush recipe` at a sub-recipe directory, e.g.
`recipes/department_of_eudaimonia/eud_uswds`.

## Post-install setup

- **AI features** need an **OpenAI API key** configured for `ai_provider_openai` (Configuration →
  AI → Providers) before the "Generate" buttons and chatbot do anything. Install works without it.
- **Canvas CLI / OAuth API:** the shipped `default_client` consumer has **no secret** (none is shipped
  for security). Generate one at `/admin/config/services/consumer`, and generate the OAuth keys with
  `drush simple-oauth:generate-keys` (or the Simple OAuth settings page).
- Content is English (`en`). If your site's default language isn't `en`, the demo content lives under
  the `/en/…` path prefix (set `en` as the default language for plain URLs).

## License

GPL-2.0-or-later.
