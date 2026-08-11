# Inclusion / exclusion ledger

This project was consolidated from the internal `demo_cms` "Demo Framework" (`dfr_*`) recipe tree into
a single, publicly installable module + recipes. This file records what was kept, changed, and left
out, and why.

## Excluded — recipes

- `dfr_acquia_dam` — Acquia DAM integration.
- `dfr_mcp_tools` — MCP (Model Context Protocol) tools.

## Excluded — modules

- **`acquia_dam`** — Acquia DAM (proprietary/credentialed; not needed for a general template).
- **`mcp_tools`** and all its submodules — MCP tooling.
- **`ai_media_image`** — only publishes an **alpha** release (`1.0.0-alpha4`); requiring it would force
  every consumer site to lower `minimum-stability`. Add it manually if you want AI-generated media.
- **`entity_clone`** — only publishes **beta** releases; same stability problem. It's a content-cloning
  convenience, easy to add with `composer require drupal/entity_clone` (and a stability tweak).

## Excluded — themes / custom code

- **`astral`** custom theme → replaced by Canvas's `canvas_stark` (front-end) so nothing overrides the
  USWDS component styling. Astral's header/footer page-regions were retargeted to `canvas_stark`.
- **`dfr_tools_*`** custom helper modules → replaced by this project's single
  `department_of_eudaimonia` module. Their `config_rewrite` tweaks became native recipe config actions
  or shipped config; the Canvas file-reference plugin was ported and generalised.

## Included

- Full **AI** stack: `ai`, `ai_agents` (+ `ai_agents_explorer`, `ai_agents_extra`), `ai_assistant_api`,
  `ai_chatbot`, `ai_automators`, `ai_ckeditor`, `ai_content_suggestions`, `ai_image_alt_text`,
  `ai_provider_openai`, `canvas_ai`, `field_widget_actions`, `modeler_api` — plus the Article-form
  "Generate" buttons and AI CKEditor integration.
- Drupal **Canvas** + the full USWDS component library, content types (Article/Program/Person), media,
  editorial workflow, JSON:API layer, SEO/authoring helpers, Gin admin theme, and the demo content.

## Config / security changes made during consolidation

- The `acquia_dam` media-source branch was stripped from 6 USWDS media components (they now reference
  regular `image` media only).
- `astral.settings.yml` (custom theme settings) removed.
- The `default_client` **OAuth consumer secret** was removed — no shared credential is shipped; each
  site generates its own.
- The `.acquia/` CI scaffolding was removed from the public repo.

## Re-included via the bundled module

Two things a pure config-only recipe cannot do, now handled by `department_of_eudaimonia`'s plugins:

- **Banner icons** — `SetCanvasFileReferences` resolves the header banner's media-icon UUIDs to
  references in the Canvas page-region config (the `eud_icons` recipe creates the media first).
- **Categorized component folders** — `SetComponentFolders` moves components out of Canvas's
  auto-assigned "Other" folder into named category folders after import.
