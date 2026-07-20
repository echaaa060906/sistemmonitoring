const puppeteer = require('puppeteer');
(async () => {
    try {
        const browser = await puppeteer.launch();
        const page = await browser.newPage();
        const errors = [];
        page.on('pageerror', err => errors.push(err.toString()));
        page.on('console', msg => { if(msg.type() === 'error') errors.push(msg.text()); });
        await page.goto('http://127.0.0.1:8000/map', {waitUntil: 'networkidle0'});
        console.log("ERRORS:", errors);
        await browser.close();
    } catch(err) {
        console.log("PUPPETEER ERROR:", err);
    }
})();
