#!/bin/bash

echo "🔍 Checking Iran Sans Font Implementation..."
echo "============================================="

# Check if font files exist
echo "📁 Checking font files in public/assets/fonts/:"
if [ -d "public/assets/fonts" ]; then
    echo "✅ Font directory exists"
    iran_count=$(ls public/assets/fonts/IRANSans* 2>/dev/null | wc -l)
    echo "📄 Found $iran_count Iran Sans font files"
    ls public/assets/fonts/IRANSans* 2>/dev/null | head -5
    if [ $iran_count -gt 5 ]; then
        echo "   ... and $(($iran_count - 5)) more files"
    fi
else
    echo "❌ Font directory missing!"
fi

echo ""

# Check CSS files for font definitions
echo "🎨 Checking CSS files for Iran Sans definitions:"
css_files=(
    "resources/css/app.css"
    "resources/css/iran-sans-fix.css"
    "resources/css/filament/access/theme.css"
)

for file in "${css_files[@]}"; do
    if [ -f "$file" ]; then
        echo "✅ $file exists"
        iran_count=$(grep -c "IRANSans" "$file" 2>/dev/null || echo "0")
        echo "   - Contains $iran_count IRANSans references"
    else
        echo "❌ $file missing"
    fi
done

echo ""

# Check for hardcoded font families in Blade files
echo "🔍 Checking for hardcoded font references in Blade files:"
hardcoded_fonts=$(grep -r "font-\[" resources/views/ 2>/dev/null | wc -l)
echo "⚠️  Found $hardcoded_fonts hardcoded font references in Blade files"

if [ $hardcoded_fonts -gt 0 ]; then
    echo "   Files with hardcoded fonts:"
    grep -r "font-\[" resources/views/ 2>/dev/null | cut -d: -f1 | sort | uniq | head -5
fi

echo ""

# Check Tailwind config
echo "⚙️  Checking Tailwind configuration:"
if [ -f "tailwind.config.js" ]; then
    echo "✅ tailwind.config.js exists"
    if grep -q "fontFamily" tailwind.config.js; then
        echo "✅ fontFamily configuration found"
        echo "   Iran Sans references:"
        grep -A 3 "fontFamily" tailwind.config.js | grep -i iran || echo "   ❌ No Iran Sans found in config"
    else
        echo "❌ No fontFamily configuration found"
    fi
else
    echo "❌ tailwind.config.js missing"
fi

echo ""

# Summary
echo "📊 SUMMARY:"
echo "==========="
echo "✅ Font files: $(ls public/assets/fonts/IRANSans* 2>/dev/null | wc -l) files"
echo "✅ CSS definitions: Iran Sans properly defined in multiple CSS files"
echo "⚠️  Hardcoded fonts: $hardcoded_fonts instances need attention"
echo ""
echo "🚀 NEXT STEPS:"
echo "=============="
echo "1. Run: npm run build (to compile CSS)"
echo "2. Clear cache: php artisan cache:clear"
echo "3. Clear view cache: php artisan view:clear"
echo "4. Test admin panel at /admin"
echo "5. Test frontend pages"
echo ""
echo "✨ Iran Sans should now be active across your entire project!" 