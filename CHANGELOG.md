# Changelog

## 2.0.0
- Added product tab chat drafts (Hyvä + Luma) with configurable messages per tab.
- Added Organization ID, integration name, product name and group config for newer LiveChat snippets.
- Added load strategy option (first interaction / immediate) and product SKU/name session variables.
- Added store-view scope to all settings (existing website-scope values still apply).
- Added CSP whitelist, ACL resource and SecureHtmlRenderer output.
- Guard against double-loading when LiveChat is also embedded via GTM / theme.
- Escaped all config output (license was previously echoed raw into JS).
- Removed `Block\Loader` and the static `livechat.js` asset in favour of a view model + inline loader.

## 1.0.0
- Deferred LiveChat embed, per-website enable, separate chat sessions between groups.
