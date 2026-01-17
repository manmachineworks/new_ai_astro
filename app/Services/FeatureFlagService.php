<?php

namespace App\Services;

class FeatureFlagService
{
    /**
     * Check if a feature is enabled.
     * Can be extended to check DB overrides.
     */
    public function isEnabled(string $key): bool
    {
        // 1. Check Config (Environment)
        $configValue = config("features.{$key}");

        // 2. Check DB Override (Optional, cached)
        // $override = Cache::get("feature_flag:{$key}");
        // if ($override !== null) return $override;

        return (bool) $configValue;
    }

    public function ensureEnabled(string $key)
    {
        if (!$this->isEnabled($key)) {
            abort(503, "Feature '{$key}' is currently disabled.");
        }
    }
}
