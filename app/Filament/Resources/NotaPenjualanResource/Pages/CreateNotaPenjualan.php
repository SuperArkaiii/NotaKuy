<?php

namespace App\Filament\Resources\NotaPenjualanResource\Pages;

use App\Filament\Resources\NotaPenjualanResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Product;

class CreateNotaPenjualan extends CreateRecord
{
    protected static string $resource = NotaPenjualanResource::class;

    protected function afterCreate(): void
    {
        // Ambil semua items dari nota
        $items = $this->record->items;

        foreach ($items as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $product->decrement('stok', $item->quantity);
                $product->save(); // ini akan memicu event 'saving'
            }
        }
    }
}
