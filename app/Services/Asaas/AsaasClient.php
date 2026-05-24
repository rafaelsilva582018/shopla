<?php

namespace App\Services\Asaas;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class AsaasClient
{
    public function createCheckout(array $payload): array
    {
        $response = $this->request()->post('/checkouts', $payload);

        if ($response->failed()) {
            throw new RuntimeException($this->errorMessage($response));
        }

        return $response->json() ?? [];
    }

    public function cancelSubscription(string $subscriptionId): array
    {
        $response = $this->request()->delete('/subscriptions/' . $subscriptionId);

        if ($response->failed()) {
            throw new RuntimeException($this->errorMessage($response));
        }

        return $response->json() ?? [];
    }

    private function request()
    {
        $token = config('services.asaas.access_token');

        if (!$token) {
            throw new RuntimeException('A chave do Asaas ainda nao foi configurada.');
        }

        return Http::baseUrl(rtrim(config('services.asaas.base_url'), '/'))
            ->acceptJson()
            ->asJson()
            ->withHeaders([
                'access_token' => $token,
                'User-Agent' => config('services.asaas.user_agent', 'Shopla'),
            ]);
    }

    private function errorMessage(Response $response): string
    {
        $errors = collect($response->json('errors', []))
            ->pluck('description')
            ->filter()
            ->join(' ');

        return $errors ?: 'Nao foi possivel criar o checkout no Asaas. Tente novamente.';
    }
}
