<?php

namespace Core\Settings\Models;

use Core\Comments\Models\Comment;
use Core\Entities\Helpers\SearchQueryBuilder;
use Core\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;


class CoreModel extends Model
{
    use HasFactory,SoftDeletes;

    public function scopeSearch($query) {}
    public function scopeActive($query) {
        $query->where('status', 'active');
    }
    public function scopeIsActive($query) {
        $query->where('status', 1);
    }
    public function scopeDataTable($query): void
    {
        if (request()->has('start') and request()->has('length') and request()->input('length') != -1) {
            $query->skip(request()->input('start'))->take(request()->input('length'));
        }
        $orderColumn = request()->columns[request()->order[0]['column']]['data'] ?? null;
        if (isset($orderColumn) ) {
            $orderBy    = $orderColumn;
            $orderDir   = request()->order[0]['dir'];
            if (isset($this->translatedAttributes) and in_array($orderBy, $this->translatedAttributes)) {
                $query->orderByTranslation($orderBy, $orderDir);
            } else {
                $query->orderBy($orderBy, $orderDir);
            }
        }
       
    }
    public function scopeUnderMyControl($query)
    {
    }
    public function comments(){
        return $this->morphMany(Comment::class, 'comment_for');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    //start Attributes

    public function getActions($slug)
    {
        $actions = '<div class ="d-flex justify-content-center">';
        if(request()->has('trash') and request()->trash == 1){
            if (auth('web')->check() and auth('web')->user()->can('dashboard.'.$slug.'.restore')) {
                $actions .= '<a class="btn-operation d-flex justify-content-center align-items-center mx-1 btn-restore" href="' . route('dashboard.'.$slug.'.restore', ['id' => $this->id]) . '">
                    <i class="fa fa-trash-restore"></i> <span>' . trans('restore') . '</span>
                    </a>';
            }
            $actions .= '</div>';
            return $actions;
        }
        if (auth('web')->check() and auth('web')->user()->can('dashboard.'.$slug.'.show')) {
            $actions .= '<a class="btn-operation d-flex justify-content-center align-items-center mx-1 " href="' . route('dashboard.'.$slug.'.show', ['id' => $this->id]) . '"> 
                <i class="fa fa-eye"></i> <span>' . trans('show') . '</span>
                </a>';
        }
        if (auth('web')->check() and auth('web')->user()->can('dashboard.'.$slug.'.edit')) {
            $actions .= '<a class="btn-operation d-flex justify-content-center align-items-center mx-1 " href="' . route('dashboard.'.$slug.'.edit', ['id' => $this->id]) . '"> 
                <i class="fa fa-edit"></i> <span>' . trans('edit') . '</span>
                </a>';
        }
        if (auth('web')->check() and auth('web')->user()->can('dashboard.'.$slug.'.delete')) {
            $actions .= '<a class="btn-operation d-flex justify-content-center align-items-center mx-1 delete-btn" href="' . route('dashboard.'.$slug.'.delete', ['id' => $this->id]) . '"> 
                <i class="fa fa-trash"></i><span> ' . trans('delete') . ' </span>
                </a>';
        }

        $actions .= '</div>';
        return $actions;
    }

    public function getItemsActions($slug)
    {

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
    public function getShowActions($slug)
    {
        $actions = '<div class ="d-flex justify-content-center">';
        if (auth('web')->check() and auth('web')->user()->can('dashboard.'.$slug.'.show')) {
            $actions .= '<a class="btn-operation d-flex justify-content-center align-items-center mx-1 " href="' . route('dashboard.'.$slug.'.show', ['id' => $this->id]) . '"> 
                <i class="fa fa-eye"></i> <span>' . trans('show') . '</span>
                </a>';
        }
    

        $actions .= '</div>';
        return $actions;
    }
    public function getItemData($slug)
    {
        $data                    = method_exists($this, 'translations')  ? $this->load('translations')->toArray()  :$this->toArray();
        $data['translations']    = collect($data['translations'] ?? [] )->keyBy('locale')->toArray();
        $data['deleteUrl']       = route('dashboard.'.$slug.'.delete',$this->id); 
        $data['updateUrl']       = route('dashboard.'.$slug.'.edit',$this->id);
        return $data;
    }
    public function getSelectSwitchAttribute()
    {
        return
            '
                <div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" name="table_selected" value="' . $this->id . '" />
                </div>
            ';
    }
   
    //end Attributes
}
