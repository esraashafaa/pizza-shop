<?php

namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\Pizza;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\OrderPizza;
use App\Models\Topping;
use App\Models\Status;
use Illuminate\Support\Facades\DB;

    class OrderController extends Controller
{
     /**
     * إنشاء طلب جديد (Order).
     *
     * @group Orders
     * @authenticated
     * 
     * @bodyParam customer_id int required رقم العميل. Example: 3
     * @bodyParam pizzas array required قائمة البيتزات. Example: [{"pizza_id": 1, "topping_ids": [1, 2]}]
     * @bodyParam pizzas[].pizza_id int required رقم البيتزا. Example: 1
     * @bodyParam pizzas[].topping_ids array قائمة التوبينغز. Example: [1, 2]
     *
     * @response 201 {
     *   "message": "Order created successfully.",
     *   "order": {
     *     "id": 1,
     *     "customer_id": 3,
     *     "status_id": 1,
     *     "total_price": 28.50,
     *     ...
     *   }
     * }
     */

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'pizzas' => 'required|array|min:1',
            'pizzas.*.pizza_id' => 'required|exists:pizzas,id',
            'pizzas.*.topping_ids' => 'array',
            'pizzas.*.topping_ids.*' => 'exists:toppings,id',
        ]);
    
        DB::beginTransaction();
    
        try {
            $status = Status::where('name', 'preparing')->firstOrFail();
    
            $order = Order::create([
                'customer_id' => $request->customer_id, 
                'status_id' => $status->id,
                'total_price' => 0, 
            ]);
    
            $totalPrice = 0;
    
            foreach ($request->pizzas as $pizzaData) {
                $pizza = Pizza::findOrFail($pizzaData['pizza_id']);
                $toppings = Topping::whereIn('id', $pizzaData['topping_ids'] ?? [])->get();
    
                $toppingTotal = $toppings->sum('price');
                $finalPrice = $pizza->price + $toppingTotal;
    
                $orderPizza = $order->pizzas()->create([
                    'pizza_id' => $pizza->id,
                    'price' => $finalPrice
                ]);
    
                $orderPizza->toppings()->attach($toppings->pluck('id'));
    
                $totalPrice += $finalPrice;
            }
    
            $order->update(['total_price' => $totalPrice]);
    
            DB::commit();
    
            return response()->json([
                'message' => 'Order created successfully.',
                'order' => $order->load('pizzas.toppings')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
 /**
     * عرض تفاصيل طلب معين.
     *
     * @group Orders
     * @urlParam id int رقم الطلب. Example: 1
     *
     * @response 200 {
     *   "order": {
     *     "id": 1,
     *     "customer": {...},
     *     "pizzas": [...],
     *     "status": {...}
     *   }
     * }
     * @response 404 {
     *   "message": "Order not found",
     *   "error": "Model not found"
     * }
     */
    /**
     * Store a newly created resource in storage.
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $order = Order::with('pizzas.toppings', 'customer', 'status')->findOrFail($id);
    
            return response()->json([
                'order' => $order
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Order not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }
     /**
     * تحديث حالة الطلب.
     *
     * @group Orders
     * @authenticated
     * 
     * @urlParam id int رقم الطلب. Example: 1
     * @bodyParam status_id int required رقم الحالة الجديدة. Example: 2
     * 
     * @response 200 {
     *   "message": "Order status updated successfully.",
     *   "order": {
     *     "id": 1,
     *     "status_id": 2,
     *     "status": {
     *       "id": 2,
     *       "name": "Ready"
     *     }
     *   }
     * }
     * @response 500 {
     *   "message": "Error updating order status.",
     *   "error": "..."
     * }
     */

    public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status_id' => 'required|exists:statuses,id',
    ]);

    try {
        $order = Order::findOrFail($id);

        $order->status_id = $request->status_id;

        $saved = $order->save();

        if (!$saved) {
            return response()->json(['message' => 'Failed to save order.'], 500);
        }

        return response()->json([
            'message' => 'Order status updated successfully.',
            'order' => $order->fresh('status'), 
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error updating order status.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

}