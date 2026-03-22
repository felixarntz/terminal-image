# Terminal Image (PHP) - AGENTS.md

This project is a PHP port of the popular JavaScript library [`terminal-image`](https://www.npmjs.com/package/terminal-image). It allows to use the exact same functionality in PHP based projects, e.g. PHP CLI tools.

## Critical Code Requirements

- All code must be backward compatible with PHP 7.4.

## Workflow Commands

- `composer phpcs` — Run PHP_CodeSniffer to check code style
- `composer phpcbf` — Auto-fix code style issues
- `composer phpstan` — Run PHPStan static analysis
- `composer test` — Alias for `test:unit`
- `composer test:unit` — Run unit tests

<!-- opensrc:start -->

## Source Code Reference

Source code for dependencies is available in `opensrc/` for deeper understanding of implementation details.

See `opensrc/sources.json` for the list of available packages and their versions.

Use this source code when you need to understand how a package works internally, not just its types/interface.

### Fetching Additional Source Code

To fetch source code for a package or repository you need to understand, run:

```bash
npx opensrc <package>           # npm package (e.g., npx opensrc zod)
npx opensrc pypi:<package>      # Python package (e.g., npx opensrc pypi:requests)
npx opensrc crates:<package>    # Rust crate (e.g., npx opensrc crates:serde)
npx opensrc <owner>/<repo>      # GitHub repo (e.g., npx opensrc vercel/ai)
```

<!-- opensrc:end -->
