<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StoreController extends Controller
{
    public function inventory()
    {
        $results = Pet::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()->toArray();

        $results = array_column($results, 'count', 'status');

        return $this->apiResponse(200, $results);
    }

    public function show($order)
    {
        $orderModel = Order::find($order);
        if (!$orderModel) {
            return $this->apiResponse(404, 'not found');
        }

        return $this->apiResponse(200, $orderModel);
    }

    public function store(Request $request)
    {
        $validated = $this->validate([
            'petId' => 'exists:pets,id',
            'quantity' => ['numeric', 'gt:0'],
            'shipDate' => Rule::requiredIf(
                in_array($request->input('status'), ['shipped', 'delivered'])
            ),
            'complete' => ['required', 'boolean', 'accepted_if:status,delivered'],
            'status' => ['required', 'in:placed,shipped,delivered,cancelled'],
        ], $request);

        if ($validated instanceof Response) {
            return $validated;
        }

        $shipDate = null;

        if (isset($validated['shipDate'])) {
            $shipDate = Carbon::parse($validated['shipDate']);
        }

        $order = Order::create([
            'pet_id' => $validated['petId'],
            'quantity' => $validated['quantity'],
            'ship_date' => $shipDate,
            'complete' => $validated['complete'],
            'status' => $validated['status'],
        ]);

        return $this->apiResponse(200, $order);
    }


    public function destroy($order)
    {
        $orderModel = Order::find($order);
        if (!$orderModel) {
            return $this->apiResponse(404, 'not found');
        }
        $orderModel->delete();
        return $this->apiResponse(200, strval($orderModel->id));
    }
}
