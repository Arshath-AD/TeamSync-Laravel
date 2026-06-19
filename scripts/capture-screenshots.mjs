#!/usr/bin/env node
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const baseUrl = process.argv[2] || 'http://127.0.0.1:8000';
const outDir = path.join(__dirname, '..', 'screenshots');
const chrome = '/usr/bin/google-chrome';

const pages = [
  { name: '01-dashboard-admin', url: '/dashboard', email: 'admin@teamsync.test' },
  { name: '02-projects', url: '/projects', email: 'admin@teamsync.test' },
  { name: '03-project-workspace', url: '/projects/1?tab=overview', email: 'admin@teamsync.test' },
  { name: '04-members', url: '/dashboard?section=members', email: 'admin@teamsync.test' },
  { name: '05-tasks-board', url: '/tasks', email: 'admin@teamsync.test' },
  { name: '06-user-dashboard', url: '/dashboard', email: 'jordan@teamsync.test' },
  { name: '07-login', url: '/login', email: null, guest: true },
  { name: '08-task-create', url: '/tasks/create', email: 'admin@teamsync.test' },
];

fs.mkdirSync(outDir, { recursive: true });

async function login(page, email) {
  await page.goto(`${baseUrl}/login`, { waitUntil: 'networkidle2', timeout: 30000 });
  const url = page.url();
  if (!url.includes('/login')) return;

  await page.waitForSelector('#email', { timeout: 10000 });
  await page.evaluate((em) => {
    document.querySelector('#email').value = em;
    document.querySelector('#password').value = 'password';
  }, email);

  await Promise.all([
    page.waitForNavigation({ waitUntil: 'networkidle2', timeout: 30000 }),
    page.click('button[type="submit"]'),
  ]);
}

async function main() {
  const puppeteer = await import('puppeteer-core');
  const browser = await puppeteer.default.launch({
    executablePath: chrome,
    headless: 'new',
    args: ['--no-sandbox', '--disable-dev-shm-usage'],
    defaultViewport: { width: 1440, height: 900 },
  });

  const contexts = {};

  for (const spec of pages) {
    if (spec.guest) {
      const page = await browser.newPage();
      await page.goto(`${baseUrl}${spec.url}`, { waitUntil: 'networkidle2', timeout: 30000 });
      await new Promise(r => setTimeout(r, 800));
      const outfile = path.join(outDir, `${spec.name}.png`);
      await page.screenshot({ path: outfile, type: 'png' });
      console.log('✓', outfile);
      await page.close();
      continue;
    }

    if (!contexts[spec.email]) {
      contexts[spec.email] = await browser.createBrowserContext();
      const loginPage = await contexts[spec.email].newPage();
      await login(loginPage, spec.email);
      await loginPage.close();
    }

    const page = await contexts[spec.email].newPage();
    await page.goto(`${baseUrl}${spec.url}`, { waitUntil: 'networkidle2', timeout: 30000 });
    await new Promise(r => setTimeout(r, 1200));
    const outfile = path.join(outDir, `${spec.name}.png`);
    await page.screenshot({ path: outfile, type: 'png' });
    console.log('✓', outfile);
    await page.close();
  }

  await browser.close();
}

main().catch(e => { console.error(e); process.exit(1); });
