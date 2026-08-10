# Department of Eudaimonia

A Drupal 11 **Site recipe** that sets up a U.S. government department site template. Its centerpiece
is a library of **USWDS (U.S. Web Design System) Canvas JS code components** — banners, headers,
footers, accordions, cards, forms, alerts, process lists, and more — ready to compose pages in
[Drupal Canvas](https://www.drupal.org/project/canvas). Pages render on Canvas's own
`canvas_stark` theme, so the components' USWDS styling is not overridden by an opinionated site theme.

The recipe depends only on Drupal core and contributed modules from drupal.org (no custom theme —
`canvas_stark` ships with `drupal/canvas`).

## What it installs

- **Drupal Canvas** with the full USWDS "Eud-Kit" component library, plus pre-built header and
  footer page regions (targeting the `canvas_stark` theme).
- **Content types:** Article, Program, Person.
- **Media** (image, video, document, remote video) with focal point, media library, and image styles.
- **Editorial workflow** (content moderation) for Articles, Persons, and Canvas Pages, with Scheduler
  integration.
- **JSON:API** layer (jsonapi, jsonapi_extras, consumers) used by Canvas.
- **SEO/authoring** helpers: pathauto, redirect, metatag, simple_sitemap, linkit, diff, tokens.
- **Themes:** Canvas Stark (default, front-end) and Gin (admin).
- Demo content: a home page (`/eud-home`), example articles, programs, taxonomy, and menus.

## Requirements

- Drupal core `^11.4`.
- The contributed projects listed in `composer.json` (pulled in automatically when you require this
  recipe via Composer).

## Usage

From a Composer-managed Drupal site:

```bash
composer require drupal/department_of_eudaimonia
drush recipe recipes/department_of_eudaimonia
drush cache:rebuild
```

(Adjust the path to wherever Composer unpacks the recipe, e.g. `recipes/`.)

## Notes

- The default front-end theme is **`canvas_stark`** (ships with `drupal/canvas`). It is intentionally
  unstyled so the USWDS components render as designed; an opinionated theme (e.g. Olivero) would
  override their styling. The header/footer USWDS page regions target `canvas_stark`. If you switch to
  another front-end theme, rebuild the header/footer in the Canvas UI from the included components.
- The USWDS banner's small decorative icons (US flag, `.gov`/HTTPS lock) are not pre-populated. In a
  config-only recipe these image props (media entity references) cannot be resolved at config-apply
  time, so add them once in the Canvas UI after install (edit the header region banner → pick the
  imported icon media). Everything else in the banner/header/footer works out of the box.
- This recipe intentionally excludes AI, Acquia DAM, and MCP integrations so it stays lightweight and
  dependency-clean for general use.

## License

GPL-2.0-or-later.
