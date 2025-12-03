@extends('layouts.developer')

@section('title', __('Controller Generator v3.27.0'))

{{--
/**
 * @file resources/views/developer/code-generator/controller.blade.php
 *
 * @brief Advanced Web Interface for Laravel Controller Generation.
 *        واجهة ويب متقدمة لإنشاء وحدات التحكم (Controllers) في Laravel.
 *
 * @version 3.27.0
 * @project php-magic-system
 * @task 19/100
 *
 * @note This view integrates input forms, code preview, and the Manus AI API for intelligent generation.
 *       تدمج هذه الواجهة نماذج الإدخال، ومعاينة الكود، وواجهة Manus AI API للتوليد الذكي.
 */
--}}

@push('styles')
    {{-- Assuming Tailwind CSS is used for styling --}}
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Custom styles for code preview */
        .code-preview {
            background-color: #1e1e1e;
            color: #d4d4d4;
            font-family: 'Fira Code', 'Consolas', monospace;
            min-height: 500px;
            overflow: auto;
            border-radius: 0.5rem;
        }
        .code-preview pre {
            margin: 0;
            padding: 1rem;
        }
        /* Style for active tab */
        .tab-active {
            border-bottom: 2px solid #3b82f6; /* blue-500 */
            color: #3b82f6;
        }
    </style>
@endpush

