<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class CustomerController extends Controller
{
    public function index()
    {
        try {
            $customers = Customer::all();
            return response()->json($customers, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch customers'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Validate incoming data
            $validated = $request->validate([
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => 'required|email|unique:customers,email',
                'contact_number' => 'required|string'
            ]);

            // Create the customer
            $customer = Customer::create($validated);

            // Sync to Elasticsearch
            $this->syncToElasticsearch($customer);

            return response()->json($customer, 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create customer'], 500);
        }
    }

    public function show($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            return response()->json($customer, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Customer not found'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $customer = Customer::findOrFail($id);

            // Validate incoming data
            $validated = $request->validate([
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => 'required|email|unique:customers,email,' . $id,
                'contact_number' => 'required|string'
            ]);

            // Update the customer
            $customer->update($validated);

            // Sync to Elasticsearch
            $this->syncToElasticsearch($customer);

            return response()->json($customer, 200);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update customer'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $customer->delete();

            // Remove from Elasticsearch
            Http::delete('http://searcher:9200/customers/_doc/' . $customer->id);

            return response()->json(['message' => 'Customer deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete customer'], 500);
        }
    }

    // Sync customer data to Elasticsearch
    private function syncToElasticsearch(Customer $customer)
    {
        $response = Http::post('http://searcher:9200/customers/_doc', [
            'id' => $customer->id,
            'first_name' => $customer->first_name,
            'last_name' => $customer->last_name,
            'email' => $customer->email,
            'contact_number' => $customer->contact_number,
        ]);

        if ($response->failed()) {
            throw new \Exception('Failed to sync customer to Elasticsearch');
        }
    }
}
