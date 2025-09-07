<?php
// Recent changes observed in TelegramTicketBot.php
// This was a 1568-line file with major issues

class TelegramTicketBot
{
    // Issues found in the analysis:
    
    // Lines 1159-1167: Automatic admin user creation (security risk)
    protected function createAdminUser($telegramUserId)
    {
        // This was creating admin users automatically - security issue
        // Used telegram_user_id for authorization instead of proper RBAC
        // Had fallback that creates admin users with default permissions
    }
    
    // Line 653: SQL injection risk
    public function getTicketStats()
    {
        // VULNERABLE CODE FOUND:
        // DB::raw('count(*) as count') - without parameterization
        
        // Lines 625-632: Raw SQL for time calculations
        // Raw SQL concatenation in time calculations
    }
    
    // Lines 406-564: handleCallbackQuery method (557 lines!)
    public function handleCallbackQuery($callbackQuery)
    {
        // This method was doing too much:
        // - Message parsing
        // - Authorization 
        // - Business logic
        // - Response formatting
        // All mixed together in one massive method
    }
    
    // Lines 95-107: Silent failures identified
    // Poor error handling causing cascading failures
    
    // Authorization logic issues:
    protected function isAuthorized($userId)
    {
        // Current weak implementation uses telegram_user_id
        // Should use proper role-based access control:
        /*
        $user = User::where('telegram_chat_id', $userId)
            ->whereHas('roles', function($query) {
                $query->whereIn('name', ['admin', 'support']);
            })
            ->first();
        
        return $user !== null;
        */
    }
}