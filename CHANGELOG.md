# Changelog

## 2.0.0
- Added product tab chat drafts (Hyvä + Luma) with configurable messages per tab.
- Added "Web Widget HTML Code" admin field: paste LiveChat / Text's own snippet instead of entering a license number (it stays correct when they change the snippet). Validated on save.
- License number kept as a legacy fallback, used only when no widget code is set.
- Added optional group config.
- Added load strategy option (first interaction / immediate) and product SKU/name session variables.
- Added store-view scope to all settings (existing website-scope values still apply).
- Added CSP whitelist (livechatinc.com, text.com), ACL resource and SecureHtmlRenderer output.
- Guard against double-loading when LiveChat is also embedded via GTM / theme.
- Escaped all config output (license was previously echoed raw into JS).
- Removed `Block\Loader` and the static `livechat.js` asset in favour of a view model + inline loader.

## 1.0.0
- Deferred LiveChat embed, per-website enable, separate chat sessions between groups.
