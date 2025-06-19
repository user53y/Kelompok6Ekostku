<?php

namespace App\Traits;

trait WhatsAppNotification
{
    protected function sendWhatsAppMessage($phone, $message)
    {
        try {
            \Log::info('Attempting to send WhatsApp message:', [
                'phone' => $phone,
                'message_length' => strlen($message)
            ]);

            if (empty(config('services.fonnte.token'))) {
                throw new \Exception('WhatsApp API token not configured');
            }

            $curl = curl_init();

            $params = [
                'target' => $phone,
                'message' => $message,
                'delay' => '1',
            ];

            \Log::debug('WhatsApp API request params:', $params);

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.fonnte.com/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 10, // Set a reasonable timeout
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $params,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: '.config('services.fonnte.token')
                ),
            ));

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            \Log::info('WhatsApp API response:', [
                'http_code' => $httpCode,
                'response' => $response
            ]);

            if (curl_errno($curl)) {
                throw new \Exception('Curl error: ' . curl_error($curl));
            }

            curl_close($curl);

            if ($httpCode != 200) {
                throw new \Exception("API request failed with status $httpCode");
            }

            $result = json_decode($response, true);
            if (!$result || isset($result['error'])) {
                throw new \Exception($result['error'] ?? 'Unknown API error');
            }

            \Log::info('Successfully sent WhatsApp message');
            return true;

        } catch (\Exception $e) {
            \Log::error('WhatsApp notification failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
}
