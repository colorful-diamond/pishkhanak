@extends('front.layouts.app')

@section('title', 'AI Chat Test Results')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-4">AI Chat System Test Results</h1>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-sky-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-sky-900">Total Tests</h3>
                    <p class="text-2xl text-sky-600">{{ $totalTests }}</p>
                </div>
                
                <div class="bg-green-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-green-900">Passed Tests</h3>
                    <p class="text-2xl text-green-600">{{ $passedTests }}</p>
                </div>
                
                <div class="bg-{{ $passRate >= 80 ? 'green' : ($passRate >= 60 ? 'yellow' : 'red') }}-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-{{ $passRate >= 80 ? 'green' : ($passRate >= 60 ? 'yellow' : 'red') }}-900">Pass Rate</h3>
                    <p class="text-2xl text-{{ $passRate >= 80 ? 'green' : ($passRate >= 60 ? 'yellow' : 'red') }}-600">{{ number_format($passRate, 1) }}%</p>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full table-auto border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-sky-100">
                            <th class="border border-gray-300 px-4 py-2 text-left">Test Name</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Message</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Expected</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Actual</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Status</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Main Service</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Confidence</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $result)
                        <tr class="hover:bg-sky-50">
                            <td class="border border-gray-300 px-4 py-2">
                                <strong>{{ $result['test'] }}</strong>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <code class="bg-sky-100 px-2 py-1 rounded">{{ $result['message'] }}</code>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <span class="bg-sky-100 text-sky-800 px-2 py-1 rounded text-sm">
                                    {{ $result['expected'] }}
                                </span>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <span class="bg-{{ $result['passed'] ? 'green' : 'red' }}-100 text-{{ $result['passed'] ? 'green' : 'red' }}-800 px-2 py-1 rounded text-sm">
                                    {{ $result['actual_intent'] }}
                                </span>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                @if($result['passed'])
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">✓ PASS</span>
                                @else
                                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-sm">✗ FAIL</span>
                                @endif
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                @if($result['has_main_service'])
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">
                                        ✓ {{ $result['selected_service'] }}
                                    </span>
                                @else
                                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">✗ No service</span>
                                @endif
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <span class="text-sm">{{ number_format($result['confidence'], 2) }}</span>
                            </td>
                        </tr>
                        <tr class="bg-sky-50">
                            <td colspan="7" class="border border-gray-300 px-4 py-2">
                                <div class="text-sm">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <strong>Response:</strong>
                                            <div class="mt-1 p-2 bg-white border rounded max-h-32 overflow-y-auto">
                                                {!! $result['response'] !!}
                                            </div>
                                        </div>
                                        <div>
                                            <div class="mb-2">
                                                <strong>Selected Service:</strong>
                                                @if($result['selected_service'])
                                                    <code class="bg-sky-100 px-2 py-1 rounded text-xs">{{ $result['selected_service'] }}</code>
                                                @else
                                                    <span class="text-gray-500">None</span>
                                                @endif
                                            </div>
                                            
                                            @if(!empty($result['suggested_services']))
                                            <div class="mb-2">
                                                <strong>Suggested Services:</strong>
                                                <div class="flex flex-wrap gap-1 mt-1">
                                                    @foreach($result['suggested_services'] as $service)
                                                        <code class="bg-yellow-100 px-2 py-1 rounded text-xs">{{ $service }}</code>
                                                    @endforeach
                                                </div>
                                            </div>
                                            @endif
                                            
                                            <div>
                                                @if($result['has_required_data'])
                                                <span class="text-green-600 text-sm">
                                                    <strong>✓ Has Required Data</strong>
                                                </span>
                                                @else
                                                <span class="text-red-600 text-sm">
                                                    <strong>✗ No Required Data</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Test Analysis</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-semibold text-gray-900 mb-2">Key Improvements</h3>
                    <ul class="list-disc pl-5 space-y-1 text-sm text-gray-700">
                        <li>Added 55+ specific conditions for intent classification</li>
                        <li>Enhanced data validation with format checking</li>
                        <li>Improved distinction between inquiry and request</li>
                        <li>Added comprehensive field validation</li>
                        <li>Better error handling and user guidance</li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="font-semibold text-gray-900 mb-2">Expected Behavior</h3>
                    <ul class="list-disc pl-5 space-y-1 text-sm text-gray-700">
                        <li><strong>"کارت به شبا"</strong> → Service Inquiry (not request)</li>
                        <li><strong>"شماره کارت من..."</strong> → Service Request (with data)</li>
                        <li><strong>"چطور کار می‌کند"</strong> → General Question</li>
                        <li><strong>"سلام"</strong> → General Conversation</li>
                        <li>No false positive "all data received" messages</li>
                    </ul>
                </div>
            </div>
            
            @if($passRate >= 80)
            <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <h3 class="font-semibold text-green-900">✓ Test Results: EXCELLENT</h3>
                <p class="text-green-700">The AI chat system is working correctly and should handle the reported bug properly.</p>
            </div>
            @elseif($passRate >= 60)
            <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <h3 class="font-semibold text-yellow-900">⚠ Test Results: NEEDS IMPROVEMENT</h3>
                <p class="text-yellow-700">Some tests are failing. Review the failed cases and adjust the AI prompts.</p>
            </div>
            @else
            <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <h3 class="font-semibold text-red-900">✗ Test Results: POOR</h3>
                <p class="text-red-700">Many tests are failing. The AI system needs significant improvements.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection 