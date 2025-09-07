import { chromium } from 'playwright';

// Your list of proxies running on the server
const proxies = [
  { server: 'http://127.0.0.1:8001' },
  { server: 'http://127.0.0.1:8002' },
  { server: 'http://127.0.0.1:8003' },
  { server: 'http://127.0.0.1:8004' },
];

(async () => {
  for (const proxyConfig of proxies) {
    console.log(`🚀 Launching browser with proxy: ${proxyConfig.server}`);

    const browser = await chromium.launch({
      proxy: proxyConfig
    });

    const page = await browser.newPage();

    // Go to a new site to check the IP address
    await page.goto('https://api.ipify.org'); // <-- Updated URL
    const ip = await page.textContent('body');
    console.log(`✅ IP for this browser is: ${ip}`);

    await browser.close();
  }
})();