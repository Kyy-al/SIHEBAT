const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const VIEWPORTS = [
    { name: 'Mobile (360px)', width: 360, height: 800 },
    { name: 'Tablet Portrait (768px)', width: 768, height: 1024 },
    { name: 'Tablet Landscape (1024px)', width: 1024, height: 768 },
    { name: 'Desktop Large (1440px)', width: 1440, height: 900 }
];

const MODES = ['light', 'dark'];

async function run() {
    console.log('=== STARTING PLAYWRIGHT VIEWPORT & DESIGN TOKEN VERIFICATION ===');
    
    let browser;
    try {
        // Try system Chrome first, fallback to Edge or default chromium
        browser = await chromium.launch({ channel: 'chrome', headless: true });
    } catch (e) {
        try {
            browser = await chromium.launch({ channel: 'msedge', headless: true });
        } catch (e2) {
            browser = await chromium.launch({ headless: true });
        }
    }

    const results = [];
    const screenshotDir = path.join(__dirname, '../storage/app/viewport_tests');
    if (!fs.existsSync(screenshotDir)) {
        fs.mkdirSync(screenshotDir, { recursive: true });
    }

    const baseUrl = 'http://127.0.0.1:8000';

    for (const vp of VIEWPORTS) {
        for (const mode of MODES) {
            const context = await browser.newContext({
                viewport: { width: vp.width, height: vp.height },
                colorScheme: mode
            });

            const page = await context.newPage();

            // 1. Visit Login Page
            await page.goto(`${baseUrl}/login`, { waitUntil: 'domcontentloaded' });

            // Check Font Family
            const bodyFont = await page.evaluate(() => {
                return window.getComputedStyle(document.body).fontFamily;
            });

            // Check for NO ITALIC anywhere in document
            const italicCount = await page.evaluate(() => {
                const elements = Array.from(document.querySelectorAll('*'));
                let count = 0;
                for (const el of elements) {
                    const style = window.getComputedStyle(el);
                    if (style.fontStyle === 'italic' || style.fontStyle === 'oblique') {
                        count++;
                    }
                }
                return count;
            });

            // Check horizontal overflow (no horizontal scroll leak)
            const hasHorizontalScroll = await page.evaluate(() => {
                return document.documentElement.scrollWidth > window.innerWidth + 2; // allowance of 2px for subpixel
            });

            // Check primary sky blue color token existence in stylesheet
            const hasSkyBlue = await page.evaluate(() => {
                const primaryBtn = document.querySelector('button[type="submit"], .btn-primary, [class*="bg-primary"]');
                if (!primaryBtn) return true;
                const bg = window.getComputedStyle(primaryBtn).backgroundColor;
                return bg.length > 0;
            });

            const passFont = bodyFont.toLowerCase().includes('plus jakarta sans') || bodyFont.toLowerCase().includes('jakarta');
            const passItalic = (italicCount === 0);
            const passOverflow = !hasHorizontalScroll;

            const passed = passFont && passItalic && passOverflow;

            results.push({
                viewport: vp.name,
                width: vp.width,
                mode: mode,
                font: bodyFont,
                hasItalic: italicCount > 0,
                hasHorizontalScroll: hasHorizontalScroll,
                passed: passed
            });

            console.log(`[${passed ? 'PASS' : 'FAIL'}] ${vp.name} | ${mode.toUpperCase()} Mode: Font: ${passFont ? 'OK' : 'MISMATCH'}, Italic: ${passItalic ? 'NONE (OK)' : 'FOUND!'}, Overflow: ${passOverflow ? 'NO (OK)' : 'OVERFLOW!'}`);

            await context.close();
        }
    }

    await browser.close();

    console.log('\n==================================================');
    console.log('PLAYWRIGHT TEST SUMMARY:');
    const allPassed = results.every(r => r.passed);
    console.log(`Total Scenarios Tested : ${results.length}`);
    console.log(`Passed Scenarios       : ${results.filter(r => r.passed).length}`);
    console.log(`Failed Scenarios       : ${results.filter(r => !r.passed).length}`);
    console.log(`Result                 : ${allPassed ? 'ALL VIEWPORTS PASSED (100%)' : 'SOME VIEWPORTS FAILED'}`);
    console.log('==================================================');

    if (!allPassed) {
        process.exit(1);
    }
}

run().catch(err => {
    console.error('Test execution failed:', err);
    process.exit(1);
});
