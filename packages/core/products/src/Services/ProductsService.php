<?php

namespace Core\Products\Services;

use Core\Comments\Services\CommentingService;
use Core\Products\Models\Product;
use Core\Products\DataResources\ProductsResource;
use Core\Categories\Services\PricesService;
use Core\Products\DataResources\ProductCardResource;
use Core\Settings\Helpers\ToolHelper;
use Core\B2B\Models\Contract;
use Illuminate\Support\Facades\DB;

class ProductsService
{
    protected static $currentContract;
    public function __construct(protected CommentingService $commentingService,protected PricesService $pricesService){}
    function getProductsCard($type = null,$user = null,$company = null,$b2b_type = null)
    {
        $cityId = $user?->profile?->city_id ?? null;
        $orderType = $type ? \Core\Orders\Helpers\OrderHelper::getOrderType($type) : null;
        $locale = app()->getLocale();

        $query = DB::table('products')
            ->join('product_translations', function ($join) use ($locale) {
                $join->on('products.id', '=', 'product_translations.product_id')
                    ->where('product_translations.locale', '=', $locale);
            })
            ->leftJoin('categories as cat', 'products.category_id', '=', 'cat.id')
            ->leftJoin('category_translations as cat_trans', function ($join) use ($locale) {
                $join->on('cat.id', '=', 'cat_trans.category_id')
                    ->where('cat_trans.locale', '=', $locale);
            })
            ->leftJoin('categories as sub_cat', 'products.sub_category_id', '=', 'sub_cat.id')
            ->leftJoin('category_translations as sub_cat_trans', function ($join) use ($locale) {
                $join->on('sub_cat.id', '=', 'sub_cat_trans.category_id')
                    ->where('sub_cat_trans.locale', '=', $locale);
            })
            ->where('products.status', 'active')
            ->whereNull('products.deleted_at')
            ->when($orderType, function ($q) use ($orderType) {
                $q->where('cat.type', $orderType);
            })
            ->select(
                'products.id',
                'products.sku',
                'products.image',
                'product_translations.name',
                'products.price',
                'products.cost',
                'products.points',
                'products.type',
                'cat_trans.name as category',
                'products.category_id',
                'sub_cat_trans.name as sub_category',
                'products.sub_category_id'
            );

        $products = $query->get();

        return $products->map(function ($p) {
            return [
                'id'              => $p->id,
                'sku'             => $p->sku,
                'image'           => \Core\MediaCenter\Helpers\MediaCenterHelper::getImagesUrl($p->image),
                'name'            => $p->name,
                'price'           => (double) $p->price,
                'points'          => (double) $p->points,
                'cost'            => (double) $p->cost,
                'type'            => $p->type,
                'category'        => $p->category,
                'category_id'     => $p->category_id,
                'sub_category'    => $p->sub_category,
                'sub_category_id' => $p->sub_category_id,
                'in_contract'     => 0,
            ];
        });
    }
    public function selectable(string $key,string $value,array $selected = [],$with = []){
        $selected[] = 'id';
        if(!in_array($key,["name","desc"])){
            $selected[] = $key;
        }
        if(!in_array($value,["name","desc"])){
            $selected[] = $value;
        }
        $with = array_unique(array_merge($with, ['translations']));
        return Product::select($selected)->with($with)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['image','type','sku','is_package','category_id','sub_category_id','price','cost','quantity','status','translations', 'wash_type', 'display_as', 'points']),ARRAY_FILTER_USE_KEY);
        $record     = Product::updateOrCreate(['id' => $id],$recordData);

        if(!isset($id)){
            //saving on create the related pricesItems
            $pricesItems            = $data['prices'] ?? [];
            foreach ($pricesItems as $index => $itemValues) {
                $itemValues['priceable_id']     = $record->id;
                $itemValues['priceable_type']   = Product::class;
                $this->pricesService->storeOrUpdate($itemValues,$itemValues['id'] ?? null);
            }
        }

        return $record;
    }

    public function get(int|string $id){
        return  Product::findOrFail($id);
    }

    public function delete(int|string $id,$final = false){
        $record             = Product::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw, $type = null){

        $recordsTotal       = Product::when($type, function($q) use ($type) { return $type === 'non-sales' ? $q->where('type', '!=', 'sales') : $q->where('type', $type); })->count();
        $recordsFiltered    = Product::when($type, function($q) use ($type) { return $type === 'non-sales' ? $q->where('type', '!=', 'sales') : $q->where('type', $type); })->search()->count();
        $records            = Product::with([
            'translations',
            'category.translations',
            'subCategory.translations'
        ])->when($type, function($q) use ($type) { return $type === 'non-sales' ? $q->where('type', '!=', 'sales') : $q->where('type', $type); })->search()->dataTable()->get();
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => ProductsResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            Product::find($value['id'])->update([$orderBy=>$value['order']]);
        }
    }
    public function import(array $items){
        foreach ($items as  $index => $item) {
            $items[$index] = $this->storeOrUpdate($item,$item['id'] ?? null);
        }
        return $items;
    }
    public function comment(int|string $id,string $content,int | null $parent_id){
       return $this->commentingService->comment(
         Product::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
     }
    public function totalCount($type = null){
        return Product::when($type, function($q) use ($type) { return $type === 'non-sales' ? $q->where('type', '!=', 'sales') : $q->where('type', $type); })->count();
    }
    public function trashCount($type = null){
        return Product::onlyTrashed()->when($type, function($q) use ($type) { return $type === 'non-sales' ? $q->where('type', '!=', 'sales') : $q->where('type', $type); })->count();
    }
    public function restore(int|string $id){
        $record = Product::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
    public function bestSales($request){
        $to = now()->format('Y-m-d');
        if ($request->to) {
            $to = $request->to;
        }
        //dd($to,$request->from);
        $query = Product::when($request->from, function ($q) use ($request, $to) {
            return $q->whereBetween('orders.created_at', [$request->from, $to]);
        })  ->where('products.status','active')
            ->where('orders.status','finished')
            ->select('products.id', 'products.name', DB::raw('SUM(order_items.quantity) as total_quantity'))
            ->join('order_items', 'order_items.product_id', '=', 'products.id')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc(DB::raw('SUM(order_items.quantity)'));

        $top_sales_products = $query->whereIn('products.category_id',['10','11','12'])->take(12)->get();

        $top_sales_package_products = $query->whereIn('products.category_id',['13'])->where('is_package',true)->take(12)->get();

  
        return view('dashboard.best_sales',['top_sales_products'=>$top_sales_products,'top_sales_package_products'=>$top_sales_package_products]);
    }
    public static function setCurrentContract($company){
        if(!$company){
            return null;
        }
        $companyContract = Contract::with(['contractPrices','contractCustomerPrices'])
        ->where('company_id',$company->id)
        ->currentActive()
        ->first();
        if($companyContract){
            self::$currentContract = $companyContract;
        }
        return self::$currentContract;
    }
    public static function getCurrentContract(){
        return self::$currentContract;
    }
    public static function getProductData($company,$b2bType,$cityId,$product){
        $price      = $product->price;
        $cost       = $product->cost;
        if(self::$currentContract){
            if($b2bType == 'company'){
                $contractPrice = self::$currentContract->contractPrices->where('product_id',$product->id)->first();
                if($contractPrice){
                    $price = $contractPrice->price;
                    $cost = $contractPrice->cost;
                }
            }else{
                $contractCustomerPrice = self::$currentContract->contractCustomerPrices->where('product_id',$product->id)->first();
                if($contractCustomerPrice){
                    $price  +=  $contractCustomerPrice->over_price;
                    $cost   += $contractCustomerPrice->over_price;
                }
            }
        }else{
            $outOfContractPriceData = self::getProductOutOfContractPriceData($product,$cityId);
            if($outOfContractPriceData){
                $price = $outOfContractPriceData['price'];
                $cost = $outOfContractPriceData['cost'];
            }
        }
        return [
            'price' => $price,
            'cost' => $cost,
        ];
    }
    public static function getProductOutOfContractPriceData($product,$cityId){
        $price = $product->price ?? 0;
        $cost = $product->cost ?? 0;
        $priceCityData = null;
        if (!($product instanceof \stdClass) && isset($product->prices)) {
             $priceCityData = $product->prices->where('city_id',$cityId)->first();
        }
        if($priceCityData){
            $price = $priceCityData->price;
            $cost = $priceCityData->cost;
        }
        return [
            'price' => ToolHelper::getPriceBasedOnCurrentWeekDay($price),
            'cost' => $cost,
        ];
    }

    /**
     * Convert product images to WebP format, and compress sales product images.
     *
     * @param int $quality
     * @return array
     */
    public function convertProductImagesToWebp($quality = 60)
    {
        $products = Product::whereNotNull('image')->where('image', '!=', '')->get();
        $convertedCount = 0;
        $failedCount = 0;
        $errors = [];

        foreach ($products as $product) {
            try {
                $imagePath = $product->image;
                $isUrl = str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://');
                $tempDownloadedPath = null;
                $localSourcePath = null;

                if ($isUrl) {
                    $uploadedFile = \Core\MediaCenter\Helpers\MediaCenterHelper::downloadFile($imagePath);
                    if ($uploadedFile) {
                        $localSourcePath = $uploadedFile->getPathname();
                        $tempDownloadedPath = $localSourcePath;
                    } else {
                        $failedCount++;
                        $errors[] = "Failed to download image URL for Product ID {$product->id}: {$imagePath}";
                        continue;
                    }
                } else {
                    $cleanRelativePath = preg_replace('#^(storage/|public/)?#', '', ltrim($imagePath, '/'));
                    $possiblePaths = [
                        \Illuminate\Support\Facades\Storage::disk('public')->path($cleanRelativePath),
                        storage_path('app/public/' . $cleanRelativePath),
                        public_path('storage/' . $cleanRelativePath),
                        public_path($cleanRelativePath),
                        base_path($imagePath),
                    ];

                    foreach ($possiblePaths as $p) {
                        if (file_exists($p) && !is_dir($p)) {
                            $localSourcePath = $p;
                            break;
                        }
                    }

                    if (!$localSourcePath) {
                        $failedCount++;
                        $errors[] = "Image file not found for Product ID {$product->id}: {$imagePath}";
                        continue;
                    }
                }

                // Make Intervention Image
                $img = \Intervention\Image\Facades\Image::make($localSourcePath);

                // Compress if sales product, or convert to WebP
                if ($product->type === 'sales') {
                    $compressedImg = \Core\MediaCenter\Helpers\MediaCenterHelper::compressImage($img, 'webp', $quality);
                    $encodedData = (string) $compressedImg->encode('webp', $quality);
                } else {
                    $encodedData = (string) $img->encode('webp', 80);
                }

                // Determine relative directory and new WebP filename
                if ($isUrl) {
                    $dir = 'images';
                    $newFileName = \Illuminate\Support\Str::random(40) . '.webp';
                } else {
                    $cleanRelativePath = preg_replace('#^(storage/|public/)?#', '', ltrim($imagePath, '/'));
                    $info = pathinfo($cleanRelativePath);
                    $dir = ($info['dirname'] !== '.' && !empty($info['dirname'])) ? $info['dirname'] : 'images';
                    $newFileName = $info['filename'] . '.webp';
                }

                $newRelativePath = trim($dir, '/') . '/' . $newFileName;

                // Save WebP file via Storage facade (automatically handles directory creation & stream writing)
                \Illuminate\Support\Facades\Storage::disk('public')->put($newRelativePath, $encodedData);

                $targetAbsolutePath = \Illuminate\Support\Facades\Storage::disk('public')->path($newRelativePath);

                // Clean up old file if different path
                if (!$isUrl && $localSourcePath && $localSourcePath !== $targetAbsolutePath && file_exists($localSourcePath)) {
                    $oldExt = strtolower(pathinfo($localSourcePath, PATHINFO_EXTENSION));
                    if ($oldExt !== 'webp') {
                        @unlink($localSourcePath);
                    }
                }

                if ($tempDownloadedPath && file_exists($tempDownloadedPath)) {
                    @unlink($tempDownloadedPath);
                }

                // Update DB record
                $product->image = $newRelativePath;
                $product->save();

                $convertedCount++;
            } catch (\Throwable $e) {
                $failedCount++;
                $errors[] = "Error processing Product ID {$product->id}: " . $e->getMessage();
            }
        }

        return [
            'total' => $products->count(),
            'converted' => $convertedCount,
            'failed' => $failedCount,
            'errors' => $errors,
        ];
    }
}
