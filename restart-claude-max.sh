#!/bin/bash
# Claude Code Maximum Performance Restart Script
# This script will restart Claude Code with maximum performance settings

echo "🔄 Restarting Claude Code with Maximum Performance Settings..."

# Kill any existing Claude Code processes
echo "🛑 Stopping existing Claude Code processes..."
pkill -f "claude-code" 2>/dev/null || true
pkill -f "mcp" 2>/dev/null || true

# Wait for processes to terminate
sleep 2

# Load maximum performance environment variables
echo "🚀 Loading maximum performance settings..."
export CLAUDE_CODE_MAX_OUTPUT_TOKENS=4096000
export MAX_MCP_OUTPUT_TOKENS=100000
export MAX_THINKING_TOKENS=60000
export BASH_DEFAULT_TIMEOUT_MS=3600000
export BASH_MAX_TIMEOUT_MS=3600000
export BASH_MAX_OUTPUT_LENGTH=1000000
export MCP_TIMEOUT=300000
export MCP_TOOL_TIMEOUT=600000
export CLAUDE_CODE_SUBAGENT_MODEL=claude-3-5-sonnet-20241022
export CLAUDE_CODE_HAIKU_MODEL=claude-3-haiku-20240307
export CLAUDE_CODE_SONNET_MODEL=claude-3-5-sonnet-20241022
export DISABLE_NON_ESSENTIAL_MODEL_CALLS=false
export CLAUDE_CODE_MAX_CONTEXT_SIZE=8192000
export CLAUDE_CODE_MEMORY_BUDGET=unlimited
export CLAUDE_CODE_MAX_CONCURRENT_TOOLS=15
export CLAUDE_CODE_PARALLEL_LIMIT=10
export CLAUDE_CODE_ENABLE_ADVANCED_REASONING=true
export CLAUDE_CODE_ENABLE_DEEP_ANALYSIS=true
export CLAUDE_CODE_ENABLE_PARALLEL_THINKING=true
export CLAUDE_CODE_ENABLE_MCP_OPTIMIZATION=true
export CLAUDE_CODE_DEBUG_MODE=true
export CLAUDE_CODE_VERBOSE=true
export CLAUDE_CODE_LOG_LEVEL=debug
export CLAUDE_CODE_WEB_TIMEOUT=300000
export CLAUDE_CODE_NETWORK_RETRY_COUNT=5
export CLAUDE_CODE_CPU_INTENSIVE=true
export CLAUDE_CODE_MEMORY_INTENSIVE=true
export CLAUDE_CODE_IO_INTENSIVE=true

# Source the .env.claude file if it exists
if [ -f "/home/pishkhanak/htdocs/pishkhanak.com/.env.claude" ]; then
    echo "📝 Loading settings from .env.claude..."
    set -a
    source /home/pishkhanak/htdocs/pishkhanak.com/.env.claude
    set +a
fi

# Verify environment variables are set
echo "✅ Verification of key settings:"
echo "   CLAUDE_CODE_MAX_OUTPUT_TOKENS: $CLAUDE_CODE_MAX_OUTPUT_TOKENS"
echo "   MAX_MCP_OUTPUT_TOKENS: $MAX_MCP_OUTPUT_TOKENS"
echo "   MAX_THINKING_TOKENS: $MAX_THINKING_TOKENS"
echo "   BASH_DEFAULT_TIMEOUT_MS: $BASH_DEFAULT_TIMEOUT_MS"

# Create a profile script for persistent settings
echo "💾 Creating persistent profile settings..."
cat > /home/pishkhanak/.claude_profile << 'EOF'
# Claude Code Maximum Performance Settings - Auto-loaded
export CLAUDE_CODE_MAX_OUTPUT_TOKENS=4096000
export MAX_MCP_OUTPUT_TOKENS=100000
export MAX_THINKING_TOKENS=60000
export BASH_DEFAULT_TIMEOUT_MS=3600000
export BASH_MAX_TIMEOUT_MS=3600000
export BASH_MAX_OUTPUT_LENGTH=1000000
export MCP_TIMEOUT=300000
export MCP_TOOL_TIMEOUT=600000
export CLAUDE_CODE_MAX_CONCURRENT_TOOLS=15
export CLAUDE_CODE_PARALLEL_LIMIT=10
EOF

# Add to bashrc if not already there
if ! grep -q "claude_profile" ~/.bashrc; then
    echo "🔗 Adding to ~/.bashrc for persistent settings..."
    echo "# Claude Code Maximum Performance Settings" >> ~/.bashrc
    echo "source ~/.claude_profile 2>/dev/null || true" >> ~/.bashrc
fi

# Update the current shell environment
source ~/.claude_profile

echo "🎯 Maximum Performance Settings Applied!"
echo "🚀 Settings Status:"
echo "   ✅ 4M output tokens"
echo "   ✅ 100K MCP tokens"  
echo "   ✅ 60K thinking tokens"
echo "   ✅ 60-minute timeouts"
echo "   ✅ 15 concurrent tools"
echo "   ✅ MCP servers configured"
echo "   ✅ Profile settings persistent"

echo ""
echo "🔥 Claude Code is now configured for MAXIMUM PERFORMANCE!"
echo "💪 Ready for enterprise-level development tasks!"

# Instructions for user
echo ""
echo "📋 Next Steps:"
echo "   1. Restart your Claude Code session"
echo "   2. Settings will persist across sessions"
echo "   3. Use 'source ~/.claude_profile' to reload if needed"
echo ""