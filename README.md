# ZERO-1 LiveChat for Magento 2 & Hyvä

Integrates [LiveChat](https://www.livechat.com/) with Magento 2.4 (Hyvä and Luma).

## Installation

```bash
composer require zero1/module-livechat
bin/magento module:enable Zero1_Livechat
bin/magento setup:upgrade
```

Configuration: **Stores › Configuration › Customers › ZERO-1 | LiveChat** (default / website / store view scope).

## Features

### Widget embed
- Paste the **Web Widget HTML Code** from LiveChat / Text (*Settings › Channels › Website › Install manually*) into the admin. Because LiveChat change this snippet from time to time, the module outputs their code as-is rather than rebuilding it from a license number. When they update it, paste the new version in.
- The pasted code is split into its `<script>` / `<noscript>` parts. Each script is output through `SecureHtmlRenderer`, and comments and any other markup are dropped. Saving fails if no `<script>` block is found.
- Optional group setting.
- **Load strategy**: on first interaction (scroll / click / key / touch – default, keeps `tracking.js` off the critical path) or immediately (async).
- Separate chat sessions between groups (`chat_between_groups`, off by default).
- Optional `product_sku` / `product_name` session variables on product pages.
- Skips itself if LiveChat is already on the page (e.g. via GTM), so the widget never loads twice.
- Inline scripts go through `SecureHtmlRenderer`; LiveChat hosts are added in `etc/csp_whitelist.xml`.

### Product tab chat drafts
When a customer selects a product info tab (Delivery, Returns, …) the chat input is pre-filled with a configurable message, e.g. *"Hi, I have a question about delivery for {product}"*.

- Hyvä: detects elements whose `@click` / `x-on:click` sets `activeTab = '<key>'`.
- Luma: detects `.product.data.items` tab titles (`id="tab-label-<key>"`).
- Default does **not** pop the chat open: the draft is applied when the customer next opens chat, or straight away if chat is already open. "Open Chat on Tab Click" changes this.
- Optional `last_tab_viewed` session variable so agents see the context.
- Placeholders: `{product}`, `{sku}`, `{tab}`.
- Works whether the widget is embedded by this module or elsewhere.

## Upgrading from 1.x
Nothing breaks on upgrade. `enable` and `license_code` keep their 1.x config paths. A saved license number is still used, shown as **Legacy License Number**, but only while **Web Widget HTML Code** is empty. The default load strategy matches 1.x (load on first interaction). The frontend block keeps the name `zero1.livechat.loader`, so theme layout overrides that reference it still apply.

Recommended: paste the Web Widget HTML Code, then clear the legacy license number.

### How deferred loading works with the pasted code
The official snippet only calls `init()` when `window.__lc.asyncInit` is not set. The module sets `asyncInit` before the snippet runs, then calls `LiveChatWidget.init()` on the visitor's first interaction. If LiveChat ever stops honouring `asyncInit`, the widget will still load, just immediately. The module also checks for an existing `tracking.js` script, so the widget is never loaded twice.

Changes:
- `Block\Loader` and `view/frontend/web/js/livechat.js` are removed; the loader is now an inline template using a view model.
- Requires PHP 7.4+.

Sign up for a [LiveChat free 30 day trial](https://bit.ly/hyva-livechat).
