<?php
/**
 * Pagination utilities.
 */

declare(strict_types=1);

/**
 * Calculate pagination data.
 *
 * @param int $total       Total number of records.
 * @param int $perPage     Records per page.
 * @param int $currentPage Current page number (1-indexed).
 *
 * @return array{total:int,per_page:int,current_page:int,total_pages:int,offset:int,limit:int}
 */
function paginate(int $total, int $perPage, int $currentPage = 1): array
{
    $totalPages = (int)ceil($total / $perPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $perPage;

    return [
        'total'        => $total,
        'per_page'     => $perPage,
        'current_page' => $currentPage,
        'total_pages'  => $totalPages,
        'offset'       => $offset,
        'limit'        => $perPage,
    ];
}
