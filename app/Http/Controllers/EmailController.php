<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Log, Mail, Validator};
use App\Mail\ContactEmail;

class EmailController extends Controller
{
    public function sendContactEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email',  // ✅ Este é o email DESTINATÁRIO
            'subject' => 'required|string|max:255', 
            'message' => 'required|string'
        ]);

        if ($validator->fails()) { 
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->only(['name', 'email', 'subject', 'message']);
            
            Log::info('📧 Tentativa de envio de email', [
                'from' => 'mussulo@siguangola.com',
                'to' => $data['email'],  // ✅ USA O EMAIL DO FORMULÁRIO COMO DESTINATÁRIO
                'subject' => $data['subject'],
                'smtp_host' => config('mail.mailers.smtp.host')
            ]);

            // ✅ ENVIA PARA O EMAIL QUE O USUÁRIO INFORMOU
            Mail::to($data['email'])->send(new ContactEmail($data));
            
            Log::info('✅ Email enviado para: ' . $data['email']);
            
            return response()->json([
                'success' => true,
                'message' => 'Email enviado com sucesso para ' . $data['email']
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Erro no envio: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar email: ' . $e->getMessage()
            ], 500);
        }
    }

    // Método para envio simples
    public function sendSimpleEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'to' => 'required|email',
            'subject' => 'required|string',
            'content' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            Mail::send([], [], function ($message) use ($request) {
                $message->from('mussulo@siguangola.com', 'Siguangola')
                        ->to($request->to)
                        ->subject($request->subject)
                        ->setBody($request->content, 'text/html');
            });

            return response()->json([
                'success' => true,
                'message' => 'Email enviado com sucesso!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar email: ' . $e->getMessage()
            ], 500);
        }
    }
}