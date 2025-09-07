<?php

namespace Database\Seeders;

use App\Models\AiSetting;
use Illuminate\Database\Seeder;

class AiSettingSeeder extends Seeder
{
    public function run()
    {
        AiSetting::create([
            'name' => 'Professional Content Generator',
            'description' => 'Advanced AI settings optimized for professional content creation across multiple formats and audiences. Includes comprehensive prompt engineering best practices and multilingual support.',
            'is_active' => true,
            'max_tokens' => 4000,
            'temperature' => 0.7,
            'frequency_penalty' => 0.1,
            'presence_penalty' => 0.1,
            'stop_sequences' => ['[END]', '[STOP]', '---END---'],
            'ordering' => 1,
            
            'model_config' => [
                'blog_posts' => [
                    'name' => 'blog_posts',
                    'label' => 'Blog Posts & Articles',
                    'model' => \App\Models\Post::class,
                    'model_type' => 'gpt-4',
                    'title_field' => 'title',
                    'description_field' => 'content',
                    'searchable_fields' => ['title', 'slug', 'excerpt'],
                    'word_count_range' => [800, 2500],
                    'structure' => ['introduction', 'main_content', 'conclusion', 'call_to_action']
                ],
                'services' => [
                    'name' => 'services',
                    'label' => 'Service Pages',
                    'model' => \LaraZeus\Bolt\Models\Form::class,
                    'model_type' => 'gpt-4',
                    'title_field' => 'name',
                    'description_field' => 'description',
                    'searchable_fields' => ['name', 'slug'],
                    'word_count_range' => [600, 1500],
                    'structure' => ['overview', 'features', 'benefits', 'process', 'pricing', 'cta']
                ],
                'product_descriptions' => [
                    'name' => 'product_descriptions',
                    'label' => 'Product Descriptions',
                    'model' => \App\Models\Product::class,
                    'model_type' => 'gpt-3.5-turbo',
                    'title_field' => 'name',
                    'description_field' => 'description',
                    'searchable_fields' => ['name', 'sku'],
                    'word_count_range' => [150, 400],
                    'structure' => ['headline', 'features', 'benefits', 'specifications']
                ],
                'landing_pages' => [
                    'name' => 'landing_pages',
                    'label' => 'Landing Pages',
                    'model' => \App\Models\Page::class,
                    'model_type' => 'gpt-4',
                    'title_field' => 'title',
                    'description_field' => 'content',
                    'searchable_fields' => ['title', 'slug'],
                    'word_count_range' => [1200, 3000],
                    'structure' => ['hero', 'problem', 'solution', 'features', 'testimonials', 'pricing', 'faq', 'cta']
                ],
                'social_media' => [
                    'name' => 'social_media',
                    'label' => 'Social Media Posts',
                    'model' => \App\Models\SocialPost::class,
                    'model_type' => 'gpt-3.5-turbo',
                    'title_field' => 'title',
                    'description_field' => 'content',
                    'searchable_fields' => ['title', 'platform'],
                    'word_count_range' => [50, 280],
                    'structure' => ['hook', 'value', 'call_to_action', 'hashtags']
                ]
            ],
            
            'generation_settings' => [
                'headings_number' => 8,
                'sub_headings_number' => 3,
                'min_headings' => 3,
                'max_headings' => 15,
                'min_sub_headings' => 1,
                'max_sub_headings' => 8,
                'min_words_per_section' => 150,
                'max_words_per_section' => 500,
                'include_faq' => true,
                'faq_count' => 5,
                'include_meta' => true,
                'include_schema' => true,
                'include_summary' => true,
                'include_introduction' => true,
                'include_conclusion' => true,
                'enable_auto_save' => true,
                'auto_save_interval' => 300,
                'enable_images' => true,
                'max_images' => 5,
                'enable_online_mode' => true,
                'enable_fact_checking' => true,
                'plagiarism_check' => true,
                'readability_optimization' => true,
                'seo_optimization' => true,
                'keyword_density_target' => 1.5,
                'enable_emotional_prompting' => true,
                'chain_of_thought' => true,
                'few_shot_examples' => true
            ],
            
            'prompt_templates' => [
                'heading_generation' => [
                    'template' => 'As a professional content strategist, generate {count} engaging, SEO-optimized headings for an article about {topic}. Target audience: {audience}. Tone: {tone}. 

                    CRITICAL REQUIREMENTS:
                    - Each heading must be compelling and click-worthy
                    - Include relevant keywords naturally
                    - Maintain {tone} tone throughout
                    - Consider search intent and user needs
                    - Format as H2-level headings
                    
                    Context: {context}
                    
                    Think step by step and ensure each heading provides unique value.',
                    'variables' => ['count', 'topic', 'audience', 'tone', 'context']
                ],
                'section_generation' => [
                    'template' => 'You are an expert content writer specializing in {content_format}. Write a comprehensive section about "{heading}" for an article titled "{title}".

                    CONTEXT & REQUIREMENTS:
                    - Target audience: {audience}
                    - Tone: {tone}
                    - Word count: {min_words}-{max_words} words
                    - Include relevant examples and actionable insights
                    - Optimize for readability and engagement
                    - Incorporate SEO best practices naturally
                    
                    CONTENT STRUCTURE:
                    1. Start with a compelling opening
                    2. Provide detailed, valuable information
                    3. Include specific examples or case studies
                    4. End with key takeaways or actionable steps
                    
                    Write in a {tone} tone that resonates with {audience}. This is very important for user engagement.',
                    'variables' => ['content_format', 'heading', 'title', 'audience', 'tone', 'min_words', 'max_words']
                ],
                'introduction_generation' => [
                    'template' => 'As a professional copywriter, create a compelling introduction for an article titled "{title}". 

                    ARTICLE DETAILS:
                    - Topic: {topic}
                    - Target audience: {audience}
                    - Tone: {tone}
                    - Main message: {main_message}
                    
                    INTRODUCTION REQUIREMENTS:
                    - Hook the reader immediately (use statistics, questions, or bold statements)
                    - Clearly establish the value proposition
                    - Preview what readers will learn
                    - Maintain {tone} tone
                    - 100-200 words maximum
                    - End with a smooth transition to the main content
                    
                    Remember: The introduction determines if readers continue reading. Make it irresistible!',
                    'variables' => ['title', 'topic', 'audience', 'tone', 'main_message']
                ],
                'conclusion_generation' => [
                    'template' => 'Write a powerful conclusion for an article about {topic}. Target audience: {audience}. Tone: {tone}.

                    CONCLUSION ELEMENTS:
                    1. Summarize key points without being repetitive
                    2. Reinforce the main value proposition
                    3. Include a compelling call-to-action
                    4. End with inspiration or motivation
                    
                    REQUIREMENTS:
                    - 150-250 words
                    - Maintain {tone} tone
                    - Create urgency or motivation to act
                    - Reference the journey readers have taken
                    
                    Main article points: {key_points}
                    
                    Your conclusion should make readers feel empowered and ready to take action.',
                    'variables' => ['topic', 'audience', 'tone', 'key_points']
                ],
                'faq_generation' => [
                    'template' => 'Generate {count} frequently asked questions and comprehensive answers about {topic}. Target audience: {audience}.

                    REQUIREMENTS FOR EACH FAQ:
                    - Questions should address real user concerns
                    - Answers must be detailed and actionable
                    - Include relevant keywords naturally
                    - Maintain {tone} tone
                    - Answers should be 50-100 words each
                    
                    QUESTION TYPES TO INCLUDE:
                    - How-to questions
                    - Problem-solving questions  
                    - Comparison questions
                    - Cost/benefit questions
                    - Implementation questions
                    
                    Context: {context}
                    
                    Think about what {audience} would genuinely want to know about {topic}.',
                    'variables' => ['count', 'topic', 'audience', 'tone', 'context']
                ],
                'meta_generation' => [
                    'template' => 'Create SEO-optimized meta title and description for an article about {topic}.

                    ARTICLE DETAILS:
                    - Target audience: {audience}
                    - Primary keyword: {primary_keyword}
                    - Secondary keywords: {secondary_keywords}
                    - Tone: {tone}
                    
                    META TITLE REQUIREMENTS:
                    - 50-60 characters maximum
                    - Include primary keyword near the beginning
                    - Compelling and click-worthy
                    - Accurately represent content
                    
                    META DESCRIPTION REQUIREMENTS:
                    - 150-160 characters maximum
                    - Include primary and secondary keywords naturally
                    - Create urgency or curiosity
                    - Include a call-to-action
                    - Accurately summarize content value
                    
                    Focus on search intent and user needs.',
                    'variables' => ['topic', 'audience', 'primary_keyword', 'secondary_keywords', 'tone']
                ],
                'summary_generation' => [
                    'template' => 'Create a comprehensive summary of the article about {topic}. Target audience: {audience}.

                    SUMMARY REQUIREMENTS:
                    - 100-150 words
                    - Highlight main points and key takeaways
                    - Include actionable insights
                    - Maintain {tone} tone
                    - Serve as a standalone piece
                    
                    STRUCTURE:
                    1. Brief overview of the topic
                    2. Key insights and main points
                    3. Primary benefits for the reader
                    4. Next steps or recommendations
                    
                    Article content: {content}
                    
                    Make it valuable for readers who want a quick overview.',
                    'variables' => ['topic', 'audience', 'tone', 'content']
                ]
            ],
            
            'language_settings' => [
                'supported_languages' => [
                    'fa' => 'فارسی (Persian)',
                    'en' => 'English',
                    'ar' => 'العربية (Arabic)',
                    'es' => 'Español (Spanish)',
                    'fr' => 'Français (French)',
                    'de' => 'Deutsch (German)',
                    'it' => 'Italiano (Italian)',
                    'pt' => 'Português (Portuguese)',
                    'ru' => 'Русский (Russian)',
                    'zh' => '中文 (Chinese)',
                    'ja' => '日本語 (Japanese)',
                    'ko' => '한국어 (Korean)',
                    'hi' => 'हिन्दी (Hindi)',
                    'tr' => 'Türkçe (Turkish)',
                    'nl' => 'Nederlands (Dutch)',
                    'sv' => 'Svenska (Swedish)',
                    'da' => 'Dansk (Danish)',
                    'no' => 'Norsk (Norwegian)',
                    'fi' => 'Suomi (Finnish)',
                    'pl' => 'Polski (Polish)'
                ],
                'default_language' => 'fa',
                'enable_translation' => true,
                'translation_service' => 'google',
                'rtl_languages' => ['fa', 'ar'],
                'language_specific_prompts' => [
                    'fa' => [
                        'cultural_context' => 'Consider Persian cultural context and values',
                        'writing_style' => 'Use appropriate Persian formal/informal registers',
                        'local_examples' => 'Include relevant Iranian/Persian examples'
                    ],
                    'ar' => [
                        'cultural_context' => 'Consider Arabic cultural context and Islamic values',
                        'writing_style' => 'Use appropriate Arabic formal/informal registers',
                        'local_examples' => 'Include relevant Middle Eastern examples'
                    ]
                ]
            ],
            
            'tone_settings' => [
                'professional' => [
                    'name' => 'professional',
                    'label' => 'Professional',
                    'description' => 'Formal, authoritative, and business-oriented tone suitable for corporate communications',
                    'attributes' => ['formal', 'precise', 'authoritative', 'trustworthy'],
                    'use_cases' => ['business documents', 'corporate blogs', 'white papers', 'case studies'],
                    'prompt_modifier' => 'Write in a professional, authoritative tone that builds trust and credibility'
                ],
                'conversational' => [
                    'name' => 'conversational',
                    'label' => 'Conversational',
                    'description' => 'Friendly, approachable, and engaging tone that feels like talking to a knowledgeable friend',
                    'attributes' => ['casual', 'approachable', 'relatable', 'warm'],
                    'use_cases' => ['blog posts', 'social media', 'newsletters', 'customer communications'],
                    'prompt_modifier' => 'Write in a friendly, conversational tone as if explaining to a good friend'
                ],
                'academic' => [
                    'name' => 'academic',
                    'label' => 'Academic',
                    'description' => 'Scholarly, research-oriented tone with emphasis on facts and analysis',
                    'attributes' => ['analytical', 'detailed', 'objective', 'evidence-based'],
                    'use_cases' => ['research papers', 'educational content', 'technical documentation'],
                    'prompt_modifier' => 'Write in an academic tone with thorough analysis and evidence-based arguments'
                ],
                'persuasive' => [
                    'name' => 'persuasive',
                    'label' => 'Persuasive',
                    'description' => 'Compelling and motivational tone designed to influence and inspire action',
                    'attributes' => ['compelling', 'motivational', 'action-oriented', 'influential'],
                    'use_cases' => ['sales pages', 'marketing copy', 'calls-to-action', 'product descriptions'],
                    'prompt_modifier' => 'Write in a persuasive tone that motivates readers to take action'
                ],
                'technical' => [
                    'name' => 'technical',
                    'label' => 'Technical',
                    'description' => 'Precise, detailed, and specification-focused tone for technical audiences',
                    'attributes' => ['precise', 'technical', 'instructional', 'detailed'],
                    'use_cases' => ['technical documentation', 'how-to guides', 'specifications', 'tutorials'],
                    'prompt_modifier' => 'Write in a technical tone with precise details and clear instructions'
                ],
                'inspirational' => [
                    'name' => 'inspirational',
                    'label' => 'Inspirational',
                    'description' => 'Uplifting and motivational tone that encourages and empowers readers',
                    'attributes' => ['uplifting', 'motivational', 'empowering', 'positive'],
                    'use_cases' => ['motivational content', 'personal development', 'leadership articles'],
                    'prompt_modifier' => 'Write in an inspirational tone that uplifts and motivates readers'
                ],
                'storytelling' => [
                    'name' => 'storytelling',
                    'label' => 'Storytelling',
                    'description' => 'Narrative-driven tone that engages through stories and examples',
                    'attributes' => ['narrative', 'engaging', 'descriptive', 'emotional'],
                    'use_cases' => ['brand stories', 'case studies', 'testimonials', 'blog narratives'],
                    'prompt_modifier' => 'Write in a storytelling tone with engaging narratives and vivid descriptions'
                ],
                'educational' => [
                    'name' => 'educational',
                    'label' => 'Educational',
                    'description' => 'Clear, informative tone focused on teaching and knowledge transfer',
                    'attributes' => ['clear', 'informative', 'structured', 'helpful'],
                    'use_cases' => ['tutorials', 'explainer articles', 'educational content', 'training materials'],
                    'prompt_modifier' => 'Write in an educational tone that clearly explains concepts and teaches effectively'
                ]
            ],
            
            'content_formats' => [
                'comprehensive_article' => [
                    'name' => 'comprehensive_article',
                    'label' => 'Comprehensive Article',
                    'description' => 'In-depth, well-researched articles with multiple sections and detailed analysis',
                    'structure' => ['introduction', 'overview', 'main_sections', 'examples', 'conclusion', 'faq'],
                    'word_count' => [1500, 3500],
                    'seo_focus' => 'high',
                    'sections' => 8,
                    'subsections' => 3
                ],
                'listicle' => [
                    'name' => 'listicle',
                    'label' => 'Listicle',
                    'description' => 'Numbered or bulleted list format with detailed explanations for each point',
                    'structure' => ['introduction', 'list_items', 'conclusion'],
                    'word_count' => [800, 2000],
                    'seo_focus' => 'medium',
                    'sections' => 10,
                    'subsections' => 1
                ],
                'how_to_guide' => [
                    'name' => 'how_to_guide',
                    'label' => 'How-to Guide',
                    'description' => 'Step-by-step instructional content with clear actions and outcomes',
                    'structure' => ['introduction', 'prerequisites', 'steps', 'tips', 'conclusion'],
                    'word_count' => [1000, 2500],
                    'seo_focus' => 'high',
                    'sections' => 6,
                    'subsections' => 4
                ],
                'product_review' => [
                    'name' => 'product_review',
                    'label' => 'Product Review',
                    'description' => 'Detailed analysis and evaluation of products or services',
                    'structure' => ['introduction', 'overview', 'pros_cons', 'features', 'verdict'],
                    'word_count' => [1200, 2500],
                    'seo_focus' => 'high',
                    'sections' => 5,
                    'subsections' => 3
                ],
                'case_study' => [
                    'name' => 'case_study',
                    'label' => 'Case Study',
                    'description' => 'Detailed examination of real-world examples with analysis and insights',
                    'structure' => ['background', 'challenge', 'solution', 'results', 'lessons'],
                    'word_count' => [1500, 3000],
                    'seo_focus' => 'medium',
                    'sections' => 5,
                    'subsections' => 2
                ],
                'news_article' => [
                    'name' => 'news_article',
                    'label' => 'News Article',
                    'description' => 'Timely, factual reporting on current events and developments',
                    'structure' => ['headline', 'lead', 'body', 'quotes', 'conclusion'],
                    'word_count' => [500, 1200],
                    'seo_focus' => 'medium',
                    'sections' => 4,
                    'subsections' => 2
                ],
                'opinion_piece' => [
                    'name' => 'opinion_piece',
                    'label' => 'Opinion Piece',
                    'description' => 'Thoughtful commentary and analysis on topics with clear perspective',
                    'structure' => ['introduction', 'argument', 'counterpoints', 'conclusion'],
                    'word_count' => [800, 1800],
                    'seo_focus' => 'low',
                    'sections' => 4,
                    'subsections' => 2
                ],
                'social_media_post' => [
                    'name' => 'social_media_post',
                    'label' => 'Social Media Post',
                    'description' => 'Short, engaging content optimized for social platforms',
                    'structure' => ['hook', 'value', 'call_to_action'],
                    'word_count' => [50, 300],
                    'seo_focus' => 'low',
                    'sections' => 1,
                    'subsections' => 0
                ]
            ],
            
            'target_audiences' => [
                'business_professionals' => [
                    'name' => 'business_professionals',
                    'label' => 'Business Professionals',
                    'description' => 'Corporate employees, managers, and executives seeking professional growth',
                    'characteristics' => ['career-focused', 'time-conscious', 'results-oriented', 'industry-aware'],
                    'interests' => ['leadership', 'productivity', 'business strategy', 'professional development'],
                    'pain_points' => ['time management', 'career advancement', 'work-life balance', 'staying competitive'],
                    'content_preferences' => ['actionable insights', 'data-driven content', 'case studies', 'best practices'],
                    'communication_style' => 'professional and authoritative'
                ],
                'entrepreneurs' => [
                    'name' => 'entrepreneurs',
                    'label' => 'Entrepreneurs & Startups',
                    'description' => 'Business owners, startup founders, and aspiring entrepreneurs',
                    'characteristics' => ['innovative', 'risk-taking', 'growth-minded', 'resource-conscious'],
                    'interests' => ['business growth', 'innovation', 'funding', 'market trends', 'scaling'],
                    'pain_points' => ['funding', 'market validation', 'team building', 'competition', 'uncertainty'],
                    'content_preferences' => ['practical advice', 'success stories', 'tools and resources', 'step-by-step guides'],
                    'communication_style' => 'inspirational and practical'
                ],
                'tech_enthusiasts' => [
                    'name' => 'tech_enthusiasts',
                    'label' => 'Tech Enthusiasts',
                    'description' => 'Technology professionals, developers, and early adopters',
                    'characteristics' => ['technical', 'detail-oriented', 'innovation-focused', 'analytical'],
                    'interests' => ['new technologies', 'programming', 'AI/ML', 'cybersecurity', 'digital trends'],
                    'pain_points' => ['keeping up with rapid changes', 'technical complexity', 'implementation challenges'],
                    'content_preferences' => ['technical depth', 'code examples', 'tutorials', 'product comparisons'],
                    'communication_style' => 'technical and precise'
                ],
                'marketers' => [
                    'name' => 'marketers',
                    'label' => 'Marketing Professionals',
                    'description' => 'Digital marketers, content creators, and marketing managers',
                    'characteristics' => ['creative', 'data-driven', 'trend-aware', 'results-focused'],
                    'interests' => ['marketing strategies', 'content creation', 'analytics', 'customer engagement'],
                    'pain_points' => ['ROI measurement', 'content creation at scale', 'audience engagement', 'platform changes'],
                    'content_preferences' => ['case studies', 'tactics and strategies', 'tools and templates', 'industry insights'],
                    'communication_style' => 'engaging and results-oriented'
                ],
                'consumers' => [
                    'name' => 'consumers',
                    'label' => 'General Consumers',
                    'description' => 'End consumers making purchasing decisions',
                    'characteristics' => ['value-conscious', 'convenience-seeking', 'comparison-shopping', 'review-dependent'],
                    'interests' => ['product benefits', 'value for money', 'user experience', 'problem-solving'],
                    'pain_points' => ['information overload', 'trust issues', 'decision paralysis', 'budget constraints'],
                    'content_preferences' => ['simple explanations', 'honest reviews', 'clear benefits', 'social proof'],
                    'communication_style' => 'friendly and trustworthy'
                ],
                'students' => [
                    'name' => 'students',
                    'label' => 'Students & Learners',
                    'description' => 'Students, educators, and lifelong learners seeking knowledge',
                    'characteristics' => ['curious', 'resource-limited', 'time-pressed', 'information-seeking'],
                    'interests' => ['education', 'skill development', 'career preparation', 'learning resources'],
                    'pain_points' => ['information overload', 'limited budget', 'time constraints', 'practical application'],
                    'content_preferences' => ['clear explanations', 'step-by-step guides', 'examples', 'free resources'],
                    'communication_style' => 'educational and supportive'
                ],
                'healthcare_professionals' => [
                    'name' => 'healthcare_professionals',
                    'label' => 'Healthcare Professionals',
                    'description' => 'Doctors, nurses, and healthcare workers',
                    'characteristics' => ['detail-oriented', 'evidence-based', 'patient-focused', 'continuing-education-minded'],
                    'interests' => ['medical advances', 'patient care', 'clinical research', 'healthcare technology'],
                    'pain_points' => ['time constraints', 'information accuracy', 'patient outcomes', 'regulatory compliance'],
                    'content_preferences' => ['evidence-based content', 'clinical studies', 'best practices', 'continuing education'],
                    'communication_style' => 'professional and evidence-based'
                ],
                'financial_services' => [
                    'name' => 'financial_services',
                    'label' => 'Financial Services',
                    'description' => 'Financial advisors, accountants, and financial professionals',
                    'characteristics' => ['analytical', 'risk-aware', 'compliance-focused', 'client-oriented'],
                    'interests' => ['market trends', 'regulations', 'investment strategies', 'client management'],
                    'pain_points' => ['regulatory compliance', 'market volatility', 'client education', 'technology adoption'],
                    'content_preferences' => ['market analysis', 'regulatory updates', 'case studies', 'educational content'],
                    'communication_style' => 'professional and trustworthy'
                ]
            ],
            
            'custom_instructions' => [
                'global_guidelines' => [
                    'always_prioritize_accuracy' => 'Always verify facts and provide accurate information. When uncertain, acknowledge limitations.',
                    'maintain_ethical_standards' => 'Ensure all content adheres to ethical guidelines and cultural sensitivity.',
                    'optimize_for_engagement' => 'Create content that engages readers while providing genuine value.',
                    'follow_seo_best_practices' => 'Incorporate SEO best practices naturally without compromising readability.',
                    'ensure_originality' => 'Create original content that provides unique insights and perspectives.'
                ],
                'content_quality_standards' => [
                    'readability' => 'Maintain appropriate reading level for target audience (typically 8th-10th grade).',
                    'structure' => 'Use clear headings, subheadings, and logical flow throughout content.',
                    'actionability' => 'Include actionable insights and practical takeaways in every piece.',
                    'evidence_based' => 'Support claims with credible sources, statistics, or examples when possible.',
                    'user_focused' => 'Always consider user intent and provide content that serves their needs.'
                ],
                'brand_voice_guidelines' => [
                    'consistency' => 'Maintain consistent brand voice across all content pieces.',
                    'authenticity' => 'Ensure content feels genuine and aligned with brand values.',
                    'authority' => 'Establish thought leadership through expert insights and analysis.',
                    'accessibility' => 'Make complex topics accessible to the target audience.',
                    'cultural_sensitivity' => 'Respect cultural differences and local contexts, especially for Persian content.'
                ],
                'persian_specific' => [
                    'language_register' => 'Use appropriate formal/informal language register based on audience and context.',
                    'cultural_context' => 'Incorporate relevant Persian cultural references and values where appropriate.',
                    'local_examples' => 'Use examples and case studies relevant to Iranian market and culture.',
                    'rtl_considerations' => 'Consider right-to-left reading patterns in content structure.',
                    'localization' => 'Adapt global concepts to local Persian context and understanding.'
                ]
            ]
        ]);
    }
} 