# Installing the Department of Eudaimonia site template

This is a Drupal 11 **recipe** (`acquia/department_of_eudaimonia`). Installing it is two steps:
pull the package with Composer, then apply the recipe with Drush.

> The recipe automatically pulls its companion module
> [`acquia/department_of_eudaimonia_helper`](https://packagist.org/packages/acquia/department_of_eudaimonia_helper)
> and ~35 contributed modules/themes. You require **only** the recipe.

## Requirements

- A Composer-managed Drupal **11.4+** site (e.g. from `drupal/recommended-project`).
- PHP **8.3+**.
- **Drush** — required to apply the recipe. The `recommended-project` template does **not** ship it,
  so step 2 installs it explicitly.
- A database (MariaDB/MySQL in production; SQLite is fine for a quick local try).

> **Using DDEV?** Prefix every `composer` and `drush` command below with `ddev`
> (e.g. `ddev composer require …`, `ddev drush …`). Each step lists the DDEV form underneath.

## 1. (New site only) Create a Drupal project

```bash
composer create-project drupal/recommended-project my-site
cd my-site
```

**DDEV** — after creating the project, set up and start the containers:

```bash
ddev config --project-type=drupal11 --docroot=web
ddev start
```

Skip this whole step if you're adding the template to an existing site — just `cd` into it.

## 2. Require the package

Two dependencies (`mcp_tools` and `entity_clone`) currently ship only **beta** releases, so your
project's root must allow beta stability. This is a **root-only** Composer setting — a package cannot
set it for you — so make this one adjustment first. Check your current value:

```bash
composer config minimum-stability
```

- **`stable`** (the default — also shown if it prints nothing) or **`RC`** → lower it to `beta`:
  ```bash
  composer config minimum-stability beta
  composer config prefer-stable true
  ```
  `prefer-stable: true` keeps every *other* package on its stable release — only `mcp_tools` and
  `entity_clone` drop to beta.
- Already **`beta`**, **`alpha`**, or **`dev`** → **leave it as-is**; beta releases already resolve.
  (Don't raise a looser setting *up* to `beta` — that could destabilise your other requirements.)

Then require the package:

```bash
composer require acquia/department_of_eudaimonia
```

**DDEV:**

```bash
ddev composer config minimum-stability beta   # only if step above said to
ddev composer config prefer-stable true       # only if step above said to
ddev composer require acquia/department_of_eudaimonia
```

> **Prefer not to touch `minimum-stability` at all?** You can instead flag just the two packages as
> beta in the same install command, leaving your global stability untouched:
> ```bash
> composer require acquia/department_of_eudaimonia 'drupal/mcp_tools:^1.0@beta' 'drupal/entity_clone:^2.2@beta'
> ```

This installs the recipe to `recipes/department_of_eudaimonia/`, the helper module to
`web/modules/contrib/department_of_eudaimonia_helper/`, and all contrib dependencies.

### Install Drush

Drush is needed to apply the recipe (step 4) and the `recommended-project` template doesn't ship it,
so install it separately:

```bash
composer require drush/drush
```

**DDEV:**

```bash
ddev composer require drush/drush
```

## 3. Install Drupal (new site only)

If the site isn't installed yet, install it first — the recipe applies **to an installed site**.

```bash
drush site:install standard -y
```

**DDEV:**

```bash
ddev drush site:install standard -y
```

## 4. Apply the recipe

```bash
drush recipe recipes/department_of_eudaimonia/department_of_eudaimonia
drush cache:rebuild
```

**DDEV users:** `ddev drush` runs from the docroot, so pass the absolute container path:

```bash
ddev drush recipe /var/www/html/recipes/department_of_eudaimonia/department_of_eudaimonia
ddev drush cr
```

You can also apply just one sub-recipe (e.g. only the USWDS components):
`drush recipe recipes/department_of_eudaimonia/eud_uswds`.

## 5. Post-install setup

- **AI authoring** — set an **OpenAI API key**: Configuration → AI → Providers → OpenAI. The Article
  "Generate" buttons and the chatbot are inert until a key is configured. (No key ships with the recipe.)
- **Language** — the demo content is English (`en`). If your site's default language is not `en`, the
  content lives under the `/en/…` path prefix; set `en` as the default for plain URLs.

### Optional integrations

These aren't needed to browse the site — set them up only if you use the feature. No secrets ship, so
follow each module's own docs to configure them:

- **Canvas CLI / OAuth API** — generate OAuth keys (`drush simple-oauth:generate-keys`) and add a
  client secret for the `default_client` consumer. See the **Simple OAuth** and **Consumers** modules.
- **MCP tools** — enable and configure per the **MCP Tools** module docs.

## What you get

- Drupal Canvas + the full **USWDS component library** (organised into category folders), a pre-built
  header/footer with the government banner.
- Content types **Article, Program, Person** with demo content, **media**, **editorial workflow**,
  **AI authoring**, JSON:API, SEO helpers, **Gin** admin + **Canvas Stark** front-end themes.
- Front page at `/eud-home`.

## Troubleshooting

- **"Command recipe was not found / Drush was unable to query the database"** — the site isn't
  installed yet. Run `drush site:install` (step 3) **before** `drush recipe`.
- **`composer require` can't find the package** — make sure you're on public Packagist (no extra
  `repositories` entry needed) and the package name is exactly `acquia/department_of_eudaimonia`.
- **`… does not match your minimum-stability`** (for `mcp_tools` or `entity_clone`) — your root
  stability is still stricter than `beta`. Revisit step 2: either set `composer config minimum-stability beta`,
  or use the per-package `@beta` install command shown there.
