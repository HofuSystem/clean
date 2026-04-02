<?php

namespace Core\Categories\Services;

use Core\Categories\Models\CategoryAppFeature;

class CategoryAppFeaturesService
{
    /**
     * Store or update the record.
     *
     * @param array $data
     * @param int|null $id
     * @return \Core\Categories\Models\CategoryAppFeature
     */
    public function storeOrUpdate(array $data = [], $id = null)
    {
        $recordData = array_filter($data, fn($key) => in_array($key, ['category_id', 'section', 'image', 'value', 'translations']), ARRAY_FILTER_USE_KEY);
        
        $record = CategoryAppFeature::updateOrCreate(['id' => $id], $recordData);
        
        return $record;
    }

    /**
     * Get the record by its ID.
     *
     * @param int $id
     * @return \Core\Categories\Models\CategoryAppFeature
     */
    public function get(int $id)
    {
        return CategoryAppFeature::findOrFail($id);
    }

    /**
     * Delete the record.
     *
     * @param int $id
     * @param bool $final
     * @return bool
     */
    public function delete(int $id, $final = false)
    {
        $record = CategoryAppFeature::findOrFail($id);
        if ($final) {
            $record->forceDelete();
        } else {
            $record->delete();
        }
        return true;
    }
}
