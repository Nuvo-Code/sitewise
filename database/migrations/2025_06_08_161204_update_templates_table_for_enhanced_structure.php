<?php

use App\Models\Template;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Convert existing templates from old format to new format
        $templates = Template::all();

        foreach ($templates as $template) {
            if (! empty($template->structure) && is_array($template->structure)) {
                $newStructure = [];

                foreach ($template->structure as $key => $type) {
                    // Check if it's already in new format
                    if (is_array($type) && isset($type['key'], $type['type'])) {
                        $newStructure[] = $type;
                    } else {
                        // Convert from old format
                        $newStructure[] = [
                            'name' => ucwords(str_replace('_', ' ', $key)),
                            'key' => $key,
                            'type' => $type,
                            'required' => in_array($key, ['title', 'name']) ? true : false,
                            'description' => null,
                            'default_value' => null,
                            'options' => [],
                            'validation_rules' => [],
                        ];
                    }
                }

                $template->update(['structure' => $newStructure]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert back to old format if needed
        $templates = Template::all();

        foreach ($templates as $template) {
            if (! empty($template->structure) && is_array($template->structure)) {
                $oldStructure = [];

                foreach ($template->structure as $field) {
                    if (is_array($field) && isset($field['key'], $field['type'])) {
                        $oldStructure[$field['key']] = $field['type'];
                    }
                }

                $template->update(['structure' => $oldStructure]);
            }
        }
    }
};
