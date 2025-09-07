# Browser Stealth Utilities

This module provides comprehensive stealth techniques to make browser automation undetectable by anti-bot systems.

## Features

- **User Agent Randomization**: Random realistic user agents from popular browsers
- **Viewport Randomization**: Common screen resolutions to avoid fingerprinting
- **Navigator Property Spoofing**: Override webdriver, plugins, languages, and hardware properties
- **Canvas & WebGL Fingerprint Protection**: Randomize canvas rendering and WebGL parameters
- **Human-like Behavior**: Mouse movements, typing delays, and form interaction patterns
- **Comprehensive Headers**: Realistic HTTP headers and security headers

## Usage

### Basic Setup

```javascript
import { 
    getStealthLaunchArgs, 
    getStealthContextConfig, 
    setupStealthMode 
} from '../utils/stealthUtils.js';

// Launch browser with stealth args
const browser = await chromium.launch({ 
    headless: true,
    args: getStealthLaunchArgs()
});

// Create stealth context (direct connection)
const context = await browser.newContext(getStealthContextConfig());
const page = await context.newPage();

// Apply stealth mode to page
await setupStealthMode(page);
```

### Human-like Interactions

```javascript
import { 
    humanType, 
    humanSubmit, 
    humanDelay, 
    addRandomMouseMovements 
} from '../utils/stealthUtils.js';

// Type with human-like delays
await humanType(page, '#username', 'myusername', { delay: 50, variance: 50 });

// Human-like delay
await humanDelay(page, 500, 1000);

// Submit form with human behavior
await humanSubmit(page, 'button[type="submit"]');

// Add random mouse movements
await addRandomMouseMovements(page);
```

### Advanced Configuration

```javascript
// Custom user agent and viewport
const userAgent = getRandomUserAgent();
const viewport = getRandomViewport();

const context = await browser.newContext(
    getStealthContextConfig(userAgent, viewport)
);
```

## Functions

### Core Functions

- `getRandomUserAgent()` - Returns random realistic user agent
- `getRandomViewport()` - Returns random common viewport size
- `getStealthLaunchArgs(userAgent?)` - Browser launch arguments for stealth
- `getStealthContextConfig(userAgent?, viewport?)` - Context configuration (direct connection)
- `setupStealthMode(page)` - Apply stealth to page

### Human Behavior

- `humanType(page, selector, text, options)` - Type with realistic delays
- `humanSubmit(page, submitSelector)` - Submit form with human behavior
- `humanDelay(page, minMs, maxMs)` - Random human-like delay
- `addRandomMouseMovements(page)` - Add random mouse movements
- `randomScroll(page)` - Random scroll behavior

## Anti-Detection Techniques

1. **Webdriver Detection**: Removes `navigator.webdriver` and automation indicators
2. **Plugin Spoofing**: Realistic browser plugins
3. **Canvas Fingerprinting**: Adds noise to canvas rendering
4. **WebGL Fingerprinting**: Spoofs graphics card information
5. **Screen Properties**: Consistent screen dimensions
6. **Timing Attacks**: Human-like delays and interactions
7. **HTTP Headers**: Realistic browser headers
8. **Mouse Behavior**: Random movements and interactions

## Best Practices

1. Always use random delays between actions
2. Combine multiple techniques for better stealth
3. Randomize user agents and viewports per session
4. Use human-like typing speeds (50-100ms per character)
5. Add mouse movements before important actions
6. Test with anti-bot detection services regularly

## Direct Connection (No Proxy)

The stealth utilities now use direct connections without any proxy for simplicity and performance:

```javascript
// Direct connection with stealth configuration
const context = await browser.newContext(getStealthContextConfig());

// Console output:
// 🌐 [STEALTH] Using direct connection (no proxy)
// 🎭 [STEALTH] Enhanced stealth mode enabled
```

## Check Login Functionality

The NICS24 provider now includes login status checking:

```javascript
import nics24 from './providers/nics24/index.js';

// Check if user is still logged in
const checkResult = await nics24.checkLogin();

if (checkResult.status === 'success') {
    console.log('User is logged in');
    console.log('Session age:', checkResult.data.sessionAge, 'minutes');
} else {
    console.log('Login required:', checkResult.message);
    
    // Perform login
    const loginResult = await nics24.login();
}
```

## Example Implementation

See `providers/nics24/login.js` and `providers/nics24/checkLogin.js` for complete implementation examples.