<?php

namespace App\Http\Controllers;

use App\DTO\PaymentLinkDto;
use GuzzleHttp\Client;
use App\Http\Controllers\Controller;
use App\Models\PaymentLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LinkController extends Controller
{
    public function getLink(Request $request)
    {
        try {
            $id = $request->id;

            $paymentLink = PaymentLink::where('nu_link', $id)->first();

            if (!$paymentLink) {
                return response()->json(['error' => 'Payment id not found'], 404);
            }

            $client = new Client();
            $url_request = "https://api-sandbox.fpay.me/link?nu_link={$id}";
            $headers = [
                'Content-Type' => 'application/json',
                'Client-Code' => 'FC-SB-15',
                'Client-key' => '6ea297bc5e294666f6738e1d48fa63d2'
            ];

            $response = $client->request('GET', $url_request, [
                'headers' => $headers
            ]);

            $responseBody = json_decode($response->getBody());

            return response()->json($responseBody, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while trying to find payment link'], 500);
        }
    }


    public function createLink(Request $request)
    {
        try {
            $client = new Client();
            $url_request = "https://api-sandbox.fpay.me/link";
            $headers = [
                'Content-Type' => 'application/json',
                'Client-Code' => 'FC-SB-15',
                'Client-key' => '6ea297bc5e294666f6738e1d48fa63d2'
            ];

            $requestData = new PaymentLinkDto($request->all());

            $response = $client->request('POST', $url_request, [
                'headers' => $headers,
                'json' => $requestData
            ]);

            $responseBody = json_decode($response->getBody());

            $paymentLinkData = [
                'nu_link' => $responseBody->data->nu_link,
                'url_link' => $responseBody->data->url_link,
                'slug' => $responseBody->data->slug,
            ];

            PaymentLink::create($paymentLinkData);

            return response()->json($responseBody, 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 400);
        }
    }

    public function deleteLink(Request $request)
    {
        try {
            $id = $request->id;

            $paymentLink = PaymentLink::where('nu_link', $id)->first();

            if (!$paymentLink) {
                return response()->json(['error' => 'Payment id not found'], 404);
            }

            $client = new Client();
            $url_request = "https://api-sandbox.fpay.me/link/{$id}";
            $headers = [
                'Content-Type' => 'application/json',
                'Client-Code' => 'FC-SB-15',
                'Client-key' => '6ea297bc5e294666f6738e1d48fa63d2'
            ];

            $response = $client->request('DELETE', $url_request, [
                'headers' => $headers
            ]);

            $responseBody = json_decode($response->getBody());

            if ($response->getStatusCode() === 200 && $responseBody->success) {
                DB::beginTransaction();
                $paymentLink->delete();
                DB::commit();
            }

            return response()->json($responseBody, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while trying to delete payment link'], 500);
        }

    }
}
