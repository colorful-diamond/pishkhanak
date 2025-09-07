# Test Links for Pishkhanak.com Header and Sidebar Designs

## Overview
This document contains all the test links for the 30 header and sidebar designs created for pishkhanak.com.

## Main Test Page
- **Design Gallery**: `/test/designs` - Complete overview of all designs

## Header Designs (15 Total)

### Original Headers (1-5)
1. **Header 1 - Classic Layout**: `/test/header1`
2. **Header 2 - Minimalist Centered**: `/test/header2` 
3. **Header 3 - Modern Search-Focused**: `/test/header3`
4. **Header 4 - Split-Level Layout**: `/test/header4`
5. **Header 5 - Sidebar-Style Layout**: `/test/header5`

### New Headers (6-15)
6. **Header 6 - Floating Navigation**: `/test/header6`
7. **Header 7 - Vertical Sidebar Style**: `/test/header7`
8. **Header 8 - Compact Mobile-First**: `/test/header8`
9. **Header 9 - Multi-Row Layout**: `/test/header9`
10. **Header 10 - Animated Hover Effects**: `/test/header10`
11. **Header 11 - Glassmorphism Design**: `/test/header11`
12. **Header 12 - Tab-Style Navigation**: `/test/header12`
13. **Header 13 - Sticky Collapsible**: `/test/header13`
14. **Header 14 - Icon-Based Navigation**: `/test/header14`
15. **Header 15 - Full-Width Banner**: `/test/header15`

## Sidebar Designs (15 Total)

### Original Sidebars (1-5)
1. **Sidebar 1 - Card-Based Layout**: `/test/sidebar1`
2. **Sidebar 2 - List-Based with Expandable Sections**: `/test/sidebar2`
3. **Sidebar 3 - Icon-Heavy Layout**: `/test/sidebar3`
4. **Sidebar 4 - Minimal Layout**: `/test/sidebar4`
5. **Sidebar 5 - Dashboard-Style Layout**: `/test/sidebar5`

### New Sidebars (6-15)
6. **Sidebar 6 - Timeline-Style Navigation**: `/test/sidebar6`
7. **Sidebar 7 - Tabbed Interface**: `/test/sidebar7`
8. **Sidebar 8 - Search-Focused**: `/test/sidebar8`
9. **Sidebar 9 - Statistics Dashboard**: `/test/sidebar9`
10. **Sidebar 10 - Notification Center**: `/test/sidebar10`
11. **Sidebar 11 - Quick Action Buttons**: `/test/sidebar11`
12. **Sidebar 12 - Chat-Style Interface**: `/test/sidebar12`
13. **Sidebar 13 - Grid-Based Service Selector**: `/test/sidebar13`
14. **Sidebar 14 - Slide-Out Panels**: `/test/sidebar14`
15. **Sidebar 15 - Modern Card-Based Layout**: `/test/sidebar15`

## Design Features

### Header Design Features
- **Responsive Design**: All headers work on mobile and desktop
- **RTL Support**: Full right-to-left language support
- **Authentication States**: Different displays for logged-in vs guest users
- **Service Integration**: Links to existing service routes
- **Modern Styling**: Various themes including glassmorphism, animations, and gradients
- **Interactive Elements**: Hover effects, dropdowns, and navigation animations

### Sidebar Design Features
- **Service Categories**: Organized service navigation
- **User Profiles**: Personalized user information display
- **Statistics**: Dashboard-style metrics and data
- **Search Functionality**: Live search and filtering
- **Notifications**: Alert and notification systems
- **Quick Actions**: Shortcut buttons for common tasks
- **Chat Interface**: Interactive messaging components
- **Timeline Navigation**: Chronological navigation systems

## Technical Implementation
- **Framework**: Laravel Blade templates
- **Styling**: Tailwind CSS with custom components
- **Icons**: Font Awesome 6.0
- **JavaScript**: Interactive functionality and animations
- **Accessibility**: ARIA labels and keyboard navigation
- **Performance**: Optimized loading and rendering

## Usage Instructions
1. Visit the main design gallery at `/test/designs`
2. Browse through header and sidebar designs
3. Click on any design to test it individually
4. Use the external link icon to open designs in new tabs
5. Test responsive behavior by resizing browser window

## File Structure
```
pishkhanak.com/resources/views/front/partials/
├── header1.blade.php through header15.blade.php
├── sidebar1.blade.php through sidebar15.blade.php
└── front/test/
    ├── index.blade.php (Design Gallery)
    ├── header.blade.php (Header Test Template)
    └── sidebar.blade.php (Sidebar Test Template)
```

## Route Configuration
All test routes are configured in `routes/web.php` under the `test` prefix with appropriate naming conventions.

## Notes
- All designs maintain the sky blue color theme consistent with the main site
- Persian text and RTL layout are properly supported
- Service routes are integrated (card-iban, iban-account, iban-check, credit-score-rating)
- Authentication states are handled for both logged-in and guest users
- Mobile-first responsive design approach is used throughout 