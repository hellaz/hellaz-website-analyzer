<?php
namespace Hellaz\Analysis;

use Hellaz\Exceptions\AnalysisException;
use Hellaz\Utilities\Formatter;

class Parser {
    public function parse($html, $url) {
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);

        return [
            'metadata' => $this->extract_metadata($xpath),
            'social' => $this->extract_social($xpath),
            'feeds' => $this->find_feeds($html),
            'security' => $this->analyze_security($url)
        ];
    }

    private function extract_metadata($xpath) {
        $metadata = [];
        // Extract standard meta tags
        $metadata['title'] = $this->get_first_match([
            '//meta[@property="og:title"]/@content',
            '//meta[@name="twitter:title"]/@content',
            '//title/text()'
        ], $xpath);

        // Similar extraction for description, keywords, etc.
        return $metadata;
    }

    private function extract_social($xpath) {
        $social = [];
        // Find social links
        $social_links = $xpath->query('//a[contains(@href, "twitter.com") or contains(@href, "facebook.com")]');
        foreach ($social_links as $link) {
            $social[] = [
                'url' => $link->getAttribute('href'),
                'platform' => $this->detect_platform($link->getAttribute('href'))
            ];
        }
        return $social;
    }

    private function find_feeds($html) {
        preg_match_all('/<link[^>]+(type="application\/(rss|atom)\+xml")[^>]+href="([^"]+)"/i', $html, $matches);
        return $matches[3] ?? [];
    }

    private function analyze_security($url) {
        $domain = Formatter::normalize_domain($url);
        return [
            'https' => parse_url($url, PHP_URL_SCHEME) === 'https',
            'hsts' => $this->check_hsts($domain)
        ];
    }
}
