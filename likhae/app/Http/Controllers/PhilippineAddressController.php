<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class PhilippineAddressController extends Controller
{
    private string $baseUrl = 'https://psgc.cloud/api/v2';

    private function fetch(string $path): array
    {
        $response = Http::timeout(15)
            ->withoutVerifying()
            ->acceptJson()
            ->get("{$this->baseUrl}/{$path}");

        if ($response->failed()) {
            return [];
        }

        return $response->json('data') ?? [];
    }

    public function regions(): JsonResponse
    {
        try {
            $data = collect($this->fetch('regions'))
                ->map(fn($r) => ['code' => $r['code'] ?? null, 'name' => $r['name'] ?? null])
                ->filter(fn($r) => !empty($r['code']) && !empty($r['name']))
                ->sortBy('name')
                ->values();

            return response()->json(['data' => $data]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['message' => 'Address service unavailable.', 'data' => []], 500);
        }
    }

    public function provinces(string $region): JsonResponse
    {
        try {
            $data = collect($this->fetch("regions/{$region}/provinces"))
                ->map(fn($p) => ['code' => $p['code'] ?? null, 'name' => $p['name'] ?? null])
                ->filter(fn($p) => !empty($p['code']) && !empty($p['name']))
                ->sortBy('name')
                ->values();

            return response()->json(['data' => $data]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['message' => 'Address service unavailable.', 'data' => []], 500);
        }
    }

    public function municipalities(string $province): JsonResponse
    {
        try {
            $data = collect($this->fetch("provinces/{$province}/cities-municipalities"))
                ->map(fn($m) => ['code' => $m['code'] ?? null, 'name' => $m['name'] ?? null])
                ->filter(fn($m) => !empty($m['code']) && !empty($m['name']))
                ->sortBy('name')
                ->values();

            return response()->json(['data' => $data]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['message' => 'Address service unavailable.', 'data' => []], 500);
        }
    }

    public function barangays(string $municipality): JsonResponse
    {
        try {
            $data = collect($this->fetch("cities-municipalities/{$municipality}/barangays"))
                ->map(fn($b) => ['code' => $b['code'] ?? null, 'name' => $b['name'] ?? null])
                ->filter(fn($b) => !empty($b['code']) && !empty($b['name']))
                ->sortBy('name')
                ->values();

            return response()->json(['data' => $data]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['message' => 'Address service unavailable.', 'data' => []], 500);
        }
    }
}
