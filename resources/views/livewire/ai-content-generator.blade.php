<div class="min-h-screen/2/2 bg-gradient-to-r from-sky-50 to-indigo-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8" dir="rtl">
    <div class="max-w-5xl w-full space-y-8">
        <div class="bg-white shadow-lg rounded-3xl p-8 lg:p-16">
            {{-- Progress Bar --}}
            @if ($currentStep > 1)
                <div class="mb-8 animate-fade-in">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-dark-sky-500">{{ __('ai_content.generation_progress') }}</span>
                        <span class="text-sm font-medium text-indigo-600">{{ $progress }}%</span>
                    </div>
                    <div class="w-full bg-sky-200 rounded-full h-2.5">
                        <div class="bg-indigo-600 h-2.5 rounded-full transition-all duration-500 ease-out"
                             style="width: {{ $progress }}%"></div>
                    </div>
                    <div class="mt-2 text-sm text-dark-sky-500">
                        @switch($currentStep)
                            @case(2)
                                {{ __('ai_content.generating_headings') }}
                                @break
                            @case(3)
                                {{ __('ai_content.creating_sections') }}
                                @break
                            @case(4)
                                {{ __('ai_content.generating_summary') }}
                                @break
                            @case(5)
                                {{ __('ai_content.creating_meta') }}
                                @break
                            @case(6)
                                {{ __('ai_content.generating_faq') }}
                                @break
                            @case(7)
                                {{ __('ai_content.generation_complete') }}
                                @break
                        @endswitch
                    </div>
                </div>
            @endif

            @if ($currentStep === 1)
                {{-- Initial Form --}}
                <form wire:submit.prevent="startGeneration" class="space-y-8 animate-fade-in">
                    <h2 class="text-3xl font-extrabold text-dark-sky-600 mb-6 text-right">{{ __('ai_content.ai_content_generator') }}</h2>

                    <div class="rounded-md shadow-sm -space-y-px">
                        {{-- Generation Type --}}
                        <div class="mb-4">
                            <label for="generation_type" class="block text-sm font-medium text-dark-sky-500 text-right">
                                {{ __('ai_content.generation_type') }}
                            </label>
                            <select id="generation_type" wire:model.live="generation_type" class="mt-1 block w-full pr-3 pl-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg transition-all duration-300 ease-in-out hover:border-indigo-400 text-right">
                                <option value="manual">{{ __('ai_content.manual_input') }}</option>
                                <option value="model">{{ __('ai_content.choose_model') }}</option>
                            </select>
                        </div>

                        @if ($generation_type === 'model')
                            {{-- Model Type --}}
                            <div class="mb-4">
                                <label for="model_name" class="block text-sm font-medium text-dark-sky-500 text-right">
                                    {{ __('ai_content.model_type') }}
                                </label>
                                <select
                                    id="model_name"
                                    wire:model.live="model_name"
                                    class="mt-1 block w-full pr-3 pl-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg transition-all duration-300 ease-in-out hover:border-indigo-400 text-right"
                                >
                                    <option value="">{{ __('ai_content.select_model_type') }}</option>
                                    @foreach($this->getAllowedModelNames() as $modelName)
                                        <option value="{{ $modelName }}">{{ __('ai_content.models.' . strtolower(str_replace(' ', '_', $modelName))) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- Searchable Field --}}
                            @if ($model_name && in_array($model_name, $this->getAllowedModelNames()))
                                <div class="mb-4 relative">
                                    <label for="search_title" class="block text-sm font-medium text-dark-sky-500 text-right">
                                        {{ __('ai_content.search_model_by_title', ['model' => $model_name]) }}
                                    </label>
                                    <div class="relative">
                                        <input
                                            type="text"
                                            id="search_title"
                                            wire:model.live.debounce.300ms="search_title"
                                            x-ref="searchInput"
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border border-gray-300 rounded-lg px-4 py-3 placeholder-gray-400 transition-all duration-300 ease-in-out hover:border-indigo-400 text-right"
                                            placeholder="{{ __('ai_content.search_placeholder', ['model' => $model_name]) }}"
                                        >
                                        @if($selectedResult)
                                            <div class="absolute left-2 top-1/2 -translate-y-1/2 flex items-center space-x-2 space-x-reverse">
                                                <span class="text-sm text-green-600">{{ __('ai_content.selected') }}</span>
                                                <button
                                                    type="button"
                                                    wire:click="clearSelectedResult"
                                                    x-on:click="$refs.searchInput.value = ''"
                                                    class="text-gray-400 hover:text-dark-sky-500 transition-colors duration-200"
                                                >
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Search Results Dropdown --}}
                                    @if ($searchResults && count($searchResults) > 0 && !$selectedResult)
                                        <div class="absolute z-50 w-full mt-1 bg-white rounded-lg shadow-lg border border-gray-200">
                                            <ul class="max-h-60 overflow-auto rounded-lg">
                                                @foreach ($searchResults as $result)
                                                    <li
                                                        wire:key="search-result-{{ $result->id }}"
                                                        wire:click="selectResult({{ $result->id }})"
                                                        class="px-4 py-2 hover:bg-sky-100 cursor-pointer transition-colors duration-200 text-sm text-dark-sky-500 flex items-center justify-between text-right"
                                                    >
                                                        <span class="text-indigo-600 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                                            ←
                                                        </span>
                                                        <span>{{ $result->{$this->getAllowedModelTitleField($model_name)} }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @elseif ($search_title && strlen($search_title) >= 2 && count($searchResults) === 0 && !$selectedResult)
                                        <div class="absolute z-50 w-full mt-1 bg-white rounded-lg shadow-lg border border-gray-200">
                                            <div class="px-4 py-2 text-sm text-gray-500 text-right">
                                                {{ __('ai_content.no_results_found') }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @else
                            {{-- Manual Input Title --}}
                            <div class="mb-4">
                                <label for="title" class="block text-sm font-medium text-dark-sky-500 flex items-center justify-end">
                                    <span class="text-red-500 mr-1">*</span>
                                    {{ __('ai_content.title') }}
                                </label>
                                <input type="text" id="title" wire:model.defer="title" required class="appearance-none rounded-lg relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-dark-sky-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm transition-all duration-300 ease-in-out hover:border-indigo-400 text-right" placeholder="{{ __('ai_content.enter_content_title') }}">
                                @error('title')
                                    <p class="mt-2 text-sm text-red-600 text-right">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Manual Input Content Description --}}
                            <div class="mb-4">
                                <label for="short_description" class="block text-sm font-medium text-dark-sky-500 flex items-center justify-end">
                                    <span class="text-red-500 mr-1">*</span>
                                    {{ __('ai_content.content_description') }}
                                </label>
                                <textarea id="short_description" wire:model.defer="short_description" rows="4" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border border-gray-300 rounded-lg px-4 py-3 placeholder-gray-400 resize-none transition-all duration-300 ease-in-out hover:border-indigo-400 text-right" placeholder="{{ __('ai_content.describe_what_generate') }}"></textarea>
                                @error('short_description')
                                    <p class="mt-2 text-sm text-red-600 text-right">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif
                    </div>

                    {{-- Additional Settings --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Content Language --}}
                        <div>
                            <label for="language" class="block text-sm font-medium text-dark-sky-500 mb-2 text-right">
                                {{ __('ai_content.content_language') }}
                            </label>
                            <select id="language" wire:model.defer="language" required
                                    class="mt-1 block w-full pr-3 pl-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg transition-all duration-300 ease-in-out
                                    hover:border-indigo-400 text-right">
                                <option value="">{{ __('ai_content.select_language') }}</option>
                                <option value="English">{{ __('ai_content.english') }}</option>
                                <option value="Arabic">{{ __('ai_content.arabic') }}</option>
                                <option value="Persian">{{ __('ai_content.persian') }}</option>
                            </select>
                            @error('language')
                                <p class="mt-2 text-sm text-red-600 text-right">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- AI Model Type --}}
                        <div>
                            <label for="model_type" class="block text-sm font-medium text-dark-sky-500 mb-2 text-right">
                                {{ __('ai_content.ai_model_type') }}
                            </label>
                            <select id="model_type" wire:model.defer="model_type" required
                                    class="mt-1 block w-full pr-3 pl-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg transition-all duration-300 ease-in-out
                                    hover:border-indigo-400 text-right">
                                <option value="fast">{{ __('ai_content.fast_generation') }}</option>
                                <option value="advanced">{{ __('ai_content.advanced_generation') }}</option>
                            </select>
                            @error('model_type')
                                <p class="mt-2 text-sm text-red-600 text-right">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Generation Mode --}}
                        <div>
                            <label for="generation_mode" class="block text-sm font-medium text-dark-sky-500 mb-2 text-right">
                                {{ __('ai_content.generation_mode') }}
                            </label>
                            <select id="generation_mode" wire:model.defer="generation_mode" required
                                    class="mt-1 block w-full pr-3 pl-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg transition-all duration-300 ease-in-out
                                    hover:border-indigo-400 text-right">
                                <option value="online">{{ __('ai_content.online_generation') }}</option>
                                <option value="offline">{{ __('ai_content.offline_generation') }}</option>
                            </select>
                            @error('generation_mode')
                                <p class="mt-2 text-sm text-red-600 text-right">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Number of Headings --}}
                        <div>
                            <label for="headings_number" class="block text-sm font-medium text-dark-sky-500 mb-2 text-right">
                                {{ __('ai_content.number_of_headings') }}
                            </label>
                            <select id="headings_number" wire:model.defer="headings_number" required
                                    class="mt-1 block w-full pr-3 pl-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg transition-all duration-300 ease-in-out
                                    hover:border-indigo-400 text-right">
                                @for($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}">{{ __('ai_content.headings_count', ['count' => $i]) }}</option>
                                @endfor
                            </select>
                            @error('headings_number')
                                <p class="mt-2 text-sm text-red-600 text-right">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Number of Sub-Headings --}}
                        <div>
                            <label for="sub_headings_number" class="block text-sm font-medium text-dark-sky-500 mb-2 text-right">
                                {{ __('ai_content.number_of_sub_headings') }}
                            </label>
                            <select id="sub_headings_number" wire:model.defer="sub_headings_number" required
                                    class="mt-1 block w-full pr-3 pl-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg transition-all duration-300 ease-in-out
                                    hover:border-indigo-400 text-right">
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ __('ai_content.sub_headings_count', ['count' => $i]) }}</option>
                                @endfor
                            </select>
                            @error('sub_headings_number')
                                <p class="mt-2 text-sm text-red-600 text-right">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Auto-Process Setting --}}
                        <div class="md:col-span-2">
                            <div class="flex items-center justify-between p-4 bg-sky-50 rounded-lg border border-gray-200">
                                <div class="text-xs text-gray-500">
                                    {{ __('ai_content.auto_process_description') }}
                                </div>
                                <div class="flex items-center">
                                    <label for="auto_process" class="mr-3 block text-sm font-medium text-dark-sky-500">
                                        {{ __('ai_content.auto_process_steps') }}
                                    </label>
                                    <input type="checkbox" id="auto_process" wire:model.defer="auto_process" 
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                </div>
                            </div>
                            <p class="mt-2 text-sm text-dark-sky-500 text-right">{{ __('ai_content.auto_process_help') }}</p>
                        </div>

                        {{-- Target Model Update Setting --}}
                        @if(isset($selectedResult))
                            <div class="md:col-span-2">
                                <div class="bg-green-50 rounded-lg border border-green-200 p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-sm font-medium text-green-900">به‌روزرسانی مدل انتخاب شده</h4>
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    
                                    <div class="space-y-3">
                                        <div class="text-sm text-green-700">
                                            <strong>مدل انتخاب شده:</strong> {{ class_basename($selectedResult) }} - {{ $selectedResult->title }}
                                        </div>
                                        
                                        <div class="flex items-center justify-between">
                                            <div class="text-sm text-dark-sky-500">
                                                آیا می‌خواهید محتوای تولید شده به صورت خودکار در این مدل ذخیره شود؟
                                            </div>
                                            <div class="flex items-center">
                                                <label for="update_target_after_completion" class="mr-3 block text-sm font-medium text-dark-sky-500">
                                                    به‌روزرسانی خودکار
                                    </label>
                                                <input type="checkbox" id="update_target_after_completion" wire:model.defer="update_target_after_completion" 
                                                       class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                                                       checked>
                                </div>
                                </div>
                            </div>
                                    
                                    <p class="mt-3 text-xs text-dark-sky-500 text-right">
                                        در صورت فعال‌سازی، محتوای تولید شده به صورت خودکار در فیلد محتوای این مدل ذخیره می‌شود.
                                    </p>
                                </div>
                            </div>
                        @endif

                        {{-- Image Generation Settings --}}
                        <div class="md:col-span-2">
                            <div class="bg-purple-50 rounded-lg border border-purple-200 p-4">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-sm font-medium text-purple-900">تنظیمات تولید تصویر</h4>
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    {{-- Image Quality --}}
                                    <div>
                                        <label for="image_quality" class="block text-sm font-medium text-dark-sky-500 mb-1 text-right">
                                            کیفیت تصویر
                                        </label>
                                        <select id="image_quality" wire:model.defer="imageGenerationSettings.image_quality" 
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm text-right">
                                            <option value="standard">استاندارد</option>
                                            <option value="hd">HD (کیفیت بالا)</option>
                                        </select>
                                    </div>

                                    {{-- Number of Images --}}
                                    <div>
                                        <label for="image_count" class="block text-sm font-medium text-dark-sky-500 mb-1 text-right">
                                            تعداد تصاویر هر بخش
                                        </label>
                                        <select id="image_count" wire:model.defer="imageGenerationSettings.image_count" 
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm text-right">
                                            <option value="2">2 تصویر</option>
                                            <option value="3">3 تصویر</option>
                                            <option value="4">4 تصویر</option>
                                            <option value="5">5 تصویر</option>
                                        </select>
                                    </div>

                                    {{-- Style Options --}}
                                    <div class="md:col-span-2">
                                        <label for="style_option" class="block text-sm font-medium text-dark-sky-500 mb-1 text-right">
                                            سبک تصویر
                                        </label>
                                        <select id="style_option" wire:model.live="imageGenerationSettings.style_option" 
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm text-right">
                                            @foreach($this->getStyleOptions() as $key => $option)
                                                <option value="{{ $key }}">{{ $option['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Custom Style Prompt (only if custom selected) --}}
                                    @if(isset($imageGenerationSettings['style_option']) && $imageGenerationSettings['style_option'] === 'custom')
                                        <div class="md:col-span-2">
                                            <label for="custom_style_prompt" class="block text-sm font-medium text-dark-sky-500 mb-1 text-right">
                                                پرامپت سبک دلخواه
                                            </label>
                                            <textarea id="custom_style_prompt" wire:model.defer="imageGenerationSettings.custom_style_prompt" 
                                                    rows="3" 
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm text-right resize-none"
                                                    placeholder="مثال: watercolor painting, soft colors, artistic style..."></textarea>
                                        </div>
                                    @endif

                                    {{-- Text Overlay Settings --}}
                                    <div class="md:col-span-2">
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="flex items-center">
                                                <input type="checkbox" id="add_text_overlay" wire:model.live="imageGenerationSettings.add_text_overlay" 
                                                       class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                                <label for="add_text_overlay" class="mr-2 text-sm font-medium text-dark-sky-500">
                                                    افزودن متن عنوان روی تصویر
                                                </label>
                                            </div>
                                        </div>
                                        
                                        @if(isset($imageGenerationSettings['add_text_overlay']) && $imageGenerationSettings['add_text_overlay'])
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                                                {{-- Text Position --}}
                                                <div>
                                                    <label for="text_overlay_position" class="block text-sm font-medium text-dark-sky-500 mb-1 text-right">
                                                        موقعیت متن
                                                    </label>
                                                    <select id="text_overlay_position" wire:model.defer="imageGenerationSettings.text_overlay_position" 
                                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm text-right">
                                                        <option value="top">بالا</option>
                                                        <option value="center">وسط</option>
                                                        <option value="bottom">پایین</option>
                                                    </select>
                                                </div>

                                                {{-- Text Style --}}
                                                <div>
                                                    <label for="text_overlay_style" class="block text-sm font-medium text-dark-sky-500 mb-1 text-right">
                                                        سبک متن
                                                    </label>
                                                    <select id="text_overlay_style" wire:model.defer="imageGenerationSettings.text_overlay_style" 
                                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm text-right">
                                                        <option value="dark">تیره (متن سفید)</option>
                                                        <option value="light">روشن (متن سیاه)</option>
                                                        <option value="gradient">گرادیان</option>
                                                    </select>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <p class="mt-3 text-xs text-dark-sky-500 text-right">
                                    تصاویر با استفاده از Google Imagen API تولید می‌شوند. هر بخش چندین تصویر تولید می‌کند و شما می‌توانید بهترین تصویر را انتخاب کنید.
                                </p>
                            </div>
                        </div>

                        {{-- Save as Default Settings --}}
                        <div class="md:col-span-2">
                            <div class="bg-sky-50 rounded-lg border border-sky-200 p-4">
                                <div class="flex items-center justify-between mb-3">
                                    @if($this->hasDefaultSettings())
                                        <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded-full">
                                            ✓ {{ __('ai_content.saved') }}
                                        </span>
                                    @endif
                                    <h4 class="text-sm font-medium text-sky-900">{{ __('ai_content.default_settings') }}</h4>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    @if($this->hasDefaultSettings())
                                        <button type="button" wire:click="clearDefaultSettings" 
                                                class="text-xs text-red-600 hover:text-red-800 hover:bg-red-50 px-2 py-1 rounded transition-colors duration-200"
                                                onclick="return confirm('{{ __('ai_content.confirm_clear_settings') }}')">
                                            {{ __('ai_content.clear_saved_settings') }}
                                        </button>
                                    @endif
                                    
                                    <div class="flex items-center">
                                        <label for="save_as_default" class="mr-3 block text-sm font-medium text-dark-sky-500">
                                            {{ __('ai_content.save_as_default') }}
                                        </label>
                                        <input type="checkbox" id="save_as_default" wire:model.defer="save_as_default" 
                                               class="h-4 w-4 text-sky-600 focus:ring-sky-500 border-gray-300 rounded">
                                    </div>
                                </div>
                                
                                <p class="mt-2 text-sm text-dark-sky-500 text-right">{{ __('ai_content.save_as_default_help') }}</p>
                                
                                @if($this->hasDefaultSettings())
                                    <div class="mt-3 p-3 bg-sky-100 rounded-lg">
                                        <p class="text-xs text-sky-700 text-right">
                                            <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ __('ai_content.settings_loaded_info') }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex justify-center">
                        <button type="submit"
                                class="group relative w-full flex justify-center py-3 px-6 border border-transparent text-lg font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition transform hover:scale-105 duration-300">
                            {{ __('ai_content.start_generation') }}
                            <svg class="w-6 h-6 text-white group-hover:text-indigo-200 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </button>
                    </div>
                </form>
            @endif

            {{-- Generation Steps Content --}}
            @if ($currentStep > 1 && $currentStep < 7)


                <div class="mt-8 bg-sky-50 p-6 rounded-xl shadow-inner animate-fade-in">
                    @switch($currentStep)
                        @case(2)
                            <div class="text-center">
                                <h3 class="text-2xl font-bold text-indigo-600 mb-4 animate-pulse">{{ __('ai_content.generating_content_headings') }}</h3>
                                <p class="text-dark-sky-500 mb-6">{{ __('ai_content.creating_structured_headings') }}</p>
                                
                                {{-- Real-time headings display --}}
                                <div class="max-w-2xl mx-auto">
                                    @if($headingGenerationStatus === 'generating')
                                        <div class="bg-indigo-100 rounded-lg p-6 mb-6">
                                            <div class="flex items-center justify-center mb-4">
                                                <svg class="w-8 h-8 text-indigo-500 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                </svg>
                                            </div>
                                            <p class="text-indigo-700 font-medium">{{ __('ai_content.ai_processing_headings') }}</p>
                                            <p class="text-indigo-600 text-sm mt-2">{{ __('ai_content.generating_headings_count', ['count' => $totalHeadingsToGenerate]) }}</p>
                                        </div>
                                    @endif
                                    
                                    @if($headingGenerationStatus === 'complete' && count($headings) > 0)
                                        <div class="bg-green-100 rounded-lg p-6 mb-6 transition-all duration-500 ease-in-out" data-headings-container>
                                            <div class="flex items-center justify-center mb-4">
                                                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </div>
                                            <p class="text-green-700 font-medium mb-4">{{ __('ai_content.headings_generated_successfully') }}</p>
                                            
                                            <div class="space-y-3" id="headings-sortable">
                                                @foreach($headings as $index => $heading)
                                                    <div class="heading-item bg-white rounded-lg p-4 shadow-sm transition-all duration-300 hover:shadow-md animate-fade-in border-r-4 @if($currentStep >= 3 && isset($sectionGenerationStatus[$index])) @if($sectionGenerationStatus[$index] === 'completed') border-green-500 @elseif($sectionGenerationStatus[$index] === 'generating') border-yellow-500 @elseif($sectionGenerationStatus[$index] === 'failed') border-red-500 @else border-gray-300 @endif @else border-indigo-500 @endif" 
                                                         style="animation-delay: {{ $index * 100 }}ms;" data-index="{{ $index }}">
                                                        
                                                        <div class="flex items-center justify-between">
                                                            <div class="flex items-center flex-1">
                                                                {{-- Status Icon --}}
                                                                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center mr-3 @if($currentStep >= 3 && isset($sectionGenerationStatus[$index])) @if($sectionGenerationStatus[$index] === 'completed') bg-green-100 @elseif($sectionGenerationStatus[$index] === 'generating') bg-yellow-100 @elseif($sectionGenerationStatus[$index] === 'failed') bg-red-100 @else bg-sky-100 @endif @else bg-indigo-100 @endif">
                                                                    @if($currentStep >= 3 && isset($sectionGenerationStatus[$index]))
                                                                        @if($sectionGenerationStatus[$index] === 'completed')
                                                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                                            </svg>
                                                                        @elseif($sectionGenerationStatus[$index] === 'generating')
                                                                            <svg class="w-5 h-5 text-yellow-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                                            </svg>
                                                                        @elseif($sectionGenerationStatus[$index] === 'failed')
                                                                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                                            </svg>
                                                                        @else
                                                                            <span class="text-dark-sky-500 font-semibold text-sm">{{ $index + 1 }}</span>
                                                                        @endif
                                                                    @else
                                                                        <span class="text-indigo-600 font-semibold text-sm">{{ $index + 1 }}</span>
                                                                    @endif
                                                                </div>
                                                                
                                                                {{-- Drag Handle --}}
                                                                @if($currentStep < 3)
                                                                    <div class="drag-handle cursor-move ml-3 text-gray-400 hover:text-dark-sky-500">
                                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                                                                        </svg>
                                                                    </div>
                                                                @endif
                                                                
                                                                {{-- Content --}}
                                                                <div class="flex-1">
                                                                    {{-- Heading Title (Editable) --}}
                                                                    @if($editingHeading === $index)
                                                                        <div class="flex items-center space-x-2">
                                                                                                                                                         <input type="text" 
                                                                                    wire:model.defer="editingText"
                                                                                    wire:keydown.enter="saveHeading"
                                                                                    wire:keydown.escape="cancelEditingHeading"
                                                                                    class="editing-input flex-1 px-3 py-2 border border-indigo-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-800 font-medium"
                                                                                    placeholder="{{ __('ai_content.enter_heading_title') }}"
                                                                                    autofocus>
                                                                            <button wire:click="saveHeading" 
                                                                                    class="p-2 text-green-600 hover:text-green-800 hover:bg-green-50 rounded-lg transition-colors duration-200"
                                                                                    title="{{ __('ai_content.save_heading') }}">
                                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                                                </svg>
                                                                            </button>
                                                                            <button wire:click="cancelEditingHeading" 
                                                                                    class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                                                                    title="{{ __('ai_content.cancel_edit') }}">
                                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                                                </svg>
                                                                            </button>
                                                                        </div>
                                                                    @else
                                                                        <div class="flex items-center justify-between group">
                                                                            @if($currentStep < 3)
                                                                                <button wire:click="startEditingHeading({{ $index }})" 
                                                                                        class="opacity-0 group-hover:opacity-100 p-1 text-gray-400 hover:text-indigo-600 transition-all duration-200"
                                                                                        title="{{ __('ai_content.edit_heading') }}">
                                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                                                    </svg>
                                                                                </button>
                                                                            @endif
                                                                            <h4 class="text-gray-800 font-medium text-right cursor-pointer hover:text-indigo-600 transition-colors duration-200"
                                                                                wire:click="startEditingHeading({{ $index }})"
                                                                                title="{{ __('ai_content.click_to_edit') }}">{{ $heading['title'] }}</h4>
                                                                        </div>
                                                                    @endif

                                                                    {{-- Sub-headings (Editable) --}}
                                                                    @if(isset($heading['sub_headlines']) && count($heading['sub_headlines']) > 0)
                                                                        <div class="mt-3 space-y-2">
                                                                            @foreach($heading['sub_headlines'] as $subIndex => $subHeading)
                                                                                @if($editingSubHeading !== null && $editingSubHeading[0] === $index && $editingSubHeading[1] === $subIndex)
                                                                                    <div class="flex items-center space-x-2 space-x-reverse mr-5">
                                                                                        <svg class="w-3 h-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                                                                        </svg>
                                                                                                                                                                                 <input type="text" 
                                                                                                wire:model.defer="editingText"
                                                                                                wire:keydown.enter="saveSubHeading"
                                                                                                wire:keydown.escape="cancelEditingSubHeading"
                                                                                                class="editing-input flex-1 px-2 py-1 border border-indigo-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                                                                                placeholder="{{ __('ai_content.enter_sub_heading_title') }}"
                                                                                                autofocus>
                                                                                        <button wire:click="saveSubHeading" 
                                                                                                class="p-1 text-green-600 hover:text-green-800 hover:bg-green-50 rounded transition-colors duration-200"
                                                                                                title="{{ __('ai_content.save_sub_heading') }}">
                                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                                                            </svg>
                                                                                        </button>
                                                                                        <button wire:click="cancelEditingSubHeading" 
                                                                                                class="p-1 text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition-colors duration-200"
                                                                                                title="{{ __('ai_content.cancel_edit') }}">
                                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                                                            </svg>
                                                                                        </button>
                                                                                    </div>
                                                                                @else
                                                                                    <div class="flex items-center text-sm text-dark-sky-500 group">
                                                                                        @if($currentStep < 3)
                                                                                            <div class="opacity-0 group-hover:opacity-100 flex items-center space-x-1 space-x-reverse transition-opacity duration-200">
                                                                                                <button wire:click="startEditingSubHeading({{ $index }}, {{ $subIndex }})" 
                                                                                                        class="p-1 text-gray-400 hover:text-indigo-600 transition-colors duration-200"
                                                                                                        title="{{ __('ai_content.edit_sub_heading') }}">
                                                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                                                                    </svg>
                                                                                                </button>
                                                                                                @if(count($heading['sub_headlines']) > 1)
                                                                                                    <button wire:click="removeSubHeading({{ $index }}, {{ $subIndex }})" 
                                                                                                            class="p-1 text-gray-400 hover:text-red-600 transition-colors duration-200"
                                                                                                            title="{{ __('ai_content.remove_sub_heading') }}"
                                                                                                            onclick="return confirm('{{ __('ai_content.confirm_remove_sub_heading') }}')">
                                                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                                                        </svg>
                                                                                                    </button>
                                                                                                @endif
                                                                                            </div>
                                                                                        @endif
                                                                                        <span class="flex-1 cursor-pointer hover:text-indigo-600 transition-colors duration-200 text-right"
                                                                                              wire:click="startEditingSubHeading({{ $index }}, {{ $subIndex }})"
                                                                                              title="{{ __('ai_content.click_to_edit') }}">{{ $subHeading }}</span>
                                                                                        <svg class="w-3 h-3 ml-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                                                                        </svg>
                                                                                    </div>
                                                                                @endif
                                                                            @endforeach
                                                                            
                                                                            {{-- Add Sub-heading Button --}}
                                                                            @if($currentStep < 3)
                                                                <div class="mr-5 text-right">
                                                                                    <button wire:click="addSubHeading({{ $index }})" 
                                                                                            class="flex items-center text-xs text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 px-2 py-1 rounded transition-colors duration-200">
                                                                        {{ __('ai_content.add_sub_heading') }}
                                                                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                                                        </svg>
                                                                                    </button>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            
                                                            {{-- Action Buttons (only show if not generating sections) --}}
                                                            @if($currentStep < 3)
                                                                <div class="flex items-center space-x-2 space-x-reverse mr-4">
                                                                    {{-- Remove --}}
                                                                    <button wire:click="removeHeading({{ $index }})" 
                                                                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                                                            title="Remove Heading"
                                                                            onclick="return confirm('Are you sure you want to remove this heading?')">
                                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                            </svg>
                                                                        </button>
                                                                    
                                                                    {{-- Rebuild --}}
                                                                    <button wire:click="rebuildHeading({{ $index }})" 
                                                                            class="p-2 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors duration-200"
                                                                            title="Rebuild Heading">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                                        </svg>
                                                                    </button>
                                                                    
                                                                    {{-- Move Down --}}
                                                                    @if($index < count($headings) - 1)
                                                                        <button wire:click="moveHeadingDown({{ $index }})" 
                                                                                class="p-2 text-gray-400 hover:text-sky-600 hover:bg-sky-50 rounded-lg transition-colors duration-200"
                                                                                title="Move Down">
                                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                                            </svg>
                                                                        </button>
                                                                    @endif
                                                                    
                                                                    {{-- Move Up --}}
                                                                    @if($index > 0)
                                                                        <button wire:click="moveHeadingUp({{ $index }})" 
                                                                                class="p-2 text-gray-400 hover:text-sky-600 hover:bg-sky-50 rounded-lg transition-colors duration-200"
                                                                                title="Move Up">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                                                        </svg>
                                                                    </button>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>
                                                        
                                                        {{-- Section Generation Status Text --}}
                                                        @if($currentStep >= 3 && isset($sectionGenerationStatus[$index]))
                                                            <div class="mt-3 pt-3 border-t border-gray-200">
                                                                <div class="text-sm">
                                                                    @if($sectionGenerationStatus[$index] === 'completed')
                                                                        <span class="text-green-600 font-medium">✓ Section generated successfully</span>
                                                                    @elseif($sectionGenerationStatus[$index] === 'generating')
                                                                        <span class="text-yellow-600 font-medium">🔄 Generating section content...</span>
                                                                    @elseif($sectionGenerationStatus[$index] === 'failed')
                                                                        <span class="text-red-600 font-medium">✗ Section generation failed</span>
                                                                    @else
                                                                        <span class="text-dark-sky-500 font-medium">⏳ Waiting for generation...</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                            
                                            {{-- Add New Heading Button --}}
                                            @if($currentStep < 3)
                                                <div class="mt-4 text-center">
                                                    <button wire:click="addNewHeading" 
                                                            class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors duration-300 shadow-sm hover:shadow-md">
                                                        {{ __('ai_content.add_new_heading') }}
                                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            @endif

                                            <div class="mt-4 text-center">
                                                <span class="text-green-600 text-sm font-medium">{{ __('ai_content.headings_count_generated', ['count' => $generatedHeadingsCount]) }}</span>
                                                @if($currentStep >= 3)
                                                    <div class="mt-2">
                                                        <span class="text-sky-600 text-sm">
                                                            بخش ها : {{ $completedSections }} از {{ count($headings) }} کامل شده است
                                                            @if($failedSections > 0), {{ $failedSections }} failed @endif
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- Manual Approval Controls (when auto-process is disabled) --}}
                                            @if($currentStep === 2 && !$auto_process)
                                                <div class="mt-6 pt-6 border-t border-green-200">
                                                    <div class="text-center">
                                                        <p class="text-dark-sky-500 text-sm mb-4">{{ __('ai_content.review_headings_before_proceeding') }}</p>
                                                        <div class="flex justify-center space-x-4 space-x-reverse">
                                                            <button wire:click="regenerateAllHeadings" 
                                                                    class="inline-flex items-center px-6 py-3 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-lg transition-colors duration-300">
                                                                {{ __('ai_content.regenerate_all_headings') }}
                                                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                                </svg>
                                                            </button>
                                                            <button wire:click="proceedToSections" 
                                                                    class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                                                                {{ __('ai_content.proceed_to_sections') }}
                                                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @break

                        @case(3)
                            <div class="text-center">
                                <h3 class="text-2xl font-semibold text-purple-600 mb-4">{{ __('ai_content.generating_content_sections') }}</h3>
                                <div class="max-w-2xl mx-auto">
                                    <div class="bg-purple-100 rounded-lg p-6 mb-6 transition-transform transform hover:scale-105 duration-300">
                                        <p class="text-dark-sky-500">{{ __('ai_content.creating_detailed_sections') }}</p>
                                        
                                        {{-- Show sections progress if available --}}
                                        @if($completedSections > 0 || $failedSections > 0)
                                            <div class="mt-4 mb-4">
                                                <div class="text-sm text-purple-700 font-medium">
                                                    Progress: {{ $completedSections }}/{{ count($headings) }} completed
                                                    @if($failedSections > 0), {{ $failedSections }} failed @endif
                                                </div>
                                            </div>
                                        @endif
                                        
                                        {{-- Show clickable headings with current status --}}
                                        <div class="mt-4 space-y-4">
                                            @if(count($headings) > 0)
                                                @foreach($headings as $index => $heading)
                                                    <div class="bg-white rounded-lg p-4 shadow-md transition-all duration-300 hover:shadow-xl border-l-4 @if(isset($sectionGenerationStatus[$index])) @if($sectionGenerationStatus[$index] === 'completed') border-green-500 @elseif($sectionGenerationStatus[$index] === 'generating') border-yellow-500 @elseif($sectionGenerationStatus[$index] === 'failed') border-red-500 @else border-gray-300 @endif @else border-purple-500 @endif">
                                                        
                                                        <div class="flex items-center justify-between">
                                                            <div class="flex items-center flex-1">
                                                                {{-- Status Icon --}}
                                                                @if(isset($sectionGenerationStatus[$index]))
                                                                    @if($sectionGenerationStatus[$index] === 'completed')
                                                                        <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                                        </svg>
                                                                    @elseif($sectionGenerationStatus[$index] === 'generating')
                                                                        <div class="relative mr-3">
                                                                            <svg class="w-5 h-5 text-yellow-500 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                                            </svg>
                                                                            {{-- Timeout indicator --}}
                                                                            @if(isset($sectionRetryCount[$index]) && $sectionRetryCount[$index] > 0)
                                                                                <span class="absolute -top-1 -right-1 bg-orange-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">
                                                                                    {{ $sectionRetryCount[$index] }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    @elseif($sectionGenerationStatus[$index] === 'failed')
                                                                        <svg class="w-5 h-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                                        </svg>
                                                                    @else
                                                                        <svg class="w-5 h-5 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                        </svg>
                                                                    @endif
                                                                @else
                                                                    <svg class="w-5 h-5 text-purple-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                                    </svg>
                                                                @endif
                                                                
                                                                {{-- Clickable Heading Title --}}
                                                                <button wire:click="clickHeading({{ $index }})" 
                                                                        class="flex-1 text-left text-gray-800 hover:text-indigo-600 transition-colors duration-200 focus:outline-none focus:text-indigo-600">
                                                                    {{ $heading['title'] }}
                                                                </button>
                                                            </div>
                                                            
                                                            {{-- Section Action Buttons --}}
                                                            <div class="flex items-center space-x-2">
                                                                {{-- Preview Button (only if completed) --}}
                                                                @if(isset($sectionGenerationStatus[$index]) && $sectionGenerationStatus[$index] === 'completed')
                                                                    <button wire:click="clickHeading({{ $index }})" 
                                                                            class="p-2 text-gray-400 hover:text-sky-600 hover:bg-sky-50 rounded-lg transition-colors duration-200"
                                                                            title="Preview Section">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                                        </svg>
                                                                    </button>
                                                                @endif
                                                                
                                                                {{-- Edit Button (only if completed) --}}
                                                                @if(isset($sectionGenerationStatus[$index]) && $sectionGenerationStatus[$index] === 'completed')
                                                                    <button wire:click="editSection({{ $index }})" 
                                                                            class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200"
                                                                            title="Edit Section">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                                        </svg>
                                                                    </button>
                                                                @endif
                                                                
                                                                {{-- Rebuild Button --}}
                                                                @if(isset($sectionGenerationStatus[$index]) && in_array($sectionGenerationStatus[$index], ['completed', 'failed']))
                                                                    <button wire:click="rebuildSection({{ $index }})" 
                                                                            class="p-2 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors duration-200"
                                                                            title="Rebuild Section">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                                        </svg>
                                                                    </button>
                                                                @endif
                                                                
                                                                {{-- Remove Button --}}
                                                                @if(isset($sectionGenerationStatus[$index]) && $sectionGenerationStatus[$index] !== 'generating')
                                                                    <button wire:click="removeSection({{ $index }})" 
                                                                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                                                            title="Remove Section"
                                                                            onclick="return confirm('Are you sure you want to remove this section?')">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                        </svg>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        
                                                        {{-- Status Text --}}
                                                        <div class="mt-2 text-sm">
                                                            @if(isset($sectionGenerationStatus[$index]))
                                                                @if($sectionGenerationStatus[$index] === 'completed')
                                                                    <span class="text-green-600 font-medium">✓ برای پیش‌نمایش محتوا کلیک کنید</span>
                                                                @elseif($sectionGenerationStatus[$index] === 'generating')
                                                                    <span class="text-yellow-600 font-medium">🔄 در حال تولید محتوا... (حداکثر 90 ثانیه)</span>
                                                                    @if(isset($sectionRetryCount[$index]) && $sectionRetryCount[$index] > 0)
                                                                        <span class="text-orange-600 ml-2">(تلاش مجدد {{ $sectionRetryCount[$index] }}/{{ $maxRetries }})</span>
                                                                    @endif
                                                                @elseif($sectionGenerationStatus[$index] === 'failed')
                                                                    <span class="text-red-600 font-medium">✗ تولید محتوا شکست خورد - برای تلاش مجدد روی بازسازی کلیک کنید</span>
                                                                @else
                                                                    <span class="text-dark-sky-500 font-medium">⏳ در انتظار تولید محتوا...</span>
                                                                @endif
                                                            @else
                                                                <span class="text-purple-600 font-medium">📝 آماده برای تولید محتوا</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                        
                                        {{-- Manual Approval Controls for Sections (when not auto-process and all completed) --}}
                                        @if(!$auto_process && $completedSections >= count($headings) && count($headings) > 0)
                                            <div class="mt-6 pt-6 border-t border-purple-200">
                                                <div class="text-center">
                                                    <p class="text-dark-sky-500 text-sm mb-4">{{ __('ai_content.all_sections_completed') }}</p>
                                                    <button wire:click="proceedToSummary" 
                                                            class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                                        </svg>
                                                        {{ __('ai_content.proceed_to_summary') }}
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @break

                        @case(4)
                            <div class="text-center">
                                <h3 class="text-2xl font-semibold text-green-600 mb-4">{{ __('ai_content.generating_content_summary') }}</h3>
                                <div class="max-w-2xl mx-auto">
                                    <div class="bg-green-100 rounded-lg p-6 mb-6 animate-pulse">
                                        <div class="flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <p class="text-dark-sky-500">{{ __('ai_content.creating_comprehensive_summary') }}</p>
                                    </div>
                                </div>
                            </div>
                            @break

                        @case(5)
                            <div class="text-center">
                                <h3 class="text-2xl font-semibold text-yellow-500 mb-4">{{ __('ai_content.generating_meta_schema') }}</h3>
                                <div class="max-w-2xl mx-auto">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                        {{-- Meta Information --}}
                                        <div class="bg-sky-100 rounded-lg p-6 transition-transform transform hover:scale-105 duration-300">
                                            <h4 class="text-lg font-semibold text-sky-800 mb-3">{{ __('ai_content.meta_information') }}</h4>
                                            <div class="space-y-4">
                                                <div class="bg-white rounded-md p-4 shadow-sm animate-pulse">
                                                    <div class="h-3 bg-sky-200 rounded w-3/4"></div>
                                                    <div class="h-2 bg-sky-200 rounded w-1/2 mt-2"></div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Schema Markup --}}
                                        <div class="bg-green-100 rounded-lg p-6 transition-transform transform hover:scale-105 duration-300">
                                            <h4 class="text-lg font-semibold text-green-800 mb-3">{{ __('ai_content.schema_markup') }}</h4>
                                            <div class="space-y-4">
                                                <div class="bg-white rounded-md p-4 shadow-sm animate-pulse">
                                                    <div class="h-3 bg-green-200 rounded w-2/3"></div>
                                                    <div class="h-2 bg-green-200 rounded w-1/2 mt-2"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @break
                        @case(6)
                            <div class="text-center">
                                <h3 class="text-2xl font-semibold text-amber-500 mb-4">{{ __('ai_content.generating_faq_section') }}</h3>
                                <div class="max-w-2xl mx-auto">
                                    <div class="bg-amber-100 rounded-lg p-6 mb-6 transition-transform transform hover:scale-105 duration-300">
                                        <div class="flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <p class="text-dark-sky-500">{{ __('ai_content.generating_faqs') }}</p>
                                        <div class="mt-6 space-y-6">
                                            @if(isset($aiContent) && $aiContent->faqs)
                                                @foreach($aiContent->faqs as $faq)
                                                    <div class="bg-white rounded-lg p-4 shadow-md transition-shadow duration-300 hover:shadow-lg">
                                                        <h4 class="font-semibold text-gray-800 mb-2">{{ $faq['question'] }}</h4>
                                                        <p class="text-dark-sky-500">{{ $faq['answer'] }}</p>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @break
                    @endswitch
                </div>
            @endif

            {{-- Success State --}}
            @if ($currentStep === 7)
                <div class="space-y-8 animate-fade-in">
                    {{-- Success Header --}}
                    <div class="text-center bg-green-50 dark:bg-green-900/50 rounded-3xl p-8 border border-green-200 dark:border-green-700">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 dark:bg-green-800 mb-6 transition-transform transform hover:scale-105 duration-300">
                            <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-green-900 dark:text-green-400 mb-2">{{ __('ai_content.generation_complete_title') }}</h3>
                        <p class="text-green-600 dark:text-green-400 mb-6">{{ __('ai_content.generation_success_message') }}</p>
                    </div>

                    {{-- Content Preview --}}
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h4 class="text-xl font-semibold text-gray-800">{{ __('ai_content.generated_content_preview') }}</h4>
                            <button
                                wire:click="copyContent"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors duration-300">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                                </svg>
                                {{ __('ai_content.copy_content') }}
                            </button>
                        </div>

                        <div class="space-y-6 max-h-[600px] overflow-y-auto px-4" id="generated-content">
                            {{-- Title --}}
                            <h1 class="text-3xl font-bold text-dark-sky-600">{{ $title }}</h1>

                            {{-- Meta Description --}}
                            <div class="bg-sky-50 rounded-lg p-4 border border-gray-200">
                                <h5 class="text-sm font-medium text-dark-sky-500 mb-2">{{ __('ai_content.meta_description_label') }}</h5>
                                <p class="text-dark-sky-500">{{ $meta['description'] ?? '' }}</p>
                            </div>

                            {{-- Main Content --}}
                            @if(isset($aiContent) && $aiContent->ai_sections)
                                @foreach($aiContent->ai_headings as $index => $heading)
                                    <div class="space-y-4">
                                        <h2 class="text-2xl font-semibold text-gray-800">{{ $heading['title'] }}</h2>
                                        <div class="prose prose-indigo max-w-none">
                                            {!! nl2br(e($aiContent->ai_sections[$index] ?? '')) !!}
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            {{-- FAQ Section --}}
                            @if(isset($aiContent) && $aiContent->faq)
                                <div class="mt-8">
                                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">{{ __('ai_content.frequently_asked_questions') }}</h2>
                                    <div class="space-y-4">
                                        @foreach($aiContent->faq as $faq)
                                            <div class="bg-sky-50 rounded-lg p-4">
                                                <h3 class="text-lg font-medium text-dark-sky-600 mb-2">{{ $faq['question'] }}</h3>
                                                <p class="text-dark-sky-500">{{ $faq['answer'] }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex justify-center space-x-4">
                        <button wire:click="resetGenerator"
                                class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition transform hover:scale-105 duration-300 shadow-lg">
                            {{ __('ai_content.generate_new_content') }}
                        </button>
                        <button wire:click="exportContent"
                                class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition transform hover:scale-105 duration-300 shadow-lg">
                            {{ __('ai_content.export_content') }}
                        </button>
                    </div>
                </div>
            @endif
        </div>

        {{-- Section Preview Modal --}}
        @if($showSectionPreview)
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="section-preview-modal">
                <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                        {{-- Modal Header --}}
                        <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-dark-sky-600">
                                پیش‌نمایش بخش: {{ $headings[$selectedSection]['title'] ?? 'بخش ' . ($selectedSection + 1) }}
                            </h3>
                            <button wire:click="closeSectionPreview" 
                                    class="text-gray-400 hover:text-dark-sky-500 transition-colors duration-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        
                        {{-- Modal Content --}}
                        <div class="mt-4 max-h-96 overflow-y-auto">
                            <div class="prose prose-indigo max-w-none">
                                {!! $sectionPreview !!}
                            </div>
                        </div>
                        
                        {{-- Modal Actions --}}
                        <div class="flex items-center justify-end pt-4 border-t border-gray-200 space-x-3">
                            <button wire:click="editSection({{ $selectedSection }})" 
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors duration-300">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                ویرایش بخش
                            </button>
                            <button wire:click="rebuildSection({{ $selectedSection }})" 
                                    class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors duration-300">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                بازسازی
                            </button>
                            <button wire:click="closeSectionPreview" 
                                    class="inline-flex items-center px-4 py-2 bg-sky-300 hover:bg-gray-400 text-dark-sky-500 text-sm font-medium rounded-lg transition-colors duration-300">
                                بستن
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Section Editing Modal --}}
        @if($editingSection !== null)
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="section-edit-modal">
                <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-4/5 lg:w-3/4 shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                        {{-- Modal Header --}}
                        <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-dark-sky-600">
                                ویرایش بخش: {{ $headings[$editingSection]['title'] ?? 'بخش ' . ($editingSection + 1) }}
                            </h3>
                            <button wire:click="cancelSectionEdit" 
                                    class="text-gray-400 hover:text-dark-sky-500 transition-colors duration-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        
                        {{-- Modal Content --}}
                        <div class="mt-4">
                            <label for="section-content" class="block text-sm font-medium text-dark-sky-500 mb-2">
                                محتوای بخش
                            </label>
                            <textarea 
                                id="section-content"
                                wire:model.defer="editingSectionContent"
                                rows="15"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none font-mono text-sm"
                                placeholder="محتوای بخش را وارد کنید..."></textarea>
                            @error('editingSectionContent')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        {{-- Modal Actions --}}
                        <div class="flex items-center justify-end pt-4 border-t border-gray-200 space-x-3">
                            <button wire:click="saveSectionEdit" 
                                    class="inline-flex items-center px-6 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-300">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                ذخیره تغییرات
                            </button>
                            <button wire:click="cancelSectionEdit" 
                                    class="inline-flex items-center px-4 py-2 bg-sky-300 hover:bg-gray-400 text-dark-sky-500 text-sm font-medium rounded-lg transition-colors duration-300">
                                لغو
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <style>
            /* RTL Support */
            [dir="rtl"] {
                direction: rtl;
                text-align: right;
            }
            
            [dir="rtl"] .space-x-2 > * + * {
                margin-right: 0.5rem;
                margin-left: 0;
            }
            
            [dir="rtl"] .space-x-reverse > * + * {
                margin-left: 0.5rem;
                margin-right: 0;
            }

            @keyframes fade-in {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .animate-fade-in {
                animation: fade-in 0.5s ease-out forwards;
            }

            /* Sortable.js styles */
            .sortable-ghost {
                opacity: 0.4;
                background-color: #f3f4f6;
            }

            .sortable-chosen {
                opacity: 0.8;
            }

            .sortable-drag {
                opacity: 0.6;
                transform: rotate(2deg);
            }

            .drag-handle:hover {
                background-color: #f9fafb;
                border-radius: 4px;
            }

            /* Editing states */
            .editing-input {
                border: 2px solid #6366f1 !important;
                box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1) !important;
            }

            .editing-input:focus {
                border-color: #4f46e5 !important;
                box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2) !important;
            }

            /* Hover effects for editable items */
            .editable-item {
                transition: all 0.2s ease-in-out;
            }

            .editable-item:hover {
                background-color: #f8fafc;
                border-radius: 4px;
                padding: 2px 4px;
                margin: -2px -4px;
            }
        </style>

        <!-- SortableJS Library -->
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
        
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('contentGenerated', () => {
                    // Scroll to content section smoothly
                    const contentSection = document.querySelector('#content-section');
                    if (contentSection) {
                        contentSection.scrollIntoView({ behavior: 'smooth' });
                    }
                });

                // Handle heading generation updates
                Livewire.on('start-heading-generation', () => {
                    console.log('Starting heading generation...');
                    // Delay the actual generation to allow UI to update
                    setTimeout(() => {
                        @this.call('performHeadingGeneration');
                    }, 100);
                });

                Livewire.on('headings-generated', () => {
                    console.log('Headings generated!');
                    // Animate heading display and scroll to view
                    const headingsContainer = document.querySelector('[data-headings-container]');
                    if (headingsContainer) {
                        headingsContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    
                    // Initialize drag and drop
                    initializeDragAndDrop();
                    
                    // Only auto-proceed if auto_process is enabled
                    if (@this.auto_process) {
                        // Wait 3 seconds to show the headings, then proceed
                        setTimeout(() => {
                            @this.call('proceedToSections');
                        }, 3000);
                    }
                    // If auto_process is disabled, the user will need to click "Proceed to Sections" button
                });

                // Handle section generation monitoring with timeout
                Livewire.on('start-section-monitoring', () => {
                    console.log('Starting section monitoring...');
                    // Start polling for section progress with timeout handling
                    startSectionMonitoringWithTimeout();
                });

                Livewire.on('continue-section-monitoring', () => {
                    // Continue monitoring after a delay
                    setTimeout(() => {
                        @this.call('checkSectionProgress');
                    }, 2000);
                });

                // Listen for progress updates
                Livewire.on('progress-updated', (data) => {
                    console.log('Progress updated:', data);
                    if (data.progress !== undefined) {
                        const progressBar = document.querySelector('.bg-indigo-600');
                        if (progressBar) {
                            progressBar.style.width = data.progress + '%';
                        }
                        
                        // Update progress text
                        const progressText = document.querySelector('.text-indigo-600');
                        if (progressText) {
                            progressText.textContent = Math.round(data.progress) + '%';
                        }
                    }
                });

                Livewire.on('monitor-section-rebuild', (data) => {
                    console.log('Monitoring section rebuild:', data);
                    // Start timeout monitoring for specific section
                    startSectionTimeoutMonitoring(data.index);
                });

                Livewire.on('section-status-updated', (data) => {
                    console.log('Section status updated:', data);
                    // Add visual feedback for status changes
                    const sectionElement = document.querySelector(`[data-section-index="${data.index}"]`);
                    if (sectionElement) {
                        sectionElement.classList.add('animate-pulse');
                        setTimeout(() => {
                            sectionElement.classList.remove('animate-pulse');
                        }, 1000);
                    }
                    
                    // Update progress bar if progress is provided
                    if (data.progress !== undefined) {
                        const progressBar = document.querySelector('.bg-indigo-600');
                        if (progressBar) {
                            progressBar.style.width = data.progress + '%';
                        }
                        
                        // Update progress text
                        const progressText = document.querySelector('.text-indigo-600');
                        if (progressText) {
                            progressText.textContent = Math.round(data.progress) + '%';
                        }
                    }
                });

                Livewire.on('show-section-preview', (data) => {
                    console.log('Showing section preview:', data);
                    // Scroll to modal and focus
                    setTimeout(() => {
                        const modal = document.getElementById('section-preview-modal');
                        if (modal) {
                            modal.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    }, 100);
                });

                Livewire.on('start-section-edit', (data) => {
                    console.log('Starting section edit:', data);
                    // Focus on textarea after modal opens
                    setTimeout(() => {
                        const textarea = document.getElementById('section-content');
                        if (textarea) {
                            textarea.focus();
                            // Move cursor to end
                            textarea.setSelectionRange(textarea.value.length, textarea.value.length);
                        }
                    }, 100);
                });

                Livewire.on('sections-completed', (data) => {
                    console.log('All sections completed:', data);
                    // Show a success notification
                    if (data.message) {
                        // Create a notification or update UI to show completion
                        const notification = document.createElement('div');
                        notification.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform transition-all duration-500 translate-y-0 opacity-100 z-50';
                        notification.textContent = data.message;
                        document.body.appendChild(notification);
                        
                        setTimeout(() => {
                            notification.style.opacity = '0';
                            notification.style.transform = 'translateY(100%)';
                            setTimeout(() => notification.remove(), 500);
                        }, 4000);
                    }
                });

                // Function to initialize drag and drop
                function initializeDragAndDrop() {
                    const sortableContainer = document.getElementById('headings-sortable');
                    if (sortableContainer && typeof Sortable !== 'undefined') {
                        new Sortable(sortableContainer, {
                            handle: '.drag-handle',
                            animation: 150,
                            ghostClass: 'sortable-ghost',
                            chosenClass: 'sortable-chosen',
                            dragClass: 'sortable-drag',
                            onEnd: function(evt) {
                                // Get the new order
                                const items = sortableContainer.querySelectorAll('.heading-item');
                                const orderedIndexes = Array.from(items).map(item => 
                                    parseInt(item.getAttribute('data-index'))
                                );
                                
                                // Update the component
                                @this.call('updateHeadingsOrder', orderedIndexes);
                            }
                        });
                    }
                }

                // Function to start section monitoring with timeout handling
                function startSectionMonitoringWithTimeout() {
                    // Initial check
                    setTimeout(() => {
                        @this.call('checkSectionProgress');
                    }, 1000);
                }

                // Function to monitor timeout for specific section
                function startSectionTimeoutMonitoring(sectionIndex) {
                    const timeoutDuration = 60000; // 90 seconds
                    const checkInterval = 5000; // Check every 5 seconds
                    
                    const timeoutMonitor = setInterval(() => {
                        // Check if section is still generating
                        const sectionStatus = @this.sectionGenerationStatus[sectionIndex];
                        if (sectionStatus !== 'generating') {
                            clearInterval(timeoutMonitor);
                            return;
                        }
                        
                        // Check for timeout
                        @this.call('checkSectionTimeouts').then((timeoutOccurred) => {
                            if (timeoutOccurred) {
                                console.log(`Timeout occurred for section ${sectionIndex}`);
                            }
                        });
                    }, checkInterval);
                    
                    // Clear monitor after maximum possible time
                    setTimeout(() => {
                        clearInterval(timeoutMonitor);
                    }, timeoutDuration + 10000); // Add buffer time
                }

                // Handle global keyboard shortcuts for editing
                document.addEventListener('keydown', function(e) {
                    // Handle escape key globally to cancel any active editing
                    if (e.key === 'Escape') {
                        // Let Livewire handle this
                        return;
                    }
                    
                    // Handle Enter key for saving when in editing mode
                    if (e.key === 'Enter' && e.target.classList.contains('editing-input')) {
                        e.preventDefault();
                        // Let Livewire handle this
                        return;
                    }
                });

                // Auto-focus on editing inputs when they appear
                document.addEventListener('livewire:updated', function() {
                    const editingInput = document.querySelector('.editing-input');
                    if (editingInput) {
                        editingInput.focus();
                        editingInput.select(); // Select all text for easy replacement
                    }
                });

                Livewire.on('showNotification', (message) => {
                    // Create notification element
                    const notification = document.createElement('div');
                    notification.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform transition-all duration-500 translate-y-0 opacity-100';
                    notification.textContent = message;

                    // Add to DOM
                    document.body.appendChild(notification);

                    // Remove after animation
                    setTimeout(() => {
                        notification.style.opacity = '0';
                        notification.style.transform = 'translateY(100%)';
                        setTimeout(() => {
                            notification.remove();
                        }, 500);
                    }, 3000);
                });

                Livewire.on('exportStarted', () => {
                    // Show loading state
                    const exportButton = document.querySelector('[wire\\:click="exportContent"]');
                    if (exportButton) {
                        exportButton.disabled = true;
                        exportButton.innerHTML = '<span class="animate-pulse-custom">Exporting...</span>';
                    }
                });

                Livewire.on('exportFinished', () => {
                    // Reset button state
                    const exportButton = document.querySelector('[wire\\:click="exportContent"]');
                    if (exportButton) {
                        exportButton.disabled = false;
                        exportButton.innerHTML = 'Export Content';
                    }
                });

                document.addEventListener('copyContent', function(event) {
                    const content = event.detail.content;
                    navigator.clipboard.writeText(content);
                });

                Livewire.on('resultSelected', () => {
                    // Add subtle highlight animation to the input
                    const searchInput = document.getElementById('search_title');
                    if (searchInput) {
                        searchInput.classList.add('ring-2', 'ring-green-500', 'border-green-500');
                        setTimeout(() => {
                            searchInput.classList.remove('ring-2', 'ring-green-500', 'border-green-500');
                        }, 1500);
                    }
                });
            });
        </script>
        @include('filament.components.tools.live-events')
    </div>
</div>
@push("styles")
    <style>
        /* Fade-in Animation */
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in {
            animation: fade-in 0.6s ease-out forwards;
        }

        /* Pulsing Effect */
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .animate-pulse-custom {
            animation: pulse 2s infinite;
        }

        /* Add smooth transitions for the selected state */
        #search_title {
            transition: all 0.3s ease-in-out;
        }

        .selected-result-animation {
            animation: selectFade 0.3s ease-out forwards;
        }

        @keyframes selectFade {
            from { background-color: rgba(16, 185, 129, 0.1); }
            to { background-color: transparent; }
        }
    </style>
@endpush
