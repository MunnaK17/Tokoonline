<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RajaOngkirController extends Controller
{
    private function baseUrl(): string
    {
        return rtrim(env('RAJAONGKIR_BASE_URL', 'https://api.rajaongkir.com/starter'), '/');
    }

    private function apiKey(): ?string
    {
        return env('RAJAONGKIR_API_KEY');
    }

    private function isKomerceV2(): bool
    {
        return str_contains($this->baseUrl(), 'komerce.id');
    }

    private function legacyStatus(array $meta): array
    {
        return [
            'code' => $meta['code'] ?? 500,
            'description' => $meta['message'] ?? $meta['status'] ?? 'Unknown response',
        ];
    }

    public function getProvinces()
    {
        if ($this->isKomerceV2()) {
            $response = Http::withHeaders([
                'key' => $this->apiKey()
            ])->get($this->baseUrl() . '/destination/province');

            $data = $response->json();
            $results = collect($data['data'] ?? [])->map(function ($province) {
                return [
                    'province_id' => $province['id'],
                    'province' => $province['name'],
                ];
            })->values();

            return response()->json([
                'rajaongkir' => [
                    'status' => $this->legacyStatus($data['meta'] ?? []),
                    'results' => $results,
                ],
            ], $response->status());
        }

        $response = Http::withHeaders([
            'key' => $this->apiKey()
        ])->get($this->baseUrl() . '/province');

        return response()->json($response->json());
    }

    public function getCities(Request $request)
    {
        $provinceId = $request->input('province_id');

        if ($this->isKomerceV2()) {
            $response = Http::withHeaders([
                'key' => $this->apiKey()
            ])->get($this->baseUrl() . '/destination/city/' . $provinceId);

            $data = $response->json();
            $results = collect($data['data'] ?? [])->map(function ($city) use ($provinceId) {
                return [
                    'city_id' => $city['id'],
                    'province_id' => $provinceId,
                    'province' => '',
                    'type' => '',
                    'city_name' => $city['name'],
                    'postal_code' => $city['zip_code'] ?? '',
                ];
            })->values();

            return response()->json([
                'rajaongkir' => [
                    'status' => $this->legacyStatus($data['meta'] ?? []),
                    'results' => $results,
                ],
            ], $response->status());
        }

        $response = Http::withHeaders([
            'key' => $this->apiKey()
        ])->get($this->baseUrl() . '/city', [
            'province' => $provinceId
        ]);

        return response()->json($response->json());
    }

    public function getCost(Request $request)
    {
        $origin = $request->input('origin');
        $destination = $request->input('destination');
        $weight = $request->input('weight');
        $courier = $request->input('courier');

        if ($this->isKomerceV2()) {
            $response = Http::asForm()->withHeaders([
                'key' => $this->apiKey()
            ])->post($this->baseUrl() . '/calculate/domestic-cost', [
                'origin' => $origin,
                'destination' => $destination,
                'weight' => $weight,
                'courier' => $courier,
                'price' => 'lowest',
            ]);

            $data = $response->json();
            $costs = collect($data['data'] ?? [])->map(function ($cost) {
                return [
                    'service' => $cost['service'],
                    'description' => $cost['description'] ?? '',
                    'cost' => [[
                        'value' => $cost['cost'],
                        'etd' => str_replace(' day', '', $cost['etd'] ?? ''),
                        'note' => '',
                    ]],
                ];
            })->values();

            return response()->json([
                'rajaongkir' => [
                    'status' => $this->legacyStatus($data['meta'] ?? []),
                    'results' => [[
                        'code' => $courier,
                        'name' => strtoupper($courier),
                        'costs' => $costs,
                    ]],
                ],
            ], $response->status());
        }

        $response = Http::withHeaders([
            'key' => $this->apiKey()
        ])->post($this->baseUrl() . '/cost', [
            'origin' => $origin,
            'destination' => $destination,
            'weight' => $weight,
            'courier' => $courier,
        ]);

        return response()->json($response->json());
    }
}
