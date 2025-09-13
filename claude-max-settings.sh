#!/bin/bash
# Claude Code Maximum Performance Settings
# Run this script to apply maximum performance settings

echo "🚀 Applying Claude Code Maximum Performance Settings..."

# Output Token Limits (Maximum)
export CLAUDE_CODE_MAX_OUTPUT_TOKENS=4096000
export MAX_MCP_OUTPUT_TOKENS=100000
export MAX_THINKING_TOKENS=60000

# Bash Timeout Settings (Maximum)
export BASH_DEFAULT_TIMEOUT_MS=3600000
export BASH_MAX_TIMEOUT_MS=3600000
export BASH_MAX_OUTPUT_LENGTH=1000000

# MCP Server Settings (Maximum Performance)
export MCP_TIMEOUT=300000
export MCP_TOOL_TIMEOUT=600000

# Model Optimization
export CLAUDE_CODE_SUBAGENT_MODEL=claude-3-5-sonnet-20241022
export CLAUDE_CODE_HAIKU_MODEL=claude-3-haiku-20240307
export CLAUDE_CODE_SONNET_MODEL=claude-3-5-sonnet-20241022

# Performance Optimizations
export DISABLE_NON_ESSENTIAL_MODEL_CALLS=false

# Memory and Context Limits
export CLAUDE_CODE_MAX_CONTEXT_SIZE=8192000
export CLAUDE_CODE_MEMORY_BUDGET=unlimited

# Concurrent Operations
export CLAUDE_CODE_MAX_CONCURRENT_TOOLS=15
export CLAUDE_CODE_PARALLEL_LIMIT=10

# Advanced Features
export CLAUDE_CODE_ENABLE_ADVANCED_REASONING=true
export CLAUDE_CODE_ENABLE_DEEP_ANALYSIS=true
export CLAUDE_CODE_ENABLE_PARALLEL_THINKING=true
export CLAUDE_CODE_ENABLE_MCP_OPTIMIZATION=true

# Development Mode
export CLAUDE_CODE_DEBUG_MODE=true
export CLAUDE_CODE_VERBOSE=true
export CLAUDE_CODE_LOG_LEVEL=debug

# Network Settings
export CLAUDE_CODE_WEB_TIMEOUT=300000
export CLAUDE_CODE_NETWORK_RETRY_COUNT=5

# Resource Allocation
export CLAUDE_CODE_CPU_INTENSIVE=true
export CLAUDE_CODE_MEMORY_INTENSIVE=true
export CLAUDE_CODE_IO_INTENSIVE=true

echo "✅ All Claude Code settings maximized for optimal performance!"
echo "🔥 Output tokens: 4,096,000"
echo "🧠 MCP output: 100,000 tokens"
echo "⚡ Thinking tokens: 60,000"
echo "🚄 Bash timeout: 60 minutes"
echo "🌐 Network timeout: 5 minutes"
echo "🔧 Debug mode: Enabled"
echo "💪 All limits maximized!"

# Source the .env.claude file if it exists
if [ -f ".env.claude" ]; then
    echo "📝 Loading settings from .env.claude..."
    set -a
    source .env.claude
    set +a
fi

echo "🎯 Claude Code is now configured for maximum performance!"