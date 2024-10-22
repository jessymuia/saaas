<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DocumentsController extends Controller
{
    //
    public function previewInvoice($invoice = null)
    {
        if (!$invoice) {
            abort(404,'Invoice not found');
        }
        $path = storage_path('app/invoices/'.$invoice);
        return response()->file($path);
    }

    public function previewManualInvoice($invoice = null)
    {
        if (!$invoice) {
            abort(404,'Invoice not found');
        }
        $path = storage_path('app/manual_invoices/'.$invoice);
        return response()->file($path);
    }

    public function previewCreditNote($creditNote = null)
    {
        if (!$creditNote) {
            abort(404,'Credit Note not found');
        }
        $path = storage_path('app/credit-notes/'.$creditNote);
        return response()->file($path);
    }

    public function previewReceipt($receipt = null)
    {
        if (!$receipt) {
            abort(404,'Receipt not found');
        }
        $path = storage_path('app/invoice_payments/'.$receipt);
        return response()->file($path);
    }

    public function previewCompanyLogo($companyLogo = null){
        if (!$companyLogo) {
            abort(404,'Logo not found');
        }
        $path = storage_path('app/public/logos/'.$companyLogo);
        return response()->file($path);
    }
}
