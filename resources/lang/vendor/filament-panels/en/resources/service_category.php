<?php

return [
    'label' => 'Service Category',
    'plural_label' => 'Service Categories',
    'navigation_group' => 'Service Management',
    'navigation_label' => 'Service Categories',
    
    'sections' => [
        'main_info' => 'Main Information',
        'styling' => 'Styling Settings',
        'background_icon' => 'Background Icon',
    ],
    
    'fields' => [
        'name' => 'Category Name',
        'slug' => 'Slug',
        'is_active' => 'Active',
        'display_order' => 'Display Order',
        'background_color' => 'Background Color',
        'border_color' => 'Border Color',
        'icon_color' => 'Icon Color',
        'hover_border_color' => 'Hover Border Color',
        'hover_background_color' => 'Hover Background Color',
        'background_icon' => 'Background Icon SVG Code',
        'background_image' => 'Background Image',
    ],
    
    'actions' => [
        'create' => 'Create New Category',
        'edit' => 'Edit Category',
        'delete' => 'Delete Category',
    ],
    
    'messages' => [
        'created' => 'Category created successfully.',
        'updated' => 'Category updated successfully.',
        'deleted' => 'Category deleted successfully.',
    ],
]; 