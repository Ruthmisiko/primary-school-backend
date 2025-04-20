<?php
namespace App\Imports;

use App\Models\MeasureUnit;
use App\Models\Product;
use Illuminate\Support\Str;
use App\Models\ProductCategory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProductsImport implements ToCollection
{
    protected $currentProducts = null;

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // Skip header row

            try {
                // Extract all data elements
                $name  = $row[0] ?? null;
                $category   = $row[1] ?? null;
                $measure_unit       = $row[2] ?? null;
                $purchase_price     = $row[3] ?? null;
                $sales_price    = $row[4] ?? null;
                $description    = $row[5] ?? null;
                $size    = $row[6] ?? null;

                $this->processProductData(
                    $index,
                    $name,
                    $category,
                    $measure_unit,
                    $purchase_price,
                    $sales_price,
                    $description,
                    $size
                );


            } catch (\Exception $e) {
                Log::error("Error processing row {$index}: " . $e->getMessage());
            }
        }
    }

    private function processProductData($rowIndex, $name, $category, $measure_unit, $purchase_price, $sales_price, $description, $size)
    {
        // Validate required fields
        if (empty($name) || empty($category) || empty($measure_unit)) {
            throw new \Exception("Missing required product fields");
        }
        $latestProduct = Product::orderBy('code', 'desc')->first();
        $nextCode = $latestProduct ? sprintf("%04d", (intval($latestProduct->code) + 1)) : "0001";

        $sku = strtoupper(Str::random(8));

        $category = ProductCategory::where('name', $category)->first();
        $measure_unit = MeasureUnit::where('name', $measure_unit)->first();

        $this->currentProducts = Product::updateOrCreate(
            [
                'name' => $name
            ],
            [
                'category_id' => $category ? $category->id : null,
                'measure_umit_id' => $measure_unit ? $measure_unit->id : null,
                'purchase_price' => $purchase_price,
                'sale_price' => $sales_price,
                'description' => $description,
                'size' => $size,
                'code' => $nextCode,
                'sku' => $sku,
            ]
        );

    }


}