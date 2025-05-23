<?php

namespace App\Http\Controllers;

use App\Models\Pizza;
use Illuminate\Http\Request;

class PizzaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pizzas = Pizza::get();
        return response()->json([
            'status'=>'success',
            'data'=>$pizzas
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
   /**
     * إضافة بيتزا جديدة.
     *
     * @group Pizzas
     * @authenticated
     * @bodyParam name string required اسم البيتزا. Example: Margherita
     * @bodyParam price number required السعر. Example: 12.50
     * @bodyParam description string وصف اختياري. Example: بيتزا نباتية
     * @bodyParam available boolean الحالة. Example: true
     * @bodyParam toppings array قائمة إضافات. Example: [1, 2]
     * @bodyParam toppings.* integer رقم الإضافة. يجب أن تكون موجودة بجدول toppings
     * @response 201 {
     *   "status": "success",
     *   "data": {
     *     "id": 1,
     *     "name": "Margherita",
     *     "price": "12.50",
     *     "available": true,
     *     "toppings": [...]
     *   }
     * }
     */
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'=>'required|string|max:255',
            'price'=>'required|numeric|min:0',
            'description'=>'nullable|string',
            'available'=>'required|boolean',
            'toppings'=>'array',
            'toppings.*' => 'exists:toppings,id',
        ]);

        $pizza=Pizza::create([
            'name'=>$validated['name'],
            'price'=>$validated['price'],
            'description'=>$validated ['description'] ?? null,
            'available'=>$validated ['available']
        ]);

        if (!empty($validated['toppings'])) {
            $pizza->toppings()->attach($validated['toppings']);
        }

        return response()->json([
            'status' => 'success',
            'data' => $pizza->load('toppings')
        ],201);
    }

/**
     * عرض تفاصيل بيتزا معينة.
     *
     * @group Pizzas
     * @urlParam id int رقم البيتزا. Example: 1
     * @response 200 {
     *   "status": "success",
     *   "data": {
     *     "id": 1,
     *     "name": "Margherita",
     *     "price": "12.50",
     *     "toppings": [...]
     *   }
     * }
     * @response 404 {
     *   "status": "error",
     *   "message": "Pizza not found"
     * }
     */
    /**
     * Display the specified resource.
     */
    public function show(string $id)
   {
    $pizza = Pizza::with('toppings')->find($id);

    if (!$pizza) {
        return response()->json([
            'status' => 'error',
            'message' => 'Pizza not found'
        ], 404);
    }

    return response()->json([
        'status' => 'success',
        'data' => $pizza
    ]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }
    /**
     * تعديل بيتزا موجودة.
     *
     * @group Pizzas
     * @authenticated
     * @urlParam id int رقم البيتزا. Example: 1
     * @bodyParam name string اسم البيتزا. Example: Updated Pizza
     * @bodyParam price number السعر. Example: 14.50
     * @bodyParam available boolean الحالة. Example: false
     * @bodyParam description string وصف. Example: وصف جديد
     * @bodyParam toppings array قائمة إضافات. Example: [1, 3]
     * @response 200 {
     *   "status": "success",
     *   "data": {
     *     "id": 1,
     *     "name": "Updated Pizza",
     *     "price": "14.50",
     *     "available": false
     *   }
     * }
     * @response 404 {
     *   "status": "error",
     *   "message": "Pizza not found"
     * }
     */

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
{
    $pizza = Pizza::find($id);

    if (!$pizza) {
        return response()->json([
            'status' => 'error',
            'message' => 'Pizza not found'
        ], 404);
    }

    $validated = $request->validate([
        'name' => 'sometimes|required|string|max:255',
        'price' => 'sometimes|required|numeric|min:0',
        'description' => 'nullable|string',
        'available' => 'sometimes|required|boolean',
        'toppings' => 'array',
        'toppings.*' => 'exists:toppings,id',
    ]);

    $pizza->update([
        'name' => $validated['name'] ?? $pizza->name,
        'price' => $validated['price'] ?? $pizza->price,
        'description' => $validated['description'] ?? $pizza->description,
        'available' => $validated['available'] ?? $pizza->available,
    ]);

    if (array_key_exists('toppings', $validated)) {
        $pizza->toppings()->sync($validated['toppings']);
    }

    return response()->json([
        'status' => 'success',
        'data' => $pizza->load('toppings')
    ]);
}
/** 
  * حذف بيتزا.
* @group Pizzas
* @authenticated
* @urlParam id int رقم البيتزا. Example: 1
* @response 200 {
*   "status": "success",
*   "message": "Pizza deleted successfully"
* }
* @response 404 {
*   "status": "error",
*   "message": "Pizza not found"
* }
*/
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
{
    $pizza = Pizza::find($id);

    if (!$pizza) {
        return response()->json([
            'status' => 'error',
            'message' => 'Pizza not found'
        ], 404);
    }

    
    $pizza->toppings()->detach();

    $pizza->delete();

    return response()->json([
        'status' => 'success',
        'message' => 'Pizza deleted successfully'
    ]);
}

}
