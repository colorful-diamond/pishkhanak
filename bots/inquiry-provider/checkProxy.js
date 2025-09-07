#!/usr/bin/env node

import fetch from 'node-fetch';
import { SocksProxyAgent } from 'socks-proxy-agent';

const PROXY_URL = 'socks5://127.0.0.1:1080';

async function checkProxy() {
    console.log('🔍 Testing SOCKS proxy connectivity...');
    console.log('📡 Proxy URL:', PROXY_URL);
    
    const proxyAgent = new SocksProxyAgent(PROXY_URL);
    
    try {
        console.log('\n⏰ Testing proxy connection to httpbin.org...');
        const startTime = Date.now();
        
        const response = await fetch('https://httpbin.org/ip', {
            agent: proxyAgent,
            timeout: 10000
        });
        
        const duration = Date.now() - startTime;
        
        if (response.ok) {
            const result = await response.json();
            console.log('✅ Proxy test successful!');
            console.log('🌐 Your IP through proxy:', result.origin);
            console.log('⏱️  Response time:', duration + 'ms');
        } else {
            console.log('❌ Proxy responded with error:', response.status, response.statusText);
        }
        
    } catch (error) {
        console.log('❌ Proxy connection failed:', error.message);
        console.log('📋 Error details:', error.code || 'Unknown error');
        
        // Common error messages and solutions
        if (error.message.includes('ECONNREFUSED')) {
            console.log('\n💡 Solution: Make sure your SOCKS proxy server is running on port 1080');
            console.log('   - Check if V2Ray/v2rayA/other proxy software is running');
            console.log('   - Verify the proxy is listening on 127.0.0.1:1080');
        } else if (error.message.includes('ENOTFOUND')) {
            console.log('\n💡 Solution: Check your internet connection');
        } else if (error.message.includes('timeout')) {
            console.log('\n💡 Solution: Proxy is too slow or not responding');
        }
    }
    
    console.log('\n🔍 Testing direct connection (without proxy)...');
    try {
        const directResponse = await fetch('https://httpbin.org/ip', { timeout: 10000 });
        if (directResponse.ok) {
            const directResult = await directResponse.json();
            console.log('✅ Direct connection successful!');
            console.log('🌐 Your real IP:', directResult.origin);
        }
    } catch (error) {
        console.log('❌ Direct connection also failed:', error.message);
    }
    
    console.log('\n🧪 Testing Google AI API access...');
    try {
        // Test if we can reach Google's API endpoint
        const googleResponse = await fetch('https://generativelanguage.googleapis.com/', {
            agent: proxyAgent,
            timeout: 10000
        });
        console.log('✅ Google API endpoint reachable through proxy, status:', googleResponse.status);
    } catch (error) {
        console.log('❌ Google API endpoint not reachable through proxy:', error.message);
        
        try {
            const directGoogleResponse = await fetch('https://generativelanguage.googleapis.com/', {
                timeout: 10000
            });
            console.log('✅ Google API endpoint reachable directly, status:', directGoogleResponse.status);
        } catch (directError) {
            console.log('❌ Google API endpoint not reachable directly either:', directError.message);
        }
    }
}

checkProxy().catch(console.error); 