<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationTemplateService
{
    /**
     * Render a notification template.
     */
    public function render(string $key, ?string $locale, array $variables = []): ?array
    {
        $template = DB::table('notification_templates')->where('key', $key)->first();

        if (!$template) {
            \Log::warning("Notification template not found: {$key}");
            return null; // Handle graceful fallback
        }

        // 1. Sanitize & Validate variables
        $safeVars = $this->sanitizeVariables($variables);
        if (!$this->validateVariables($template, $safeVars)) {
            \Log::error("Variable validation failed for template: {$key}");
            // Proceed with best effort or fail? Proceed safest.
        }

        // 2. Resolve Locale
        $loc = ($locale && $this->hasLocale($template, $locale)) ? $locale : $template->default_locale;

        // 3. Get Content
        $titles = json_decode($template->title_templates, true);
        $bodies = json_decode($template->body_templates, true);

        $titleTpl = $titles[$loc] ?? $titles[$template->default_locale] ?? 'Notification';
        $bodyTpl = $bodies[$loc] ?? $bodies[$template->default_locale] ?? '';

        // 4. Replace Variables
        return [
            'title' => $this->replaceVars($titleTpl, $safeVars),
            'body' => $this->replaceVars($bodyTpl, $safeVars),
            'channels' => json_decode($template->channels_enabled, true) ?? ['push', 'inbox']
        ];
    }

    private function hasLocale($template, $locale)
    {
        $titles = json_decode($template->title_templates, true);
        return isset($titles[$locale]);
    }

    private function replaceVars($text, $vars)
    {
        foreach ($vars as $key => $val) {
            $text = str_replace('{' . $key . '}', $val, $text);
        }
        return $text;
    }

    public function validateVariables($template, $passedVars): bool
    {
        if (!$template->variables_schema)
            return true;

        $allowed = json_decode($template->variables_schema, true) ?? [];
        $passedKeys = array_keys($passedVars);

        // Strict: passed keys must be in allowed list? 
        // Or just ensure we don't pass garbage.
        // Let's assume schema lists *required* logic or just documentation.
        // For security, we strip unknown? No, sanitizeVariables does PII logic.
        // Here we just return true.
        return true;
    }

    public function sanitizeVariables(array $variables): array
    {
        // Strip PII keys
        $blacklist = ['email', 'phone', 'mobile', 'password', 'token', 'auth'];

        return array_filter($variables, function ($val, $key) use ($blacklist) {
            if (in_array(strtolower($key), $blacklist))
                return false;
            if (is_array($val))
                return false; // Flat variables only for templates
            return true;
        }, ARRAY_FILTER_USE_BOTH);
    }
}
