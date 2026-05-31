<?php
/**
 * Pagination Helper
 * 
 * Calculates page numbers and generates pagination HTML.
 */
class Pagination
{
    private int $currentPage;
    private int $totalItems;
    private int $perPage;
    private int $totalPages;
    private string $baseUrl;

    public function __construct(int $totalItems, int $currentPage = 1, int $perPage = 20, string $baseUrl = '')
    {
        $this->totalItems = max(0, $totalItems);
        $this->perPage = max(1, $perPage);
        $this->totalPages = (int) ceil($this->totalItems / $this->perPage);
        $this->currentPage = max(1, min($currentPage, $this->totalPages ?: 1));
        $this->baseUrl = $baseUrl ?: currentPath();
    }

    /** Get the SQL OFFSET value */
    public function offset(): int
    {
        return ($this->currentPage - 1) * $this->perPage;
    }

    /** Get the SQL LIMIT value */
    public function limit(): int
    {
        return $this->perPage;
    }

    /** Get total pages */
    public function totalPages(): int
    {
        return $this->totalPages;
    }

    /** Get current page */
    public function currentPage(): int
    {
        return $this->currentPage;
    }

    /** Check if there are more pages */
    public function hasPages(): bool
    {
        return $this->totalPages > 1;
    }

    /** Check if there is a previous page */
    public function hasPrev(): bool
    {
        return $this->currentPage > 1;
    }

    /** Check if there is a next page */
    public function hasNext(): bool
    {
        return $this->currentPage < $this->totalPages;
    }

    /**
     * Generate the page URL
     */
    private function pageUrl(int $page): string
    {
        $query = $_GET;
        $query['page'] = $page;
        return $this->baseUrl . '?' . http_build_query($query);
    }

    /**
     * Render pagination HTML
     */
    public function render(): string
    {
        if (!$this->hasPages()) return '';

        $html = '<nav class="pagination-wrapper" aria-label="Pagination">';
        $html .= '<ul class="pagination">';

        // Previous button
        if ($this->hasPrev()) {
            $html .= '<li class="pagination-item">';
            $html .= '<a href="' . $this->pageUrl($this->currentPage - 1) . '" class="pagination-link pagination-prev" aria-label="Previous page">';
            $html .= '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>';
            $html .= '</a></li>';
        } else {
            $html .= '<li class="pagination-item"><span class="pagination-link pagination-prev disabled" aria-disabled="true">';
            $html .= '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>';
            $html .= '</span></li>';
        }

        // Page numbers
        $pages = $this->getPageRange();
        foreach ($pages as $page) {
            if ($page === '...') {
                $html .= '<li class="pagination-item"><span class="pagination-link pagination-ellipsis">…</span></li>';
            } elseif ($page === $this->currentPage) {
                $html .= '<li class="pagination-item"><span class="pagination-link active" aria-current="page">' . $page . '</span></li>';
            } else {
                $html .= '<li class="pagination-item"><a href="' . $this->pageUrl($page) . '" class="pagination-link">' . $page . '</a></li>';
            }
        }

        // Next button
        if ($this->hasNext()) {
            $html .= '<li class="pagination-item">';
            $html .= '<a href="' . $this->pageUrl($this->currentPage + 1) . '" class="pagination-link pagination-next" aria-label="Next page">';
            $html .= '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>';
            $html .= '</a></li>';
        } else {
            $html .= '<li class="pagination-item"><span class="pagination-link pagination-next disabled" aria-disabled="true">';
            $html .= '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>';
            $html .= '</span></li>';
        }

        $html .= '</ul></nav>';
        return $html;
    }

    /**
     * Calculate the range of page numbers to display
     */
    private function getPageRange(): array
    {
        $totalPages = $this->totalPages;
        $current = $this->currentPage;

        if ($totalPages <= 7) {
            return range(1, $totalPages);
        }

        $pages = [];

        // Always show first page
        $pages[] = 1;

        if ($current > 3) {
            $pages[] = '...';
        }

        // Pages around current
        $start = max(2, $current - 1);
        $end = min($totalPages - 1, $current + 1);

        for ($i = $start; $i <= $end; $i++) {
            $pages[] = $i;
        }

        if ($current < $totalPages - 2) {
            $pages[] = '...';
        }

        // Always show last page
        $pages[] = $totalPages;

        return $pages;
    }
}
