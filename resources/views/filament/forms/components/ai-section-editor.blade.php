<div x-data="{
    sections: @entangle($getStatePath()),
    editingSection: null,
    regeneratingSection: null,
    
    async regenerateSection(index) {
        if (this.regeneratingSection !== null) return;
        
        this.regeneratingSection = index;
        const section = this.sections[index];
        
        try {
            const response = await $wire.regenerateSection(index, section.heading);
            
            if (response.success) {
                this.sections[index] = response.section;
                new FilamentNotification()
                    .title('Section regenerated successfully')
                    .success()
                    .send();
            } else {
                new FilamentNotification()
                    .title('Failed to regenerate section')
                    .danger()
                    .send();
            }
        } catch (error) {
            new FilamentNotification()
                .title('Error regenerating section')
                .danger()
                .send();
        } finally {
            this.regeneratingSection = null;
        }
    },
    
    saveSection(index) {
        this.editingSection = null;
        $wire.set('{{ $getStatePath() }}', this.sections);
    },
    
    cancelEdit(index) {
        // Restore original content
        this.sections[index] = { ...this.sections[index] };
        this.editingSection = null;
    },
    
    addNewSection() {
        this.sections.push({
            heading: 'New Section',
            content: '<p>Section content here...</p>'
        });
        this.editingSection = this.sections.length - 1;
    },
    
    removeSection(index) {
        if (confirm('Are you sure you want to remove this section?')) {
            this.sections.splice(index, 1);
            $wire.set('{{ $getStatePath() }}', this.sections);
        }
    }
}" class="space-y-4">
    
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            Content Sections ({{ count($getState() ?? []) }})
        </h3>
        <button
            type="button"
            @click="addNewSection()"
            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
        >
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Section
        </button>
    </div>
    
    <template x-for="(section, index) in sections" :key="index">
        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 space-y-3">
            <!-- Section Header -->
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <template x-if="editingSection === index">
                        <input
                            type="text"
                            x-model="section.heading"
                            class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500"
                        />
                    </template>
                    <template x-if="editingSection !== index">
                        <h4 class="text-base font-medium text-gray-900 dark:text-white" x-text="section.heading"></h4>
                    </template>
                </div>
                
                <!-- Section Actions -->
                <div class="flex items-center space-x-2 ml-4">
                    <template x-if="editingSection === index">
                        <button
                            type="button"
                            @click="saveSection(index)"
                            class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                            title="Save changes"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </button>
                    </template>
                    
                    <template x-if="editingSection === index">
                        <button
                            type="button"
                            @click="cancelEdit(index)"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                            title="Cancel"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </template>
                    
                    <template x-if="editingSection !== index">
                        <button
                            type="button"
                            @click="editingSection = index"
                            class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300"
                            title="Edit section"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                    </template>
                    
                    <button
                        type="button"
                        @click="regenerateSection(index)"
                        :disabled="regeneratingSection === index"
                        class="text-amber-600 hover:text-amber-900 dark:text-amber-400 dark:hover:text-amber-300 disabled:opacity-50"
                        title="Regenerate with AI"
                    >
                        <svg class="w-5 h-5" :class="{ 'animate-spin': regeneratingSection === index }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </button>
                    
                    <button
                        type="button"
                        @click="removeSection(index)"
                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                        title="Remove section"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Section Content -->
            <div class="mt-3">
                <template x-if="editingSection === index">
                    <div>
                        <div 
                            x-data="{ content: section.content }"
                            x-init="
                                // Initialize a simple rich text editor
                                const editor = document.createElement('div');
                                editor.contentEditable = true;
                                editor.innerHTML = content;
                                editor.className = 'prose prose-sm max-w-none p-3 border border-gray-300 dark:border-gray-700 rounded-md min-h-[150px] dark:bg-gray-900 dark:text-white';
                                editor.addEventListener('input', () => {
                                    section.content = editor.innerHTML;
                                });
                                $el.appendChild(editor);
                            "
                        ></div>
                    </div>
                </template>
                
                <template x-if="editingSection !== index">
                    <div class="prose prose-sm max-w-none dark:prose-invert" x-html="section.content"></div>
                </template>
                
                <!-- Section Status (if available) -->
                <div x-show="$wire.sectionStatuses && $wire.sectionStatuses[section.heading]" class="mt-2 text-xs">
                    <span 
                        :class="{
                            'text-green-600 dark:text-green-400': $wire.sectionStatuses && $wire.sectionStatuses[section.heading] === 'completed',
                            'text-yellow-600 dark:text-yellow-400': $wire.sectionStatuses && $wire.sectionStatuses[section.heading] === 'generating',
                            'text-red-600 dark:text-red-400': $wire.sectionStatuses && $wire.sectionStatuses[section.heading] === 'failed'
                        }"
                        x-text="'Status: ' + ($wire.sectionStatuses ? $wire.sectionStatuses[section.heading] : 'unknown')"
                    ></span>
                </div>
            </div>
        </div>
    </template>
    
    <div x-show="!sections || sections.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
        No sections available. Click "Add Section" to create one.
    </div>
</div>