<?php

namespace App\Http\Controllers;

use App\Models\Topping;
use Illuminate\Http\Request;

class ToppingController extends Controller
{
        /**
     * عرض كل الإضافات المتاحة (Toppings).
     *
     * @group Toppings
     * @response 200 {
     *   "status": "success",
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Olives",
     *       "price": 1.50,
     *       "available": true
     *     }
     *   ]
     * }
     */
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $toppings = Topping::get();
        return response()->json([
            'status'=>'success',
            'data'=>$toppings
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
     * إنشاء إضافة جديدة.
     *
     * @group Toppings
     * @authenticated
     * @bodyParam name string required اسم الإضافة. Example: Pepperoni
     * @bodyParam price number required سعر الإضافة. Example: 2.00
     * @bodyParam available boolean الحالة. Example: true
     * @response 201 {
     *   "status": "success",
     *   "data": {
     *     "id": 1,
     *     "name": "Pepperoni",
     *     "price": "2.00",
     *     "available": true
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
            'available'=>'required|boolean',
        ]);

        $toppings=Topping::create([
            'name'=>$validated['name'],
            'price'=>$validated['price'],
            'available'=>$validated ['available']
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $toppings
        ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }
/**
     * تعديل إضافة موجودة.
     *
     * @group Toppings
     * @authenticated
     * @urlParam id int رقم الإضافة. Example: 1
     * @bodyParam name string اسم الإضافة. Example: Updated Topping
     * @bodyParam price number السعر الجديد. Example: 2.50
     * @bodyParam available boolean الحالة. Example: false
     * @response 200 {
     *   "status": "success",
     *   "data": {
     *     "id": 1,
     *     "name": "Updated Topping",
     *     "price": "2.50",
     *     "available": false
     *   }
     * }
     * @response 404 {
     *   "status": "error",
     *   "message": "Topping not found"
     * }
     */
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }
 /**
     * حذف إضافة.
     *
     * @group Toppings
     * @authenticated
     * @urlParam id int رقم الإضافة. Example: 1
     * @response 200 {
     *   "status": "success",
     *   "message": "Topping deleted successfully"
     * }
     * @response 404 {
     *   "status": "error",
     *   "message": "Topping not found"
     * }
     */
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
