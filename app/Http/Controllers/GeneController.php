<?php

namespace App\Http\Controllers;

use App\Models\Gene;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class GeneController
 *
 * Manages the "Genes" (features/modules) of the SEMOP Magic System.
 * This includes listing, enabling, disabling, and updating configuration.
 *
 * @package App\Http\Controllers
 */
class GeneController extends Controller
{
    /**
     * Display a listing of all Genes.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // Fetch all genes from the database.
        // Assumes the Gene model has been defined.
        $genes = Gene::all();

        // Return the list of genes with a success message.
        return response()->json([
            'status' => 'success',
            'message' => 'Genes list retrieved successfully.',
            'data' => $genes,
        ], 200);
    }

    /**
     * Enable a specific Gene by its ID.
     *
     * @param int $id The ID of the Gene to enable.
     * @return JsonResponse
     */
    public function enable(int $id): JsonResponse
    {
        // Find the gene by its ID.
        $gene = Gene::find($id);

        // Check if the gene exists.
        if (!$gene) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gene not found.',
            ], 404);
        }

        // Check if the gene is already enabled.
        if ($gene->is_enabled) {
            return response()->json([
                'status' => 'warning',
                'message' => 'Gene is already enabled.',
                'data' => $gene,
            ], 200);
        }

        // Update the status to enabled and save the change.
        $gene->is_enabled = true;
        $gene->save();

        // Return a success response.
        return response()->json([
            'status' => 'success',
            'message' => "Gene '{$gene->name}' enabled successfully.",
            'data' => $gene,
        ], 200);
    }

    /**
     * Disable a specific Gene by its ID.
     *
     * @param int $id The ID of the Gene to disable.
     * @return JsonResponse
     */
    public function disable(int $id): JsonResponse
    {
        // Find the gene by its ID.
        $gene = Gene::find($id);

        // Check if the gene exists.
        if (!$gene) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gene not found.',
            ], 404);
        }

        // Check if the gene is already disabled.
        if (!$gene->is_enabled) {
            return response()->json([
                'status' => 'warning',
                'message' => 'Gene is already disabled.',
                'data' => $gene,
            ], 200);
        }

        // Update the status to disabled and save the change.
        $gene->is_enabled = false;
        $gene->save();

        // Return a success response.
        return response()->json([
            'status' => 'success',
            'message' => "Gene '{$gene->name}' disabled successfully.",
            'data' => $gene,
        ], 200);
    }

    /**
     * Update the configuration for a specific Gene.
     *
     * @param Request $request The incoming request containing the new configuration.
     * @param int $id The ID of the Gene to update.
     * @return JsonResponse
     */
    public function updateConfig(Request $request, int $id): JsonResponse
    {
        // Find the gene by its ID.
        $gene = Gene::find($id);

        // Check if the gene exists.
        if (!$gene) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gene not found.',
            ], 404);
        }

        // Define validation rules for the configuration update.
        // Assumes 'config' is a JSON field in the Gene model.
        $rules = [
            'config' => ['required', 'array'],
            // Additional specific validation for config keys can be added here, e.g.:
            // 'config.max_users' => ['nullable', 'integer', 'min:1'],
        ];

        // Validate the request data.
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'validation_error',
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Get the current configuration, defaulting to an empty array if null.
        $currentConfig = $gene->config ?? [];
        $newConfig = $request->input('config');

        // Merge the new configuration with the existing one.
        // This allows partial updates to the JSON config.
        $updatedConfig = array_merge($currentConfig, $newConfig);

        // Update the gene's configuration and save.
        $gene->config = $updatedConfig;
        $gene->save();

        // Return a success response.
        return response()->json([
            'status' => 'success',
            'message' => "Configuration for Gene '{$gene->name}' updated successfully.",
            'data' => $gene,
        ], 200);
    }
}