<?php

use App\Services\PolicyEngine;

if (! function_exists('policy_value')) {
    /**
     * Get an evaluated policy value.
     *
     * @param  string  $key  The policy rule key.
     * @param  mixed  $default  Value to return when no matching rule exists.
     * @param  array  $context  Contextual attributes used to match conditional rules.
     * @param  string|null  $category  Optional policy category filter.
     * @return mixed
     */
    function policy_value(string $key, $default = null, array $context = [], ?string $category = null)
    {
        return app(PolicyEngine::class)->get($key, $context, $default, $category);
    }
}

if (! function_exists('policy_all')) {
    /**
     * Get all evaluated values for a given policy category.
     */
    function policy_all(string $category, array $context = []): array
    {
        return app(PolicyEngine::class)->getAll($category, $context);
    }
}

if (! function_exists('policy_evaluate')) {
    /**
     * Evaluate all rules of a single policy by code.
     */
    function policy_evaluate(string $code, array $context = []): array
    {
        return app(PolicyEngine::class)->evaluatePolicy($code, $context);
    }
}
