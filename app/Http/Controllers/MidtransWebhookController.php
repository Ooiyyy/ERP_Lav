<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\PembayaranVa;
use Carbon\Carbon;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('MIDTRANS WEBHOOK MASUK', $request->all());

        // =============================
        // 1. VALIDASI SIGNATURE
        // =============================
        $serverKey = config('services.midtrans.server_key');

        $expectedSignature = hash(
            'sha512',
            $request->order_id .
            $request->status_code .
            $request->gross_amount .
            $serverKey
        );

        if ($request->signature_key !== $expectedSignature) {
            Log::warning('MIDTRANS SIGNATURE TIDAK VALID');
            return response()->json(['message' => 'invalid signature'], 403);
        }

        // =============================
        // 2. AMBIL DATA PENTING
        // =============================
        $midtransOrderId   = $request->order_id;
        $transactionStatus = $request->transaction_status;
        $paymentType       = $request->payment_type;

        // =============================
        // 3. NORMALISASI STATUS
        // =============================
        switch ($transactionStatus) {
            case 'capture':
            case 'settlement':
                $status = 'paid';
                break;
            case 'expire':
                $status = 'expired';
                break;
            case 'cancel':
            case 'deny':
                $status = 'failed';
                break;
            default:
                $status = 'pending';
        }

        // =============================
        // 4. DETEKSI CHANNEL (INI KUNCI)
        // =============================
        $channel = null;

        // VA BANK
        if ($paymentType === 'bank_transfer') {
            if (!empty($request->va_numbers[0]['bank'])) {
                $channel = strtoupper($request->va_numbers[0]['bank']); // BRI / BCA / MANDIRI
            } elseif (!empty($request->permata_va_number)) {
                $channel = 'PERMATA';
            } else {
                $channel = 'BANK_TRANSFER';
            }
        }
        // E-WALLET
        elseif (in_array($paymentType, ['gopay', 'shopeepay', 'dana'])) {
            $channel = strtoupper($paymentType);
        }
        elseif ($paymentType === 'cstore') {
            if (!empty($request->store)) {
                $channel = strtoupper($request->store); // INDOMARET / ALFAMART
            } else {
                $channel = 'CSTORE';
            }
        }
        
        // FALLBACK
        else {
            $channel = strtoupper((string) $paymentType);
        }

        // =============================
        // 5. UPDATE DATABASE
        // =============================
        $updated = PembayaranVa::where('nomor_ref', $midtransOrderId)
            ->update([
                'status'         => $status,
                'channel'        => $channel,
                'tanggal_update' => Carbon::now(),
            ]);

        Log::info('MIDTRANS WEBHOOK UPDATE', [
            'order_id' => $midtransOrderId,
            'status'   => $status,
            'channel'  => $channel,
            'updated'  => $updated,
        ]);

        return response()->json(['message' => 'OK']);
    }
}
