<?php
namespace Hellaz\Analysis;

class Parser {
    public function parse($html_content, $url) {
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        @$dom->loadHTML($html_content);

        $result = [
            'metadata' => $this->extract_metadata($dom),
            'social_links' => $this->extract_social_links($dom),
            'feeds' => $this->detect_feeds($dom),
            'security' => $this->analyze_security($url),
        ];

        libxml_clear_errors();
        return $result;
    }

    private function extract_metadata($dom) {
        $metadata = [];
        $meta_tags = $dom->getElementsByTagName('meta');
        foreach ($meta_tags as $meta) {
            $name = $meta->getAttribute('name') ?: $meta->getAttribute('property');
            $content = $meta->getAttribute('content');
            if ($name && $content) {
                $metadata[$name] = $content;
            }
        }
        return $metadata;
    }

    private function extract_social_links($dom) {
        $social_links = [];
        $links = $dom->getElementsByTagName('a');
        foreach ($links as $link) {
            $href = $link->getAttribute('href');
            if (strpos($href, 'facebook.com') !== false || strpos($href, 'twitter.com') !== false) {
                $social_links[] = $href;
            }
        }
        return $social_links;
    }

    private function detect_feeds($dom) {
        $feeds = [];
        $links = $dom->getElementsByTagName('link');
        foreach ($links as $link) {
            if ($link->getAttribute('rel') === 'alternate' && in_array($link->getAttribute('type'), ['application/rss+xml', 'application/atom+xml'])) {
                $feeds[] = $link->getAttribute('href');
            }
        }
        return $feeds;
    }

    private function analyze_security($url) {
        $security_info = [];
        // Placeholder for security analysis logic
        return $security_info;
    }
}