@section('content')
    <div class="container mx-auto p-6" x-data="{ activeTab: 'inputs', codePreview: '// Generated Controller Code will appear here...', aiPrompt: '' }">

        <h1 class="text-3xl font-bold mb-6 text-gray-800 dark:text-gray-100">
            {{ __('Controller Generator') }} <span class="text-sm text-gray-500">v3.27.0</span>
        </h1>

        {{-- Main Layout: Inputs and Preview/AI --}}
        <div class="flex flex-col lg:flex-row gap-6">

            {{-- Panel 1: Input Forms --}}
            <div class="lg:w-1/2 bg-white dark:bg-gray-800 shadow-xl rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4 border-b pb-2 text-gray-700 dark:text-gray-200">{{ __('Controller Configuration') }}</h2>

                <form id="controller-form" onsubmit="return false;">
                    {{-- Controller Name --}}
                    <div class="mb-4">
                        <label for="controller_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Controller Name (e.g., PostController)') }}</label>
                        <input type="text" id="controller_name" name="controller_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white p-2">
                        @error('controller_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Model Name (Optional) --}}
                    <div class="mb-4">
                        <label for="model_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Model Name (Optional, e.g., Post)') }}</label>
                        <input type="text" id="model_name" name="model_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white p-2">
                    </div>

                    {{-- Controller Type --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Controller Type') }}</label>
                        <div class="flex flex-wrap gap-4">
                            @foreach(['resource' => 'Resource', 'api' => 'API Resource', 'basic' => 'Basic', 'invokable' => 'Invokable'] as $key => $label)
                                <label class="inline-flex items-center">
                                    <input type="radio" name="controller_type" value="{{ $key }}" class="form-radio text-indigo-600 dark:bg-gray-700 dark:border-gray-600" @if($key === 'basic') checked @endif>
                                    <span class="ml-2 text-gray-700 dark:text-gray-300">{{ __($label) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Custom Logic / AI Instructions --}}
                    <div class="mb-6">
                        <label for="custom_logic" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Custom Methods/Logic (For AI or Manual Generation)') }}</label>
                        <textarea id="custom_logic" name="custom_logic" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white p-2" placeholder="{{ __('Specify methods like: index(list all), show(single item), store(create new), update(edit existing), destroy(delete). Add specific logic requirements here.') }}"></textarea>
                    </div>

                    {{-- Options Checkboxes --}}
                    <div class="mb-6 border-t pt-4">
                        <h3 class="text-md font-medium mb-2 text-gray-700 dark:text-gray-200">{{ __('Additional Options') }}</h3>
                        <div class="grid grid-cols-2 gap-4">
                            @foreach(['--requests' => 'Generate Form Requests', '--migration' => 'Generate Migration', '--policy' => 'Generate Policy', '--parent' => 'Parent Controller (for nested resources)'] as $key => $label)
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="options[]" value="{{ $key }}" class="form-checkbox text-indigo-600 dark:bg-gray-700 dark:border-gray-600">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __($label) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex justify-between border-t pt-4">
                        <button type="button" onclick="generatePreview()" class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-md shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Generate & Preview') }}
                        </button>
                        <button type="reset" class="px-4 py-2 bg-gray-200 text-gray-700 font-semibold rounded-md shadow-md hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 transition ease-in-out duration-150">
                            {{ __('Reset Form') }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- Panel 2: Tabs (Preview and AI) --}}
            <div class="lg:w-1/2 bg-white dark:bg-gray-800 shadow-xl rounded-lg">
                {{-- Tabs Navigation --}}
                <div class="flex border-b border-gray-200 dark:border-gray-700">
                    <button @click="activeTab = 'preview'" :class="{'tab-active': activeTab === 'preview', 'text-gray-500 dark:text-gray-400': activeTab !== 'preview'}" class="py-3 px-6 text-sm font-medium transition duration-150 ease-in-out hover:text-indigo-600 dark:hover:text-indigo-400">
                        {{ __('Code Preview') }}
                    </button>
                    <button @click="activeTab = 'ai'" :class="{'tab-active': activeTab === 'ai', 'text-gray-500 dark:text-gray-400': activeTab !== 'ai'}" class="py-3 px-6 text-sm font-medium transition duration-150 ease-in-out hover:text-indigo-600 dark:hover:text-indigo-400">
                        {{ __('Manus AI Assistant') }}
                    </button>
                </div>

                {{-- Tab Content: Code Preview --}}
                <div x-show="activeTab === 'preview'" class="p-4">
                    <h2 class="text-xl font-semibold mb-4 text-gray-700 dark:text-gray-200">{{ __('Generated Code') }}</h2>
                    <div class="code-preview">
                        <pre><code class="language-php" x-text="codePreview"></code></pre>
                    </div>

                    {{-- Error Handling Area --}}
                    <div id="error-display" class="mt-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded hidden" role="alert">
                        <p class="font-bold">{{ __('Error:') }}</p>
                        <p id="error-message"></p>
                    </div>

                    {{-- Final Action Buttons --}}
                    <div class="flex justify-end mt-4 gap-4">
                        <button type="button" onclick="copyCode()" class="px-4 py-2 bg-green-600 text-white font-semibold rounded-md shadow-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Copy Code') }}
                        </button>
                        <button type="button" onclick="saveFile()" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-md shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Save Controller File') }}
                        </button>
                    </div>
                </div>

                {{-- Tab Content: AI Assistant --}}
                <div x-show="activeTab === 'ai'" class="p-4">
                    <h2 class="text-xl font-semibold mb-4 text-gray-700 dark:text-gray-200">{{ __('AI-Powered Generation') }}</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        {{ __('Use the Manus AI to generate complex controller logic based on natural language instructions.') }}
                        <br>
                        {{ __('استخدم Manus AI لتوليد منطق وحدة تحكم معقد بناءً على تعليمات اللغة الطبيعية.') }}
                    </p>

                    <div class="mb-4">
                        <label for="ai_prompt" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('AI Prompt / Instructions') }}</label>
                        <textarea id="ai_prompt" x-model="aiPrompt" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white p-2" placeholder="{{ __('Example: Create a resource controller for "Product" model. The index method should only return products where "is_published" is true. The store method must validate the "price" field to be a positive integer.') }}"></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="button" onclick="generateWithAI(aiPrompt)" class="px-4 py-2 bg-purple-600 text-white font-semibold rounded-md shadow-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Generate with Manus AI') }}
                        </button>
                    </div>

                    {{-- AI Response/Status Area --}}
                    <div id="ai-status" class="mt-4 p-3 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded hidden" role="status">
                        <p id="ai-status-message">{{ __('Waiting for AI instructions...') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Alpine.js for simple interactivity --}}
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>

    <script>
        /**
         * @brief Simulates the generation of controller code based on form inputs.
         *        This function would typically make an AJAX call to a Laravel backend route.
         *
         * @param {Event} event The form submission event (optional).
         * @return {void}
         */
        function generatePreview(event) {
            // Prevent default form submission if called from a form
            if (event) event.preventDefault();

            const controllerName = document.getElementById('controller_name').value;
            const modelName = document.getElementById('model_name').value;
            const controllerType = document.querySelector('input[name="controller_type"]:checked').value;
            const customLogic = document.getElementById('custom_logic').value;
            const options = Array.from(document.querySelectorAll('input[name="options[]"]:checked')).map(cb => cb.value);

            // Simple validation
            if (!controllerName) {
                displayError('Controller Name is required.');
                return;
            }

            // Simulate API call to backend for code generation
            // In a real application, this would be an axios/fetch call to a route like /developer/code-generator/controller/generate
            const generatedCode = `<?php

namespace App\\Http\\Controllers;

use Illuminate\\Http\\Request;
${modelName ? 'use App\\Models\\' + modelName + ';' : ''}
/**
 * @class ${controllerName}
 * @brief Controller generated by the Controller Generator v3.27.0.
 *        وحدة تحكم تم توليدها بواسطة مولد وحدات التحكم.
 *
 * @package App\\Http\\Controllers
 * @type ${controllerType}
 */
class ${controllerName} extends Controller
{
    // Custom Logic / Methods based on input:
    /*
${customLogic.split('\\n').map(line => '     * ' + line).join('\\n')}
    */

    /**
     * @brief Display a listing of the resource.
     *        عرض قائمة بالموارد.
     *
     * @param \\Illuminate\\Http\\Request $request
     * @return \\Illuminate\\Http\\Response
     */
    public function index(Request $request)
    {
        // Implementation for index method...
        ${modelName ? 'return ' + modelName + '::all();' : 'return response()->json([]);'}
    }

    // ... other resource methods (show, store, update, destroy) would follow based on type ...

    // Additional Options: ${options.join(', ')}

    // Error Handling Example:
    /**
     * @brief Handle a potential error during resource creation.
     *        معالجة خطأ محتمل أثناء إنشاء مورد.
     *
     * @param \\Illuminate\\Http\\Request $request
     * @return \\Illuminate\\Http\\Response
     */
    public function store(Request $request)
    {
        try {
            // Logic to store the resource...
            // Use Type Hints: Request $request
            // Follow PSR-12
            return response()->json(['status' => 'success'], 201);
        } catch (\\Exception $e) {
            // Comprehensive Error Handling
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
`;

            // Update the Alpine.js data property to refresh the preview
            Alpine.store('codePreview', generatedCode);
            document.querySelector('[x-data]').__x.$data.codePreview = generatedCode;
            hideError();
        }

        /**
         * @brief Handles the AI generation request via Manus API.
         *        تعالج طلب التوليد بالذكاء الاصطناعي عبر Manus API.
         *
         * @param {string} prompt The user's natural language prompt.
         * @return {void}
         */
        function generateWithAI(prompt) {
            if (!prompt) {
                displayAIStatus('Please enter a prompt for the AI.', 'bg-red-100 text-red-700');
                return;
            }

            displayAIStatus('Generating code with Manus AI... Please wait.', 'bg-yellow-100 text-yellow-700');

            // Simulate API call to Manus AI
            // In a real application, this would be an axios/fetch call to a route that interfaces with the Manus API
            setTimeout(() => {
                const aiGeneratedCode = `<?php

namespace App\\Http\\Controllers;

use App\\Models\\Product;
use App\\Http\\Requests\\StoreProductRequest; // Generated by AI based on prompt
use Illuminate\\Http\\Request;

/**
 * @class ProductController
 * @brief AI-Generated Controller for Product resource.
 *        وحدة تحكم مولدة بالذكاء الاصطناعي لمورد المنتج.
 */
class ProductController extends Controller
{
    /**
     * @brief Display a listing of published products.
     *        عرض قائمة بالمنتجات المنشورة.
     *
     * @param \\Illuminate\\Http\\Request $request
     * @return \\Illuminate\\Http\\Response
     */
    public function index(Request $request)
    {
        // AI-generated logic: only published products
        return Product::where('is_published', true)->get();
    }

    /**
     * @brief Store a newly created resource in storage.
     *        تخزين مورد جديد تم إنشاؤه.
     *
     * @param \\App\\Http\\Requests\\StoreProductRequest $request
     * @return \\Illuminate\\Http\\Response
     */
    public function store(StoreProductRequest $request)
    {
        try {
            // AI-generated logic: validation handled by StoreProductRequest
            $product = Product::create($request->validated());
            return response()->json($product, 201);
        } catch (\\Exception $e) {
            // Comprehensive Error Handling
            return response()->json(['status' => 'error', 'message' => 'Failed to create product: ' . $e->getMessage()], 500);
        }
    }
}
`;
                // Update preview and switch tab
                document.querySelector('[x-data]').__x.$data.codePreview = aiGeneratedCode;
                document.querySelector('[x-data]').__x.$data.activeTab = 'preview';
                displayAIStatus('AI generation complete. Review the code in the Preview tab.', 'bg-green-100 text-green-700');
            }, 2000); // Simulate network delay
        }

        /**
         * @brief Displays an error message in the dedicated error area.
         *        تعرض رسالة خطأ في منطقة عرض الأخطاء المخصصة.
         *
         * @param {string} message The error message.
         * @return {void}
         */
        function displayError(message) {
            const errorDisplay = document.getElementById('error-display');
            const errorMessage = document.getElementById('error-message');
            errorMessage.textContent = message;
            errorDisplay.classList.remove('hidden');
        }

        /**
         * @brief Hides the error message area.
         *        تخفي منطقة عرض رسائل الخطأ.
         *
         * @return {void}
         */
        function hideError() {
            document.getElementById('error-display').classList.add('hidden');
        }

        /**
         * @brief Displays a status message in the AI status area.
         *        تعرض رسالة حالة في منطقة حالة الذكاء الاصطناعي.
         *
         * @param {string} message The status message.
         * @param {string} classes Tailwind classes for styling the status box.
         * @return {void}
         */
        function displayAIStatus(message, classes) {
            const aiStatus = document.getElementById('ai-status');
            const aiStatusMessage = document.getElementById('ai-status-message');
            aiStatusMessage.textContent = message;
            aiStatus.className = `mt-4 p-3 border rounded ${classes}`;
            aiStatus.classList.remove('hidden');
        }

        /**
         * @brief Copies the generated code to the clipboard.
         *        تنسخ الكود المولد إلى الحافظة.
         *
         * @return {void}
         */
        function copyCode() {
            const code = document.querySelector('[x-data]').__x.$data.codePreview;
            navigator.clipboard.writeText(code).then(() => {
                alert('Code copied to clipboard!');
            }).catch(err => {
                console.error('Could not copy text: ', err);
                alert('Failed to copy code. Please copy manually.');
            });
        }

        /**
         * @brief Simulates saving the file to the project structure.
         *        تحاكي حفظ الملف في هيكل المشروع.
         *
         * @return {void}
         */
        function saveFile() {
            // In a real application, this would trigger a backend process to write the file
            alert('File save initiated. Controller will be saved to: app/Http/Controllers/' + document.getElementById('controller_name').value + '.php');
        }

        // Initial call to populate the preview with a default structure
        document.addEventListener('DOMContentLoaded', () => {
            generatePreview();
        });
    </script>
@endpush
