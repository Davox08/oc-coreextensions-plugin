<?php namespace Davox\CoreExtensions\Classes;

use Illuminate\Support\Facades\DB;

class ThemeOptions
{
    private static function getThemeDefaults()
    {
        $defaults = [
            'primary' => '#3b82f6',
            'secondary' => '#ec4899',
            'accent' => '#8b5cf6',
            'neutral' => '#64748b',
            'info' => '#0ea5e9',
            'success' => '#10b981',
            'warning' => '#f59e0b',
            'error' => '#ef4444',
            'base_100' => '#ffffff',
            'base_200' => '#f8fafc',
            'base_300' => '#f1f5f9',
            'black' => '#000000',
            'white' => '#ffffff',
            'transparent' => 'transparent',
        ];

        try {
            $themeDataJson = DB::table('cms_theme_data')->where('theme', 'master')->value('data');
            if ($themeDataJson) {
                $themeData = json_decode($themeDataJson, true);
                if (is_array($themeData)) {
                    $defaults = array_merge($defaults, $themeData);
                }
            }
        } catch (\Exception $e) {}

        return $defaults;
    }

    private static function formatOption($label, $color)
    {
        return [
            // Proveemos todas las posibles llaves para asegurarnos de que October CMS lo lea bien
            0 => $label,
            'label' => $label,
            'title' => $label,
            'name' => $label,
            'color' => $color
        ];
    }

    public static function getThemeColors()
    {
        $defaults = self::getThemeDefaults();
        
        $keys = [
            'base_100' => 'Base 100',
            'base_200' => 'Base 200',
            'base_300' => 'Base 300',
            'primary' => 'Primary',
            'secondary' => 'Secondary',
            'accent' => 'Accent',
            'neutral' => 'Neutral',
            'info' => 'Info',
            'success' => 'Success',
            'warning' => 'Warning',
            'error' => 'Error',
            'black' => 'Black (Negro)',
            'white' => 'White (Blanco)',
            'transparent' => 'Transparent (Transparente)',
        ];

        $options = [];
        foreach ($keys as $key => $label) {
            $options[$key] = self::formatOption($label, $defaults[$key] ?? '');
        }

        return $options;
    }

    public static function getTextColors()
    {
        $defaults = self::getThemeDefaults();
        $keys = [
            'base_content' => 'Base Content',
            'base_100' => 'Base 100',
            'primary' => 'Primary',
            'secondary' => 'Secondary',
            'accent' => 'Accent',
            'neutral' => 'Neutral',
            'info' => 'Info',
            'success' => 'Success',
            'warning' => 'Warning',
            'error' => 'Error',
            'transparent' => 'Transparent',
            'inherit' => 'Inherit',
        ];

        $options = [];
        foreach ($keys as $key => $label) {
            $color = $defaults[$key] ?? '';
            if ($key === 'transparent') $color = 'transparent';
            if ($key === 'inherit') $color = 'inherit';
            
            $dashKey = str_replace('_', '-', $key);
            $options["text-{$dashKey}"] = self::formatOption($label, $color);
        }

        return $options;
    }

    public static function getButtonColors()
    {
        $defaults = self::getThemeDefaults();
        $keys = [
            'base_100' => 'Base 100',
            'base_200' => 'Base 200',
            'base_300' => 'Base 300',
            'primary' => 'Primary',
            'secondary' => 'Secondary',
            'accent' => 'Accent',
            'neutral' => 'Neutral',
            'info' => 'Info',
            'success' => 'Success',
            'warning' => 'Warning',
            'error' => 'Error',
            'black' => 'Black',
        ];

        $options = [];
        foreach ($keys as $key => $label) {
            $color = $defaults[$key] ?? '';
            $dashKey = str_replace('_', '-', $key);
            
            if ($key === 'base_100') {
                $options["bg-{$dashKey} text-base-content hover:bg-base-200"] = self::formatOption("{$label} (Solid)", $color);
            } elseif ($key === 'base_200') {
                $options["bg-{$dashKey} text-base-content hover:bg-base-300"] = self::formatOption("{$label} (Solid)", $color);
            } elseif ($key === 'base_300') {
                $options["bg-{$dashKey} text-base-content hover:bg-base-300"] = self::formatOption("{$label} (Solid)", $color);
            } elseif (in_array($key, ['neutral', 'black'])) {
                $textColor = $key === 'black' ? 'text-white' : "text-{$dashKey}-content";
                $options["bg-{$dashKey} {$textColor} hover:opacity-80"] = self::formatOption("{$label} (Solid)", $color);
            } else {
                $options["bg-{$dashKey} text-{$dashKey}-content hover:opacity-80"] = self::formatOption("{$label} (Solid)", $color);
                $options["bg-{$dashKey}/10 text-{$dashKey} hover:bg-{$dashKey} hover:text-{$dashKey}-content"] = self::formatOption("{$label} (Transparent)", $color);
            }
        }

        return $options;
    }

    public static function getThemeFonts()
    {
        // Placeholder for future Davox.GFonts integration.
        // For now, we return standard keys that the theme css will map to.
        return [
            'font-primary' => 'Primary Font',
            'font-secondary' => 'Secondary Font',
            'font-tertiary' => 'Tertiary Font',
            'font-sans' => 'Sans-serif',
            'font-serif' => 'Serif',
            'font-mono' => 'Monospace (Code)',
            'font-handwrite' => 'Handwrite / Special',
        ];
    }

    public static function getTextCaseModes()
    {
        return [
            'normal-case' => 'Normal / Sentence case',
            'uppercase' => 'MAYÚSCULAS',
            'lowercase' => 'minúsculas',
            'capitalize' => 'Capitalizar Cada Palabra',
        ];
    }

    public static function getAvailableMenus()
    {
        $options = [];
        try {
            $menus = \Tailor\Models\EntryRecord::inSection('Site\Menus')->get();
            
            if ($menus) {
                foreach ($menus as $menu) {
                    $options[$menu->id] = $menu->title ?? 'Menu ' . $menu->id;
                }
            }
        } catch (\Exception $e) {
            // Ignore exceptions
        }
        
        return $options ?: ['' => 'No menus available (Add them in Menus tab)'];
    }
    public static function getAvailableHeaders()
    {
        $options = ['' => 'Default Header (Global)'];
        try {
            $headers = \Tailor\Models\EntryRecord::inSection('Site\Headers')->get();
            if ($headers) {
                foreach ($headers as $header) {
                    $options[$header->id] = $header->title ?? 'Header ' . $header->id;
                }
            }
        } catch (\Exception $e) {}
        
        return $options;
    }

    public static function getAvailableLayouts()
    {
        $options = [];
        try {
            $theme = \Cms\Classes\Theme::getActiveTheme();
            if ($theme) {
                $layouts = \Cms\Classes\Layout::listInTheme($theme, true);
                foreach ($layouts as $layout) {
                    $options[$layout->getBaseFileName()] = $layout->name ?: $layout->getBaseFileName();
                }
            }
        } catch (\Exception $e) {}
        
        return $options ?: ['' => 'No layouts found'];
    }
}
