<?php
// Recent changes observed in GeminiService.php
// External API integration with performance issues

class GeminiService
{
    // Lines 322-331: Synchronous HTTP requests with excessive timeout
    public function makeRequest($prompt)
    {
        // PERFORMANCE ISSUE FOUND:
        $request = Http::withHeaders($this->getHeaders())
            ->timeout(600); // 10-minute timeout is excessive!
            
        // Issues:
        // - No connection pooling
        // - No async processing  
        // - Blocking operations causing timeouts
        // - No retry logic with exponential backoff
        
        // RECOMMENDED:
        // ->timeout(30) // Reduce to 30 seconds max
        // Add connection pooling
        // Use async/parallel requests for multiple API calls
    }
    
    // Lines 131-161: API key rotation causing additional latency
    protected function rotateApiKey()
    {
        // This was causing additional overhead in each request
        // Should be optimized or moved to background process
    }
    
    // Lines 37-47: Proxy usage adding extra network overhead
    protected function configureProxy()
    {
        // Proxy configuration was adding latency
        // Should be conditional based on environment
    }
    
    // Recent optimizations needed:
    // - Implement request caching for repeated prompts
    // - Add circuit breaker pattern for API failures  
    // - Use connection pooling for better performance
    // - Add proper error handling and retry logic
}