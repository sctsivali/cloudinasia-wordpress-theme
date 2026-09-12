# Cloud in Asia WordPress Theme

Public source review repository for the currently active Cloud in Asia WordPress theme and the redesign handoff.

## Repository layout

```text
theme/cia-recovery-3.7.1/   Byte-identical copy of the audited production theme
docs/REDESIGN-HANDOFF.md    Technical and design handoff for the next theme
baseline/                   Live-source provenance and SHA-256 checksums
```

## Important baseline note

The active production directory identifies itself as `cia-recovery-3.7.1`, but nine files contain post-release hotfixes and no longer match the bundled `RELEASE-MANIFEST.json`:

- `assets/cia-chrome.css`
- `footer.php`
- `functions.php`
- `header.php`
- `home.php`
- `page-about.php`
- `page-speakers.php`
- `page.php`
- `style.css`

The Git repository contains the **actual inspected live bytes**, not a reconstruction from the stale release manifest. Use `baseline/THEME-LIVE-SHA256SUMS.txt` to verify the copied theme.

```bash
sha256sum --check baseline/THEME-LIVE-SHA256SUMS.txt
```

## Development rule

- Do not deploy directly from this baseline branch.
- Build the redesign as a new versioned sibling theme, not as in-place edits to the active production theme.
- Use a feature branch and pull request for implementation.
- Keep content types, taxonomy, bilingual behavior, SEO hooks, consent-gated analytics, and Inquiry Desk integration compatible.
- Validate on staging before any production activation.

See [`docs/REDESIGN-HANDOFF.md`](docs/REDESIGN-HANDOFF.md) for the full technical contract and design checklist.

## Public-source provenance

The baseline was copied read-only from the active production theme. Internal infrastructure identifiers and filesystem paths are intentionally omitted from this public repository.

The bundled portraits and organization marks are baseline assets from the live theme. Their inclusion here does not grant new rights for unrelated reuse; confirm attribution, consent, and trademark requirements before republishing them elsewhere.

No credential, token, connection string, database dump, upload directory, or private inquiry data is included.
