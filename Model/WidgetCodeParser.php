<?php
declare(strict_types=1);

namespace Zero1\Livechat\Model;

/**
 * Splits the "Web Widget HTML Code" pasted from LiveChat / Text into its
 * <script> and <noscript> parts so each script can be output through
 * SecureHtmlRenderer (CSP nonce/hash). Comments and any other markup are dropped.
 */
class WidgetCodeParser
{
    /**
     * @return array{scripts: array<int, array{attributes: array<string,string>, content: string}>, noscript: string}
     */
    public function parse(string $html): array
    {
        $result = ['scripts' => [], 'noscript' => ''];
        if (trim($html) === '') {
            return $result;
        }

        // Pull <noscript> blocks out first so scripts inside them are not executed
        if (preg_match_all('#<noscript\b[^>]*>.*?</noscript>#is', $html, $noscript)) {
            $result['noscript'] = implode("\n", $noscript[0]);
            $html = (string) preg_replace('#<noscript\b[^>]*>.*?</noscript>#is', '', $html);
        }
        $html = (string) preg_replace('#<!--.*?-->#s', '', $html);

        if (preg_match_all('#<script\b([^>]*)>(.*?)</script\s*>#is', $html, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $attributes = $this->parseAttributes($match[1]);
                $type = strtolower($attributes['type'] ?? 'text/javascript');
                if (!in_array($type, ['text/javascript', 'application/javascript', 'module'], true)) {
                    continue;
                }
                $content = trim($match[2]);
                if ($content === '' && !isset($attributes['src'])) {
                    continue;
                }
                $result['scripts'][] = ['attributes' => $attributes, 'content' => $content];
            }
        }

        return $result;
    }

    /**
     * @return array<string,string>
     */
    private function parseAttributes(string $raw): array
    {
        $attributes = [];
        $pattern = '#([a-zA-Z_:][-a-zA-Z0-9_:.]*)(?:\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s"\'=<>`]+)))?#';
        if (preg_match_all($pattern, $raw, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                $value = $m[2] ?? '';
                if ($value === '' && isset($m[3]) && $m[3] !== '') {
                    $value = $m[3];
                }
                if ($value === '' && isset($m[4]) && $m[4] !== '') {
                    $value = $m[4];
                }
                $attributes[strtolower($m[1])] = html_entity_decode($value, ENT_QUOTES | ENT_HTML5);
            }
        }
        return $attributes;
    }
}
