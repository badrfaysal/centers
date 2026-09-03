<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    /**
     * Apply generic filters, search, and sorting.
     *
     * @param Builder $query
     * @param \Illuminate\Http\Request $request
     * @param array $searchableFields Fields to search using LIKE %...%
     * @param array $filterableFields Fields to match exactly
     * @return Builder
     */
    public function scopeFilterAndSort(Builder $query, $request, array $searchableFields = [], array $filterableFields = [])
    {
        // 1. Search (LIKE)
        if ($request->filled('search') && !empty($searchableFields)) {
            $search = $request->search;
            $query->where(function ($q) use ($search, $searchableFields) {
                foreach ($searchableFields as $field) {
                    if (str_contains($field, '.')) {
                        [$relation, $relationField] = explode('.', $field);
                        $q->orWhereHas($relation, function($q2) use ($relationField, $search) {
                            $q2->where($relationField, 'like', "%{$search}%");
                        });
                    } else {
                        $q->orWhere($field, 'like', "%{$search}%");
                    }
                }
            });
        }

        // 2. Exact Match Filters
        foreach ($filterableFields as $field) {
            // Check if request has the exact key (we handle dot notation replacement for exact if needed, but usually simple names)
            $reqKey = str_replace('.', '_', $field); // if form uses something else, but let's assume same name
            
            // if form field is named exactly like the DB column
            if ($request->filled($field) && $request->input($field) !== 'all') {
                $query->where($field, $request->input($field));
            }
        }

        // 3. Sorting
        if ($request->filled('sort_by')) {
            $direction = $request->input('sort_dir', 'desc');
            $query->orderBy($request->sort_by, $direction);
        } else {
            // Default sorting if no sorting is specified
            $query->latest();
        }

        return $query;
    }
}
