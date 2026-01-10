<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Product;
use App\Models\StockBatch;
use App\Models\StockEntry;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Milon\Barcode\BarcodeServiceProvider;

class StockController extends Controller
{

    public function index()
    {
        //
    }
    /**
     * Показать форму приёмки товара
     */
    public function create(Request $request, Merchant $merchant, Warehouse $warehouse)
    {
        // Получаем товары мерчанта с вариациями
        $products = Product::where('merchant_id', $merchant->id)->get();

        // Достаём временные данные для конкретного мерчанта и склада
        $sessionKey = "stock_preview_data_{$merchant->id}_{$warehouse->id}";

        // Если есть параметр from_preview - это возврат с превью
        if ($request->has('from_preview')) {
            $oldData = session($sessionKey, null);
        } else {
            // Обычный заход - очищаем старые данные
            session()->forget($sessionKey);
            $oldData = null;
        }

        return view('admin.stock.create', compact(
            'merchant', 'warehouse', 'products', 'oldData'
        ));
    }

    public function preview(Request $request, Merchant $merchant, Warehouse $warehouse)
    {
        // Валидация
        $data = $request->validate([
            'products' => 'required|array',
            'products.*.product_id' => [
                'required',
                function ($attribute, $value, $fail) use ($merchant) {
                    // Разрешаем 'new' или существующий продукт мерчанта
                    if ($value !== 'new') {
                        $exists = \App\Models\Product::where('merchant_id', $merchant->id)
                            ->where('id', $value)
                            ->exists();
                        if (!$exists) {
                            $fail("Selected product does not exist for this merchant.");
                        }
                    }
                }
            ],
            'products.*.new_product_name' => 'nullable|string|max:255',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        // Нормализуем данные
        foreach ($data['products'] as &$product) {
            // Если выбран новый продукт
            if ($product['product_id'] === 'new' && !empty($product['new_product_name'])) {
                $product['product_id'] = null; // ещё не существует в базе
            } else {
                // Если выбран существующий продукт, очистим поле нового
                $product['new_product_name'] = trim($product['new_product_name'] ?? null);
            }
        }
        unset($product);

        // Сохраняем во временную сессию
        session([
            "stock_preview_data_{$merchant->id}_{$warehouse->id}" => $data,
        ]);

        // Редирект на страницу превью
        return redirect()->route('dashboard.stock.showPreview', [$merchant, $warehouse]);
    }

    public function showPreview(Merchant $merchant, Warehouse $warehouse)
    {
        // Извлекаем данные из сессии
        $data = session("stock_preview_data_{$merchant->id}_{$warehouse->id}");


        // Если данных нет — редирект обратно
        if (!$data) {
            return redirect()
                ->route('dashboard.stock.create', [$merchant, $warehouse])
                ->with('warning', 'No preview data found. Please fill the form again.');
        }

        // Получаем все продукты мерчанта и формируем массив id => product
        $productsMap = $merchant->products->keyBy('id');

        return view('admin.stock.preview', compact('data', 'merchant', 'warehouse', 'productsMap'));
    }

    /**
     * Принять товары на склад
     */
    public function store(Request $request, Merchant $merchant, Warehouse $warehouse)
    {
        // Ключ сессии для текущей партии
        $sessionKey = "stock_preview_data_{$merchant->id}_{$warehouse->id}";
        $data = $request->session()->get($sessionKey);

        if (!$data || empty($data['products'])) {
            return redirect()
                ->route('dashboard.stock.create', [$merchant, $warehouse])
                ->with('error', 'No data to save.');
        }

        // Создаём партию в транзакции
        $stockBatch = DB::transaction(function () use ($data, $merchant, $warehouse) {

            // Создаём новую поставку (batch)
            $stockBatch = StockBatch::create([
                'uuid'         => Str::uuid(),
                'merchant_id'  => $merchant->id,
                'warehouse_id' => $warehouse->id,
                'batch_code'   => sprintf('%d%06d', $merchant->id, random_int(100000, 999999)),
                'received_at'  => now(),
                'notes'        => $data['notes'] ?? null,
            ]);

            // Перебираем товары и добавляем записи в entries
            foreach ($data['products'] as $productData) {

                if (!empty($productData['new_product_name'])) {
                    // Создаём новый продукт
                    $sku = sprintf('%d-%06d', $merchant->id, random_int(100000, 999999));
                    $barcode = sprintf('%d%06d', $merchant->id, random_int(100000, 999999));

                    $product = Product::create([
                        'merchant_id' => $merchant->id,
                        'name'        => $productData['new_product_name'],
                        'sku'         => $sku,
                        'barcode'     => $barcode,
                        'active'      => true,
                    ]);
                } else {
                    // Используем существующий
                    $product = Product::findOrFail($productData['product_id']);
                }

                // Создаём запись в stock_entries
                $stockBatch->entries()->create([
                    'uuid'               => Str::uuid(),
                    'product_id'         => $product->id,
                    'quantity'           => $productData['quantity'],
                    'remaining_quantity' => $productData['quantity'],
                    'purchase_price'     => isset($productData['purchase_price'])
                        ? (int) round($productData['purchase_price'] * 100)
                        : null,
                ]);
            }

            return $stockBatch;
        });

        // После успешного сохранения очищаем сессию
        $request->session()->forget($sessionKey);

        // Редирект на страницу с партией
        return redirect()
            ->route('dashboard.batch.receipt', $stockBatch)
            ->with('success', 'Товары успешно добавлены на склад');
    }



}
