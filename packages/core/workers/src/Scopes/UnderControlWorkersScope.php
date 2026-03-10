<?php

namespace Core\Orders\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
class UnderControlWorkersScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (Request::is('dashboard*') && Auth::check()) {
            if(Auth::user()->hasRole('operator')){
                $builder->whereHas('leader.operator',function($operatorsQuery){
                    $operatorsQuery->where('id',Auth::id());
                });
           }else if(Auth::user()->hasRole(['driver','technical'])){
                $builder->whereHas('leader',function($operatorsQuery){
                    $operatorsQuery->where('id',Auth::id());
                });
           }
        }
    }
}
