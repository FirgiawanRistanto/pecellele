<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'payment_method' => 'required|string|max:50',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
            // 'items.*.notes' => 'nullable|string', // Removed validation for item-specific notes
        ]);

        try {
            $order = DB::transaction(function () use ($validatedData) {
                // 1. Fetch all menu items at once to be efficient
                $menuIds = collect($validatedData['items'])->pluck('id');
                $menus = Menu::whereIn('id', $menuIds)->lockForUpdate()->get()->keyBy('id');

                $total = 0;

                // 2. Validate stock and calculate total price
                foreach ($validatedData['items'] as $item) {
                    $menuItem = $menus->get($item['id']);

                    // Check if there is enough stock
                    if ($menuItem->stock < $item['quantity']) {
                        throw ValidationException::withMessages([
                            'stock' => 'Stok untuk ' . $menuItem->name . ' tidak mencukupi. Sisa stok: ' . $menuItem->stock,
                        ]);
                    }

                    $total += $menuItem->price * $item['quantity'];
                }

                // 3. Create the Order
                $order = Order::create([
                    'total' => $total,
                    'payment_method' => $validatedData['payment_method'],
                    'address' => $validatedData['address'],
                    'notes' => $validatedData['notes'] ?? null, // Save global order notes
                ]);

                // 4. Create OrderItems and decrement stock
                foreach ($validatedData['items'] as $item) {
                    $menuItem = $menus->get($item['id']);
                    
                    $order->items()->create([
                        'menu_id' => $menuItem->id,
                        'quantity' => $item['quantity'],
                        'price' => $menuItem->price, // Price at the time of purchase
                        'notes' => $item['notes'] ?? null, // Pass notes field
                    ]);

                    // Decrement stock
                    $menuItem->decrement('stock', $item['quantity']);
                }

                return $order;
            });

            return response()->json(['message' => 'Pesanan berhasil dibuat!', 'order_id' => $order->id], 201);

        } catch (ValidationException $e) {
            // Re-throw validation exception with a 422 status code
            return response()->json(['message' => $e->getMessage(), 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Catch any other exceptions
            return response()->json(['message' => 'Terjadi kesalahan saat memproses pesanan.', 'error' => $e->getMessage()], 500);
        }
    }    public function index()
    {
        // Fetch all orders with their associated order items and menu details
        $orders = Order::with('items.menu')->orderBy('created_at', 'desc')->get();

        // Format the data to be easily consumable by the frontend
        $formattedOrders = $orders->map(function($order) {
            return [
                'id' => $order->id,
                'total' => $order->total,
                'paymentMethod' => $order->payment_method,
                'address' => $order->address,
                'notes' => $order->notes, // Include the global order notes
                'date' => $order->created_at, // Use created_at for the date
                'items' => $order->items->map(function($item) {
                    return [
                        'name' => $item->menu->name,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'notes' => $item->notes,
                    ];
                })
            ];
        });

        return response()->json($formattedOrders);
    }
}
