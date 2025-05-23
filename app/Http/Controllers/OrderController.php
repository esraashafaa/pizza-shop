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
                'customer_id' => $request->customer_id, // 👈 إدخال customer_id من الطلب
                'status_id' => $status->id,
                'total_price' => 0, // سيتم تعديله لاحقًا
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

    public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status_id' => 'required|exists:statuses,id',
    ]);

    try {
        $order = Order::findOrFail($id);

        $order->status_id = $request->status_id;

        // احفظ وأعد تحميل الطلب للتحقق
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