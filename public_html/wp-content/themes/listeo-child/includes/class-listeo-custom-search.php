<?php
if (!defined('ABSPATH')) {
    exit;
}

class Listeo_Custom_Search {
    
    public function __construct() {
        add_filter('posts_search', array($this, 'listeo_complete_search_filter'), 500, 2);
        
        if (defined('WP_DEBUG') && WP_DEBUG) {
            add_action('init', array($this, 'debug_listeo_search'));
        }
    }

    /**
     * Funkcja normalizująca tekst
     */
    private function normalize_search_text($text) {
        $replacements = array(
            'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 
            'ó' => 'o', 'ś' => 's', 'ź' => 'z', 'ż' => 'z',
            'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'E', 'Ł' => 'L', 'Ń' => 'N', 
            'Ó' => 'O', 'Ś' => 'S', 'Ź' => 'Z', 'Ż' => 'Z'
        );
        
        $text = str_replace(array_keys($replacements), array_values($replacements), $text);
        $text = strtolower(trim($text));
        return $text;
    }

    /**
     * Funkcja porównująca podobieństwo słów
     */
    private function similar_words($word1, $word2) {
        if (empty($word1) || empty($word2)) return false;
        
        $word1 = $this->normalize_search_text($word1);
        $word2 = $this->normalize_search_text($word2);
        
        if ($word1 === $word2) return true;
        
        if (strlen($word1) <= 4 || strlen($word2) <= 4) {
            return levenshtein($word1, $word2) <= 1;
        }
        
        $max_distance = floor(max(strlen($word1), strlen($word2)) * 0.3);
        return levenshtein($word1, $word2) <= $max_distance;
    }

    /**
     * Główna funkcja wyszukiwania
     */
    public function listeo_complete_search_filter($search, $wp_query) {
        global $wpdb;
        
        if (!isset($_REQUEST['action']) || $_REQUEST['action'] !== 'listeo_get_listings') {
            return $search;
        }
        
        $keyword = isset($_REQUEST['keyword_search']) ? sanitize_text_field($_REQUEST['keyword_search']) : '';
        if (empty($keyword)) {
            return $search;
        }

        // Lista pól meta do przeszukania
        $meta_fields = array(
            '_address',
            '_friendly_address',
            '_email',
            '_phone',
            '_website',
            '_facebook',
            '_twitter',
            '_instagram',
            '_linkedin',
            '_youtube',
            '_listing_title',
            '_listing_description',
            '_nip',
            '_regon',
            '_krs',
            '_company_name',
            '_foundation_date',
            '_city',
            '_state',
            '_postal_code',
            '_location',
            'keywords',
            'listeo_subtitle'
        );

        $search_conditions = array();

        // Obsługa fraz w cudzysłowach
        $exact_phrases = array();
        if (preg_match_all('/"([^"]+)"/', $keyword, $matches)) {
            foreach ($matches[1] as $phrase) {
                $exact_phrases[] = $phrase;
                $keyword = str_replace('"' . $phrase . '"', '', $keyword);
            }
        }

        // Normalizacja i podział na słowa
        $keyword = $this->normalize_search_text($keyword);
        $search_terms = array_filter(explode(' ', $keyword), 'strlen');
        $search_terms = array_merge($search_terms, $exact_phrases);

        // Funkcja do generowania wariantów słowa
        $generate_variants = function($term) {
            $variants = array($term);
            $variants[] = preg_replace('/(.)\1+/', '$1', $term);
            if (strlen($term) > 3) {
                for ($i = 0; $i < strlen($term) - 1; $i++) {
                    $variant = $term;
                    $variant[$i] = $term[$i + 1];
                    $variant[$i + 1] = $term[$i];
                    $variants[] = $variant;
                }
            }
            return array_unique($variants);
        };

        // Wyszukiwanie w podstawowych polach
        foreach ($search_terms as $term) {
            $variants = $generate_variants($term);
            $term_conditions = array();
            
            foreach ($variants as $variant) {
                $variant = $wpdb->esc_like($variant);
                $term_conditions[] = $wpdb->prepare(
                    "($wpdb->posts.post_title REGEXP %s 
                    OR $wpdb->posts.post_content REGEXP %s)",
                    '[[:<:]]' . $variant . '[[:>:]]',
                    '[[:<:]]' . $variant . '[[:>:]]'
                );
                
                if (strlen($variant) > 3) {
                    $term_conditions[] = $wpdb->prepare(
                        "($wpdb->posts.post_title LIKE %s 
                        OR $wpdb->posts.post_content LIKE %s)",
                        '%' . $variant . '%',
                        '%' . $variant . '%'
                    );
                }
            }
            
            $search_conditions[] = '(' . implode(' OR ', $term_conditions) . ')';
        }

        // Wyszukiwanie w meta polach
        foreach ($meta_fields as $meta_key) {
            foreach ($search_terms as $term) {
                $variants = $generate_variants($term);
                $meta_conditions = array();
                
                foreach ($variants as $variant) {
                    $variant = $wpdb->esc_like($variant);
                    $meta_conditions[] = $wpdb->prepare(
                        "EXISTS (
                            SELECT * FROM $wpdb->postmeta 
                            WHERE $wpdb->postmeta.post_id = $wpdb->posts.ID 
                            AND $wpdb->postmeta.meta_key = %s 
                            AND ($wpdb->postmeta.meta_value REGEXP %s 
                                 OR $wpdb->postmeta.meta_value LIKE %s)
                        )",
                        $meta_key,
                        '[[:<:]]' . $variant . '[[:>:]]',
                        '%' . $variant . '%'
                    );
                }
                
                $search_conditions[] = '(' . implode(' OR ', $meta_conditions) . ')';
            }
        }

        // Wyszukiwanie w taksonomii
        $taxonomies = array('listing_category', 'listing_feature', 'region');
        foreach ($taxonomies as $taxonomy) {
            foreach ($search_terms as $term) {
                $variants = $generate_variants($term);
                $tax_conditions = array();
                
                foreach ($variants as $variant) {
                    $variant = $wpdb->esc_like($variant);
                    $tax_conditions[] = $wpdb->prepare(
                        "EXISTS (
                            SELECT 1 
                            FROM $wpdb->term_relationships tr 
                            INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                            INNER JOIN $wpdb->terms t ON tt.term_id = t.term_id
                            WHERE tr.object_id = $wpdb->posts.ID 
                            AND tt.taxonomy = %s 
                            AND (t.name LIKE %s OR t.slug LIKE %s)
                        )",
                        $taxonomy,
                        '%' . $variant . '%',
                        '%' . $variant . '%'
                    );
                }
                
                if (!empty($tax_conditions)) {
                    $search_conditions[] = '(' . implode(' OR ', $tax_conditions) . ')';
                }
            }
        }

        // Łączenie wszystkich warunków
        if (!empty($search_conditions)) {
            $search = ' AND (' . implode(' OR ', $search_conditions) . ') ';
        }

        return $search;
    }

    /**
     * Debug function
     */
    public function debug_listeo_search() {
        if (isset($_REQUEST['action']) && $_REQUEST['action'] === 'listeo_get_listings') {
            error_log('Search Request: ' . print_r($_REQUEST, true));
        }
    }
}