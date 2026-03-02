<?php

/**
 * Laravel Controller Example
 * 
 * This example shows how to use JasperReports CLI Bridge in a Laravel controller.
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PTPKP\JasperCliBridge\JasperReportService;
use PTPKP\JasperCliBridge\JasperException;
use PTPKP\JasperCliBridge\Laravel\Facade as Jasper;

class ReportController extends Controller
{
    /**
     * Example 1: Using Dependency Injection
     */
    public function generateInvoice(Request $request, JasperReportService $jasper, $invoiceId)
    {
        try {
            $pdfPath = $jasper->generateReport(
                'invoice',           // Template name
                'db',                // Use database connection
                [
                    'invoice_id' => $invoiceId,
                    'company_logo' => public_path('images/logo.png'),
                ],
            );

            // Return PDF as download
            return response()->download($pdfPath, 'invoice.pdf')
                ->deleteFileAfterSend(true);

        } catch (JasperException $e) {
            return response()->json([
                'error' => 'Failed to generate report',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Example 2: Using Facade
     */
    public function generateStatement(Request $request, $customerId)
    {
        try {
            $pdfPath = Jasper::generateReport(
                'customer_statement',
                'db',
                [
                    'customer_id' => $customerId,
                    'start_date' => $request->input('start_date'),
                    'end_date' => $request->input('end_date'),
                ],
            );

            // Return PDF inline (preview in browser)
            return response()->file($pdfPath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="statement.pdf"',
            ]);

        } catch (JasperException $e) {
            return back()->with('error', 'Failed to generate statement: ' . $e->getMessage());
        }
    }

    /**
     * Example 3: Using JSON data source
     */
    public function generateCustomReport(Request $request)
    {
        try {
            // Prepare data
            $data = [
                'report_title' => $request->input('title'),
                'generated_at' => now()->toDateTimeString(),
                'items' => $request->input('items', []),
            ];

            // Create temporary JSON file
            $jsonFile = tempnam(sys_get_temp_dir(), 'report_data_') . '.json';
            file_put_contents($jsonFile, json_encode($data));

            // Generate report
            $pdfPath = Jasper::generateReport(
                'custom_report',
                $jsonFile,
                ['author' => auth()->user()->name],
            );

            // Clean up
            @unlink($jsonFile);

            return response()->download($pdfPath, 'report.pdf')
                ->deleteFileAfterSend(true);

        } catch (JasperException $e) {
            @unlink($jsonFile);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Example 4: List available templates
     */
    public function listTemplates()
    {
        $templates = Jasper::listTemplates();

        return response()->json([
            'templates' => $templates,
            'count' => count($templates),
        ]);
    }

    /**
     * Example 5: Check template exists
     */
    public function checkTemplate($templateName)
    {
        $exists = Jasper::templateExists($templateName);

        return response()->json([
            'template' => $templateName,
            'exists' => $exists,
        ]);
    }

    /**
     * Example 6: Generate and email report
     */
    public function emailInvoice($invoiceId)
    {
        try {
            // Generate PDF
            $pdfPath = Jasper::generateReport('invoice', 'db', [
                'invoice_id' => $invoiceId,
            ]);

            // Get invoice details (example)
            $invoice = \App\Models\Invoice::findOrFail($invoiceId);

            // Send email with attachment
            \Mail::to($invoice->customer_email)->send(
                new \App\Mail\InvoiceMail($invoice, $pdfPath)
            );

            // Clean up
            @unlink($pdfPath);

            return response()->json([
                'message' => 'Invoice sent successfully',
            ]);

        } catch (JasperException $e) {
            return response()->json([
                'error' => 'Failed to generate invoice',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
