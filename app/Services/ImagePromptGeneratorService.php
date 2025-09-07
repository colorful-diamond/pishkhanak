<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class ImagePromptGeneratorService
{
    protected $aiService;
    
    // Professional styles only - appropriate for business/service content
    protected $artStyles = [
        'photorealistic' => 'photorealistic, professional photography, sharp focus, high resolution',
        'corporate_modern' => 'modern corporate style, clean professional, business aesthetic',
        'tech_professional' => 'professional tech visualization, clean modern design, sophisticated',
        'business_elegant' => 'elegant business style, premium quality, sophisticated presentation',
        'commercial_clean' => 'commercial photography style, bright and clean, professional lighting',
        'office_professional' => 'professional office environment, modern workspace, business setting',
        'service_showcase' => 'service industry showcase, professional presentation, clear focus',
        'digital_professional' => 'professional digital rendering, clean modern aesthetic, high quality',
    ];

    protected $compositionRules = [
        'rule_of_thirds' => 'rule of thirds composition',
        'golden_ratio' => 'golden ratio composition',
        'centered' => 'centered composition with symmetry',
        'diagonal' => 'diagonal composition with dynamic flow',
        'frame_within_frame' => 'frame within frame composition',
    ];

    protected $lightingStyles = [
        'golden_hour' => 'golden hour lighting, warm glow, soft shadows, sunset colors',
        'blue_hour' => 'blue hour lighting, twilight atmosphere, cool tones, dusk ambiance',
        'studio' => 'studio lighting, professional, even illumination, softbox lighting',
        'dramatic' => 'dramatic lighting, high contrast, deep shadows, cinematic mood',
        'ambient' => 'ambient lighting, soft diffused light, natural, gentle illumination',
        'neon' => 'neon lighting, vibrant glow, urban night, electric atmosphere',
        'backlight' => 'backlit, silhouette effect, rim lighting, ethereal glow',
        'morning' => 'morning light, fresh atmosphere, soft sunlight, peaceful mood',
        'cloudy' => 'overcast lighting, soft shadows, muted colors, even distribution',
        'spotlight' => 'spotlight effect, focused beam, dramatic highlight, stage lighting',
    ];

    protected $perspectiveOptions = [
        'wide_angle' => 'wide angle lens, expansive view, 24mm focal length',
        'aerial' => 'aerial view, bird\'s eye perspective, top-down angle',
        'eye_level' => 'eye level perspective, natural viewpoint, human perspective',
        'low_angle' => 'low angle shot, heroic perspective, looking upward',
        'macro' => 'macro photography, extreme close-up detail, magnified view',
        'panoramic' => 'panoramic view, wide landscape, 180 degree view',
        'dutch_angle' => 'dutch angle, tilted perspective, dynamic composition',
        'three_quarter' => 'three-quarter view, angled perspective, dimensional depth',
        'frontal' => 'straight-on view, symmetrical composition, direct angle',
        'profile' => 'side view, profile angle, lateral perspective',
    ];

    public function __construct(AiService $aiService = null)
    {
        $this->aiService = $aiService ?: app(AiService::class);
    }

    // Track used combinations to avoid repetition
    protected static $usedCombinations = [];
    protected static $sectionCounter = 0;
    
    /**
     * Generate an optimized image prompt based on title and description
     * Returns array with prompt and metadata
     */
    public function generateImagePrompt(string $title, string $description = '', array $options = []): array
    {
        // Skip AI enhancement and use our direct prompts for better variety
        $useDirectPrompt = $options['use_direct_prompt'] ?? true;
        
        if ($useDirectPrompt) {
            // Generate direct prompt without AI interference
            return $this->generateDirectPrompt($title, $description, $options);
        }
        
        // Original AI-enhanced flow (kept for backward compatibility)
        $promptRequest = $this->buildPromptTemplate($title, $description, $options);
        
        try {
            // Use AI to enhance and optimize the prompt
            $response = $this->aiService->generateContent(
                'Image Prompt Generation',
                $promptRequest,
                'English',
                'fast',
                false,
                0,
                ['max_tokens' => 500, 'response_format' => 'json']
            );

            if ($response) {
                // Try to extract JSON if AI returns it
                $jsonMatch = [];
                if (preg_match('/\{.*\}/s', $response, $jsonMatch)) {
                    $decoded = json_decode($jsonMatch[0], true);
                    if (json_last_error() === JSON_ERROR_NONE && isset($decoded['prompt'])) {
                        return $decoded;
                    }
                }
                
                // Otherwise clean and structure the response
                $cleanedPrompt = $this->cleanPrompt($response);
                return [
                    'prompt' => $cleanedPrompt,
                    'style' => $options['style'] ?? $this->getRandomStyle(),
                    'color_palette' => $this->determineColorPalette($title),
                    'objects' => $this->extractKeyConcepts($title)
                ];
            }
        } catch (\Exception $e) {
            Log::error('Failed to generate enhanced image prompt', [
                'error' => $e->getMessage(),
                'title' => $title
            ]);
        }

        // Fallback to basic prompt if AI enhancement fails
        return [
            'prompt' => $this->generateBasicPrompt($title, $description),
            'style' => $options['style'] ?? $this->getRandomStyle(),
            'color_palette' => $this->determineColorPalette($title),
            'objects' => $this->extractKeyConcepts($title)
        ];
    }

    /**
     * Build the prompt template for AI processing
     */
    protected function buildPromptTemplate(string $title, string $description, array $options): string
    {
        // Use section-based style selection if section index is provided
        if (isset($options['section_index'])) {
            $style = $this->getStyleForSection($options['section_index']);
        } else {
            $style = $options['style'] ?? $this->getRandomStyle();
        }
        
        $styleDetails = $this->artStyles[$style];
        
        // Vary composition, lighting, and perspective based on style
        $composition = $this->getComplementaryComposition($style);
        $lighting = $this->getComplementaryLighting($style);
        $perspective = $this->getComplementaryPerspective($style);

        $template = <<<PROMPT
You are creating a simple, realistic image prompt for a Persian/Farsi service. Generate a CLEAR and CONCRETE image description.

Title: {$title}
Description: {$description}

STRICT RULES:
1. Create a REALISTIC, LITERAL scene - NOT abstract or metaphysical
2. Use REAL OBJECTS that people can recognize immediately
3. NO people in the image at all
4. NO text, letters, numbers, or words visible
5. Simple composition with 2-3 main objects maximum
6. Professional photography style, not artistic interpretations

Focus on REAL, TANGIBLE elements:
- If it's about cars: Show a real car, steering wheel, or car keys
- If it's about documents: Show actual papers, folders, or filing cabinets  
- If it's about money: Show currency, wallet, or bank cards
- If it's about technology: Show computers, phones, or specific devices
- If it's about services: Show the actual tools or objects used

Create a prompt that describes:
1. The MAIN OBJECT (be specific - what type, color, position)
2. The SETTING (office desk, outdoor, indoor, specific location)
3. Supporting elements (maximum 2 additional items)
4. Lighting: Natural daylight, bright and clear
5. Camera angle: Straightforward, eye-level view

Style: {$styleDetails}
Composition: {$composition}
Lighting: {$lighting}  
Perspective: {$perspective}
Color variation: Use different color schemes for each image
Mood: Professional yet visually interesting

Return ONLY a JSON object:
{
  "prompt": "complete prompt focusing on objects/symbols, not people",
  "style": "{$style}",
  "color_palette": "3-4 colors that fit the service",
  "main_objects": "3-4 key visual elements",
  "mood": "emotional tone",
  "story": "symbolic meaning"
}
PROMPT;

        return $template;
    }

    /**
     * Generate a basic fallback prompt with variety
     */
    protected function generateBasicPrompt(string $title, string $description): string
    {
        $titleLower = mb_strtolower($title);
        
        // Different prompt variations for variety
        $promptVariations = [
            'car' => [
                "luxury car showroom, gleaming vehicles, modern architecture, glass reflections",
                "car dashboard with navigation system, leather interior, ambient lighting",
                "automotive workshop, tools on wall, clean garage, professional equipment",
                "car keys on premium wooden surface, dealership brochure, elegant presentation",
                "modern electric vehicle charging station, futuristic design, green technology"
            ],
            'insurance' => [
                "insurance policy documents, magnifying glass, professional folder, organized desk",
                "umbrella protecting miniature objects, safety concept, protective shield visualization",
                "modern insurance office, glass partitions, comfortable seating area",
                "calculator and charts on desk, risk assessment papers, analytical tools",
                "digital tablet showing insurance app, modern technology, paperless office"
            ],
            'bank' => [
                "modern bank vault door, security features, metallic textures, impressive architecture",
                "credit cards arranged artistically, holographic security features, premium cards",
                "digital banking on laptop screen, secure connection icon, modern interface",
                "piggy bank collection, savings concept, financial planning visualization",
                "bank building exterior, impressive columns, financial district architecture"
            ],
            'property' => [
                "architectural blueprints, ruler and compass, professional drafting table",
                "modern apartment building, balconies, urban architecture, city view",
                "house keys with wooden house keychain, welcome mat, new home concept",
                "real estate sign, sold placard, suburban house, achievement visualization",
                "interior design magazine, color swatches, renovation planning materials"
            ],
            'mobile' => [
                "smartphone repair tools, precision instruments, technical workspace",
                "5G network visualization, signal towers, connectivity concept, modern tech",
                "mobile accessories display, cases and chargers, retail presentation",
                "smartphone photography setup, ring light, content creation tools",
                "mobile app development workspace, multiple devices, coding environment"
            ],
            'internet' => [
                "fiber optic cables, colorful light streams, high-speed connection visualization",
                "network server room, blinking lights, data center, technology infrastructure",
                "wifi symbol in coffee shop, laptop users silhouettes, connected workspace",
                "ethernet cables in organized pattern, network switch, IT infrastructure",
                "cloud computing visualization, connected devices, digital ecosystem"
            ],
            'customs' => [
                "shipping port aerial view, containers, cargo ships, international trade",
                "customs stamp on documents, official seal, import/export papers",
                "warehouse with stacked boxes, forklift, logistics operation",
                "global shipping routes on map, international commerce visualization",
                "customs officer desk setup, stamps and forms, border control equipment"
            ],
            'tax' => [
                "tax calculation on modern calculator, receipts, financial planning",
                "organized filing cabinet, labeled folders, document management system",
                "tax forms spread on desk, coffee cup, deadline calendar, work session",
                "digital tax software on computer, e-filing visualization, modern solution",
                "accountant's desk with graphs and charts, analytical work environment"
            ],
            'default' => [
                "modern co-working space, plants, natural light, productive environment",
                "minimalist office setup, clean desk policy, organized workspace",
                "business meeting room, conference table, professional setting",
                "startup office, creative workspace, innovative environment",
                "executive office, leather chair, prestigious workspace, success visualization"c7b36028", ultra high quality, professional photography, sharp focus, no people, no text",
            ", cinematic lighting, depth of field, bokeh background, no people, no text",
            ", studio lighting, clean composition, minimalist aesthetic, no people, no text",
            ", golden hour lighting, warm tones, artistic composition, no people, no text",
            ", modern style, contemporary design, sleek appearance, no people, no text"
        ];
        
        $modifierIndex = (self::$sectionCounter + 1) % count($styleModifiers);
        $prompt = $basePrompt . $styleModifiers[$modifierIndex];
        
        return $prompt;
    }

    /**
     * Extract key concepts from title for visual representation
     * Simplified - let AI handle most of the interpretation
     */
    protected function extractKeyConcepts(string $title): string
    {
        return "abstract symbols and objects representing {$title}";
    }

    /**
     * Determine scene elements based on title context
     * Simplified - let AI handle the interpretation
     */
    protected function determineSceneElements(string $title): string
    {
        return "minimalist environment suitable for {$title} service";
    }

    /**
     * Determine color palette based on context
     * Keep this for some basic guidance
     */
    protected function determineColorPalette(string $title): string
    {
        $palettes = [
            'financial' => 'deep blues, gold accents, professional grays',
            'technology' => 'electric blue, purple gradients, modern cyan',
            'trust' => 'ocean blue, emerald green, warm white',
            'innovative' => 'vibrant purple, orange highlights, dynamic gradients',
            'corporate' => 'navy blue, silver, charcoal gray',
            'friendly' => 'warm orange, soft blue, gentle green',
            'premium' => 'black, gold, platinum silver',
        ];
        
        // Random selection for variety
        return $palettes[array_keys($palettes)[array_rand(array_keys($palettes))]];
    }

    /**
     * Clean and optimize the generated prompt
     */
    protected function cleanPrompt(string $prompt): string
    {
        // Remove any actual text content that might have been generated
        $prompt = preg_replace('/["\'](.*?)["b56e658b"Professional comparison chart showing advantages, clean infographic style",
                "Modern dashboard displaying positive metrics and benefits",
                "Business success indicators, upward trending graphs",
                "Professional presentation slides showing key benefits",
                "Clean icons representing various advantages in grid layout"6cacd5a3"Step-by-step visual guide with numbered elements",
                "Professional instruction manual layout, clear diagrams",
                "Modern tutorial interface with progress indicators",
                "Educational flowchart showing process steps",
                "Clean instructional design with arrow flows"30ead616"Feature showcase with modern UI elements",
                "Professional feature comparison table",
                "Technology stack visualization with icons",
                "Modern app interface showing key features",
                "Clean feature grid with descriptive icons"5ea003a4"Professional pricing table with tiers",
                "Calculator and financial documents on desk",
                "Modern pricing interface with comparison",
                "Cost breakdown visualization, clean design",
                "Budget planning tools and charts"a904f201"Process workflow diagram with clear steps",
                "Professional methodology visualization",
                "Modern interface showing user journey",
                "Step-by-step process with checkmarks",
                "Clean flowchart explaining the process"ed57eb23"Modern banking app interface on smartphone",
                "Professional financial dashboard with charts",
                "Secure payment terminal in retail setting",
                "Digital wallet interface showing transactions",
                "Banking cards arranged professionally on desk",
                "Financial planning tools and calculators",
                "Modern ATM or banking kiosk interface",
                "Professional financial advisor workspace"1e7e05ce"Insurance policy documents professionally arranged",
                "Modern insurance app interface",
                "Professional insurance advisor desk setup",
                "Safety and protection concept visualization",
                "Insurance claim form with calculator",
                "Digital insurance dashboard showing coverage",
                "Professional insurance office environment",
                "Insurance comparison charts and graphs"
            ];
        }
        
        // Default professional scenes
        return [
            "Modern office desk with laptop and documents",
            "Professional business dashboard on screen",
            "Clean workspace with technology tools",
            "Corporate meeting room setup",
            "Professional service counter or desk",
            "Modern business application interface",
            "Clean professional documentation layout",
            "Business workflow visualization"8b9a87fe"{$basePrompt}, {$styleDetails}, {$lighting}, {$perspective}";
        
        // Add subtle variety while keeping professional
        $modifiers = $this->getProfessionalModifiers(self::$sectionCounter);
        $finalPrompt .= ", {$modifiers}";
        
        // Always end with quality tags
        $finalPrompt .= ", no people, no text, no watermarks, clean and professional";
        
        Log::info('Generated contextual prompt', [
            'section' => self::$sectionCounter,
            'title' => $title,
            'prompt_preview' => substr($finalPrompt, 0, 200)
        ]);
        
        return [
            'prompt' => $finalPrompt,
            'style' => $style,
            'color_palette' => $this->determineColorPalette($title),
            'objects' => $this->extractKeyConcepts($title)
        ];
    }
    
    
    /**
     * Generate multiple unique prompts for different sections
     */
    public function generateSectionPrompts(array $sections, string $mainTitle = ''): array
    {
        $prompts = [];
        self::$sectionCounter = 0;
        
        foreach ($sections as $index => $section) {
            $sectionTitle = is_array($section) ? ($section['title'] ?? $section['heading'] ?? '') : $section;
            $sectionDesc = is_array($section) ? ($section['description'] ?? '') : '';
            
            // Pass section index for style variety and use direct prompt
            $options = [
                'section_index' => $index,
                'main_title' => $mainTitle,
                'use_direct_prompt' => true  // Force direct prompt generation
            ];
            
            $promptData = $this->generateImagePrompt($sectionTitle, $sectionDesc, $options);
            $prompts[$index] = $promptData;
            
            self::$sectionCounter++;
        }
        
        return $prompts;
    }
}