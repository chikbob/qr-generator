const fs = require('fs')
const path = require('path')
const puppeteer = require('puppeteer-core')

const ROOT = process.cwd()
const OUT_DIR = path.join(ROOT, 'tmp', 'real_screenshots')
fs.mkdirSync(OUT_DIR, { recursive: true })

const chromePath = '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome'
const baseUrl = 'http://127.0.0.1:8090'

async function wait(ms) {
    return new Promise(resolve => setTimeout(resolve, ms))
}

async function dismissDialogs(page) {
    page.on('dialog', async dialog => {
        await dialog.dismiss()
    })
}

async function login(page, email, password) {
    console.log('open login')
    await page.goto(`${baseUrl}/login`, { waitUntil: 'networkidle2' })
    await page.type('input[type="email"]', email, { delay: 20 })
    await page.type('input[type="password"]', password, { delay: 20 })
    await page.click('button[type="submit"]')
    await page.waitForFunction(() => location.pathname !== '/login', { timeout: 20000 })
    await wait(1200)
    console.log('logged in', await page.url())
}

async function ensureGeneratorData(page) {
    console.log('open generate')
    await page.goto(`${baseUrl}/generate`, { waitUntil: 'networkidle2' })
    await page.waitForSelector('textarea')
    await page.click('textarea', { clickCount: 3 })
    await page.type('textarea', 'https://example.com/qr-demo')
    await wait(500)
    const checkbox = await page.$('input[type="checkbox"]')
    if (checkbox) {
        const checked = await page.$eval('input[type="checkbox"]', el => el.checked)
        if (!checked) {
            await checkbox.click()
            await wait(400)
        }
    }
    const saveButton = await page.$('button.save, button[class*="save"]')
    if (saveButton) {
        console.log('save qr')
        await saveButton.click()
        await page.waitForFunction(() => location.pathname.includes('/history'), { timeout: 20000 })
        await wait(800)
        console.log('saved qr, now back to generate')
        await page.goto(`${baseUrl}/generate`, { waitUntil: 'networkidle2' })
        await page.waitForSelector('textarea')
        await page.click('textarea', { clickCount: 3 })
        await page.type('textarea', 'https://example.com/qr-demo')
        const checkedAgain = await page.$eval('input[type="checkbox"]', el => el.checked)
        if (!checkedAgain) {
            await page.click('input[type="checkbox"]')
        }
        await wait(500)
    }
}

async function save(page, name, selector = 'body') {
    if (selector === '@viewport') {
        console.log('capture viewport', name, await page.url())
        await page.screenshot({
            path: path.join(OUT_DIR, `${name}.png`)
        })
        return
    }
    console.log('capture', name, selector, await page.url())
    const element = await page.$(selector)
    if (!element) {
        throw new Error(`Selector not found for ${name}: ${selector}`)
    }
    await element.screenshot({
        path: path.join(OUT_DIR, `${name}.png`)
    })
}

async function main() {
    const browser = await puppeteer.launch({
        executablePath: chromePath,
        headless: 'new',
        defaultViewport: { width: 1440, height: 1400, deviceScaleFactor: 1.5 },
        args: ['--no-sandbox', '--disable-gpu']
    })

    const page = await browser.newPage()
    await dismissDialogs(page)
    await login(page, 'admin@gmail.com', 'password')
    await ensureGeneratorData(page)
    await save(page, 'qr_generator', '.qr-generator')

    await page.goto(`${baseUrl}/history`, { waitUntil: 'networkidle2' })
    await wait(700)
    await save(page, 'qr_history', '@viewport')

    const analyticsLink = await page.$('button.analytics-btn, a[href*="/analytics"]')
    if (analyticsLink) {
        console.log('open analytics')
        await analyticsLink.click()
        await page.waitForFunction(() => location.pathname.includes('/analytics'), { timeout: 20000 })
        await wait(1200)
        await save(page, 'qr_analytics', '.qr-analytics')
    } else {
        console.log('analytics button not found, open direct analytics route')
        await page.goto(`${baseUrl}/qr/89/analytics`, { waitUntil: 'networkidle2' })
        await wait(1200)
        await save(page, 'qr_analytics', '.qr-analytics')
    }

    await page.goto(`${baseUrl}/scan`, { waitUntil: 'networkidle2' })
    await wait(700)
    await save(page, 'qr_scanner', 'body')

    await page.goto(`${baseUrl}/admin`, { waitUntil: 'networkidle2' })
    await wait(700)
    await save(page, 'admin_dashboard', '@viewport')

    await page.goto(`${baseUrl}/admin/qr_codes`, { waitUntil: 'networkidle2' })
    await wait(700)
    await save(page, 'admin_qr_codes', '@viewport')

    await browser.close()
    console.log(JSON.stringify({
        outDir: OUT_DIR,
        files: fs.readdirSync(OUT_DIR).sort()
    }))
}

main().catch(err => {
    console.error(err)
    process.exit(1)
})
