<?php

namespace Core\Categories\Services;

use Core\Comments\Services\CommentingService;
use Core\Categories\Models\Slider;
use Core\Categories\Models\SliderView;
use Core\Categories\DataResources\SlidersResource;

class SlidersService
{
    public function __construct(protected CommentingService $commentingService)
    {
    }

    public function selectable(string $key, string $value)
    {
        $selected = ['id'];
        if (!in_array($key, [])) {
            $selected[] = $key;
        }
        if (!in_array($value, [])) {
            $selected[] = $value;
        }
        return Slider::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [], $id = null)
    {
        $recordData = array_filter(
            $data,
            fn($key) => in_array($key, ['image_en', 'image_ar', 'category_id', 'link', 'type', 'status', 'city_id', 'translations']),
            ARRAY_FILTER_USE_KEY
        );

        // The SliderObserver::saved() will handle SliderView UUID creation/refresh
        $record = Slider::updateOrCreate(['id' => $id], $recordData);

        return $record;
    }

    public function get(int $id)
    {
        return Slider::findOrFail($id);
    }

    public function getSliderView(int $sliderId): ?SliderView
    {
        return SliderView::where('slider_id', $sliderId)->first();
    }

    /**
     * Increment the view count for the given UUID and return the destination URL.
     * Returns null if the UUID is not found.
     */
    public function redirect(string $uuid): ?string
    {
        $sliderView = SliderView::where('uuid', $uuid)->firstOrFail();
        $sliderView->increment('views_count');
        return $sliderView->url;
    }

    public function delete(int $id, $final = false)
    {
        $record = Slider::findOrFail($id);
        if ($final) {
            $record->forceDelete();
        } else {
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw)
    {
        $recordsTotal = Slider::count();
        $recordsFiltered = Slider::search()->count();
        $records = Slider::select(['id', 'category_id', 'type', 'status', 'city_id', 'link', 'image_en', 'image_ar'])
            ->with(['category', 'city'])
            ->search()->dataTable()->get();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => SlidersResource::collection($records),
        ];
    }

    public function order(array $list, $orderBy = 'order')
    {
        foreach ($list as $value) {
            Slider::find($value['id'])->update([$orderBy => $value['order']]);
        }
    }

    public function import(array $items)
    {
        foreach ($items as $index => $item) {
            $items[$index] = $this->storeOrUpdate($item, $item['id'] ?? null);
        }
        return $items;
    }

    public function comment(int $id, string $content, int|null $parent_id)
    {
        return $this->commentingService->comment(
            Slider::class,
            $id,
            $content,
            request()->user()->id,
            $parent_id
        );
    }

    public function totalCount()
    {
        return Slider::count();
    }

    public function trashCount()
    {
        return Slider::onlyTrashed()->count();
    }

    public function restore(int $id)
    {
        $record = Slider::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
