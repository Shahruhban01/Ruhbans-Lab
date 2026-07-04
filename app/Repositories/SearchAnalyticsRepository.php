<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class SearchAnalyticsRepository extends BaseRepository
{
    protected string $table = 'search_queries';

    public function logSearch(string $term, array $filters, int $resultCount, array $context = array()): void
    {
        $term = $this->normalizeTerm($term);

        if ($term === '') {
            return;
        }

        $statement = $this->connection->prepare('INSERT INTO search_queries (search_term, normalized_term, filters_json, result_count, ip_address, user_agent, referrer, created_at) VALUES (:search_term, :normalized_term, :filters_json, :result_count, :ip_address, :user_agent, :referrer, :created_at)');
        $statement->execute(array(
            'search_term' => $this->truncate($term, 255),
            'normalized_term' => $this->truncate(function_exists('mb_strtolower') ? mb_strtolower($term, 'UTF-8') : strtolower($term), 255),
            'filters_json' => $filters === array() ? null : json_encode($this->normalizeFilters($filters), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'result_count' => max(0, $resultCount),
            'ip_address' => isset($context['ip_address']) ? $this->truncate((string) $context['ip_address'], 45) : null,
            'user_agent' => isset($context['user_agent']) ? $this->truncate((string) $context['user_agent'], 255) : null,
            'referrer' => isset($context['referrer']) ? $this->truncate((string) $context['referrer'], 255) : null,
            'created_at' => date('Y-m-d H:i:s'),
        ));
    }

    public function popularSearches(int $limit = 10): array
    {
        $limit = max(1, min(25, $limit));
        $statement = $this->connection->prepare('SELECT normalized_term AS term, MAX(search_term) AS search_term, COUNT(*) AS total, MAX(created_at) AS last_searched_at FROM search_queries WHERE normalized_term <> "" GROUP BY normalized_term ORDER BY total DESC, last_searched_at DESC LIMIT :limit');
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function recentSearches(int $limit = 10): array
    {
        $limit = max(1, min(25, $limit));
        $statement = $this->connection->prepare('SELECT normalized_term AS term, MAX(search_term) AS search_term, COUNT(*) AS total, MAX(created_at) AS last_searched_at FROM search_queries WHERE normalized_term <> "" GROUP BY normalized_term ORDER BY last_searched_at DESC LIMIT :limit');
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function relatedSearches(string $term, int $limit = 10): array
    {
        $term = $this->normalizeTerm($term);
        if ($term === '') {
            return $this->popularSearches($limit);
        }

        $tokens = $this->tokenize($term);
        $candidates = array_merge($this->popularSearches(25), $this->recentSearches(25));
        $matches = array();

        foreach ($candidates as $candidate) {
            $candidateTerm = isset($candidate['search_term']) ? (string) $candidate['search_term'] : '';
            if ($candidateTerm === '' || strcasecmp($candidateTerm, $term) === 0) {
                continue;
            }

            $candidateNeedle = function_exists('mb_strtolower') ? mb_strtolower($candidateTerm, 'UTF-8') : strtolower($candidateTerm);
            $score = 0;

            foreach ($tokens as $token) {
                if ($token !== '' && strpos($candidateNeedle, $token) !== false) {
                    $score++;
                }
            }

            if ($score > 0) {
                $candidate['score'] = $score;
                $matches[] = $candidate;
            }
        }

        usort($matches, static function (array $left, array $right): int {
            if (($left['score'] ?? 0) === ($right['score'] ?? 0)) {
                return ($right['total'] ?? 0) <=> ($left['total'] ?? 0);
            }

            return ($right['score'] ?? 0) <=> ($left['score'] ?? 0);
        });

        return array_slice($this->uniqueByTerm($matches), 0, max(1, min(25, $limit)));
    }

    public function suggestions(string $term, int $limit = 8): array
    {
        $term = $this->normalizeTerm($term);
        $limit = max(1, min(20, $limit));

        if ($term === '') {
            $terms = array_merge($this->popularSearches($limit), $this->recentSearches($limit));
            return $this->formatSuggestionTerms($this->uniqueByTerm($terms), $limit);
        }

        $needle = function_exists('mb_strtolower') ? mb_strtolower($term, 'UTF-8') : strtolower($term);
        $terms = array_merge($this->popularSearches(20), $this->recentSearches(20));
        $matches = array();

        foreach ($terms as $row) {
            $candidate = isset($row['search_term']) ? (string) $row['search_term'] : '';
            if ($candidate === '') {
                continue;
            }

            $candidateNeedle = function_exists('mb_strtolower') ? mb_strtolower($candidate, 'UTF-8') : strtolower($candidate);
            if (strpos($candidateNeedle, $needle) !== false) {
                $matches[] = $row;
            }
        }

        return $this->formatSuggestionTerms($this->uniqueByTerm($matches), $limit);
    }

    public function analyticsSummary(): array
    {
        $summary = $this->connection->query('SELECT COUNT(*) AS total_searches, COUNT(DISTINCT normalized_term) AS unique_terms, COALESCE(AVG(result_count), 0) AS average_results, SUM(CASE WHEN result_count = 0 THEN 1 ELSE 0 END) AS zero_results FROM search_queries')->fetch(PDO::FETCH_ASSOC);
        $weekly = $this->connection->query('SELECT COUNT(*) AS total FROM search_queries WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)')->fetch(PDO::FETCH_ASSOC);

        return array(
            'total_searches' => isset($summary['total_searches']) ? (int) $summary['total_searches'] : 0,
            'unique_terms' => isset($summary['unique_terms']) ? (int) $summary['unique_terms'] : 0,
            'average_results' => isset($summary['average_results']) ? round((float) $summary['average_results'], 1) : 0,
            'zero_results' => isset($summary['zero_results']) ? (int) $summary['zero_results'] : 0,
            'last_7_days' => isset($weekly['total']) ? (int) $weekly['total'] : 0,
        );
    }

    private function normalizeTerm(string $term): string
    {
        $term = trim($term);

        if ($term === '') {
            return '';
        }

        return $this->truncate($term, 255);
    }

    private function normalizeFilters(array $filters): array
    {
        $normalized = array();

        foreach ($filters as $key => $value) {
            if (is_scalar($value) || $value === null) {
                $normalized[$key] = $value;
            }
        }

        ksort($normalized);

        return $normalized;
    }

    private function tokenize(string $term): array
    {
        $term = function_exists('mb_strtolower') ? mb_strtolower($term, 'UTF-8') : strtolower($term);
        $parts = preg_split('/[^a-z0-9]+/i', $term) ?: array();

        return array_values(array_filter($parts, static function ($part): bool {
            return strlen((string) $part) >= 2;
        }));
    }

    private function uniqueByTerm(array $items): array
    {
        $seen = array();
        $unique = array();

        foreach ($items as $item) {
            $term = isset($item['search_term']) ? (string) $item['search_term'] : (isset($item['term']) ? (string) $item['term'] : '');
            $key = function_exists('mb_strtolower') ? mb_strtolower($term, 'UTF-8') : strtolower($term);

            if ($term === '' || isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $unique[] = $item;
        }

        return $unique;
    }

    private function formatSuggestionTerms(array $items, int $limit): array
    {
        $suggestions = array();

        foreach (array_slice($items, 0, $limit) as $item) {
            $term = isset($item['search_term']) ? (string) $item['search_term'] : (isset($item['term']) ? (string) $item['term'] : '');

            if ($term === '') {
                continue;
            }

            $suggestions[] = array(
                'label' => $term,
                'value' => $term,
                'kind' => 'query',
                'href' => url('/search?q=' . rawurlencode($term)),
                'count' => isset($item['total']) ? (int) $item['total'] : 0,
            );
        }

        return $suggestions;
    }

    private function truncate(string $value, int $length): string
    {
        return function_exists('mb_substr') ? mb_substr($value, 0, $length) : substr($value, 0, $length);
    }
}