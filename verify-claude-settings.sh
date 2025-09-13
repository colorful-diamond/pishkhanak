#!/bin/bash
echo "🔍 Claude Code Performance Settings Verification"
echo "=============================================="
echo ""

echo "📊 Current Environment Variables:"
echo "CLAUDE_CODE_MAX_OUTPUT_TOKENS: ${CLAUDE_CODE_MAX_OUTPUT_TOKENS:-'NOT SET'}"
echo "MAX_MCP_OUTPUT_TOKENS: ${MAX_MCP_OUTPUT_TOKENS:-'NOT SET'}"
echo "MAX_THINKING_TOKENS: ${MAX_THINKING_TOKENS:-'NOT SET'}"
echo "BASH_DEFAULT_TIMEOUT_MS: ${BASH_DEFAULT_TIMEOUT_MS:-'NOT SET'}"
echo "CLAUDE_CODE_MAX_CONCURRENT_TOOLS: ${CLAUDE_CODE_MAX_CONCURRENT_TOOLS:-'NOT SET'}"
echo ""

echo "🎯 Target Values (Maximum Performance):"
echo "CLAUDE_CODE_MAX_OUTPUT_TOKENS: 4096000"
echo "MAX_MCP_OUTPUT_TOKENS: 100000"
echo "MAX_THINKING_TOKENS: 60000"  
echo "BASH_DEFAULT_TIMEOUT_MS: 3600000"
echo "CLAUDE_CODE_MAX_CONCURRENT_TOOLS: 15"
echo ""

echo "📁 Configuration Files:"
echo "~/.claude_profile: $([ -f ~/.claude_profile ] && echo '✅ EXISTS' || echo '❌ MISSING')"
echo ".env.claude: $([ -f .env.claude ] && echo '✅ EXISTS' || echo '❌ MISSING')"
echo ".mcp.json: $([ -f .mcp.json ] && echo '✅ EXISTS' || echo '❌ MISSING')"
echo ""

echo "🔧 MCP Configuration Check:"
if [ -f .mcp.json ]; then
    if grep -q "MAX_MCP_OUTPUT_TOKENS" .mcp.json; then
        echo "MCP servers configured: ✅ YES"
    else
        echo "MCP servers configured: ❌ NO"
    fi
else
    echo "MCP configuration: ❌ MISSING"
fi
echo ""

if [ "$CLAUDE_CODE_MAX_OUTPUT_TOKENS" = "4096000" ] && [ "$MAX_MCP_OUTPUT_TOKENS" = "100000" ]; then
    echo "🎉 SUCCESS: Maximum performance settings are ACTIVE!"
    echo "🚀 Claude Code is running at full power!"
else
    echo "⚠️  WARNING: Settings not fully applied"
    echo "💡 Try running: source ~/.claude_profile"
    echo "💡 Or restart Claude Code session"
fi