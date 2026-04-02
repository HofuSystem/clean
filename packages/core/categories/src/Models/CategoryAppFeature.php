<?php

namespace Core\Categories\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;

class CategoryAppFeature extends Model implements TranslatableContract
{
    use HasFactory, Translatable, SoftDeletes;

    protected $table = 'category_app_features';

    public $translatedAttributes = ['title'];

    protected $fillable = [
        'category_id',
        'section',
        'image',
        'value',
        'creator_id',
        'updater_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getItemDataAttribute()
    {
        $data = $this->toArray();
        $data['translations'] = [];
        foreach (config('translatable.locales') as $locale) {
            $data['translations'][$locale] = [
                'title' => $this->translate($locale)?->title ?? '',
            ];
        }
        return $data;
    }

    public function getItemsActionsAttribute()
    {
        $slug = 'category-app-features';
           $actions = '<div class ="d-flex justify-content-center">';
        if (auth('web')->check() and auth('web')->user()->can('dashboard.'.$slug.'.edit')) {
            $actions .= ' <button class="btn-operation edit-item mx-1" data-href="' . route('dashboard.'.$slug.'.edit', ['id' => $this->id]) . '"><i class="fas fa-pencil-alt"></i></button>';
        }
        if (auth('web')->check() and auth('web')->user()->can('dashboard.'.$slug.'.delete')) {
            $actions .= '<button class="btn-operation delete-item mx-1" data-href="' . route('dashboard.'.$slug.'.delete', ['id' => $this->id]) . '"> <i class="fas fa-trash"></i></button></td>';
        }

        $actions .= '</div>';
        return $actions;
    }
}
