<?php

namespace Core\Categories\DataResources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryAppFeaturesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'section' => $this->section,
            'image' => $this->image,
            'value' => $this->value,
            'title' => $this->title,
            'translations' => $this->getTranslationsArray(),
        ];
    }

    /**
     * Generate common translation array for dashboard.
     *
     * @return array
     */
    protected function getTranslationsArray()
    {
        $translations = [];
        foreach (config('translatable.locales') as $locale) {
            $translations[$locale] = [
                'title' => $this->translate($locale)?->title ?? '',
            ];
        }
        return $translations;
    }
}
